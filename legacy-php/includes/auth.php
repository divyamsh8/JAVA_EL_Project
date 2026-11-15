<?php
/**
 * Authentication & Rate Limiting Module
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/csrf.php';

function check_session_timeout(): void {
    if (isset($_SESSION['user_id'])) {
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT_SECONDS)) {
            // Session has expired
            session_unset();
            session_destroy();
            header('Location: ' . BASE_URL . '/index.php?timeout=1');
            exit;
        }
        $_SESSION['last_activity'] = time();
    }
}

// Call session check on every page load
check_session_timeout();

function is_logged_in(): bool {
    return !empty($_SESSION['user_id']);
}

function require_login(): void {
    if (!is_logged_in()) {
        header('Location: ' . BASE_URL . '/index.php?auth_required=1');
        exit;
    }
}

function require_admin(): void {
    require_login();
    $roleId = (int)($_SESSION['role_id'] ?? 0);
    // 1: super_admin, 2: department_admin, 3: faculty
    if (!in_array($roleId, [1, 2, 3], true)) {
        header('Location: ' . BASE_URL . '/dashboard.php?error=unauthorized');
        exit;
    }
}

function current_user(): ?array {
    if (!is_logged_in()) {
        return null;
    }
    static $currentUser = null;
    if ($currentUser !== null) {
        return $currentUser;
    }

    $db = get_db();
    $userId = (int)$_SESSION['user_id'];
    
    // Check if student/parent or admin
    $stmt = $db->prepare("
        SELECT u.id as user_id, u.role_id, r.name as role_name, u.username, u.email as user_email, u.phone as user_phone,
               s.id as student_id, s.usn, s.roll_no, s.first_name, s.last_name, s.gender, s.dob, s.blood_group,
               s.admission_year, s.current_sem, s.photo_url,
               d.id as dept_id, d.code as dept_code, d.name as dept_name,
               c.id as course_id, c.code as course_code, c.name as course_name,
               p.id as parent_id, p.father_name, p.mother_name, p.primary_mobile, p.alternate_mobile, p.address, p.occupation
        FROM users u
        JOIN roles r ON u.role_id = r.id
        LEFT JOIN students s ON s.user_id = u.id
        LEFT JOIN departments d ON s.department_id = d.id
        LEFT JOIN courses c ON s.course_id = c.id
        LEFT JOIN parents p ON s.parent_id = p.id
        WHERE u.id = ?
        LIMIT 1
    ");
    $stmt->execute([$userId]);
    $currentUser = $stmt->fetch() ?: null;
    return $currentUser;
}

/**
 * Rate Limiting Functions
 */
function get_recent_failed_attempts(string $ip, string $username): int {
    $db = get_db();
    $windowStart = time() - (LOGIN_LOCKOUT_MINUTES * 60);

    $stmt = $db->prepare("
        SELECT COUNT(*) 
        FROM login_attempts 
        WHERE ip_address = ? AND username = ? AND attempt_time >= ?
    ");
    $stmt->execute([$ip, $username, $windowStart]);
    return (int)$stmt->fetchColumn();
}

function get_lockout_remaining_seconds(string $ip, string $username): int {
    $db = get_db();
    $windowStart = time() - (LOGIN_LOCKOUT_MINUTES * 60);

    $stmt = $db->prepare("
        SELECT MIN(attempt_time) 
        FROM login_attempts 
        WHERE ip_address = ? AND username = ? AND attempt_time >= ?
    ");
    $stmt->execute([$ip, $username, $windowStart]);
    $oldestAttempt = $stmt->fetchColumn();

    if ($oldestAttempt) {
        $lockExpires = (int)$oldestAttempt + (LOGIN_LOCKOUT_MINUTES * 60);
        return max(0, $lockExpires - time());
    }
    return 0;
}

function record_failed_attempt(string $ip, string $username, ?string $reason = 'Invalid credentials'): int {
    $db = get_db();
    $now = time();

    // 1. Record in login_attempts
    $stmt = $db->prepare("INSERT INTO login_attempts (ip_address, username, attempt_time) VALUES (?, ?, ?)");
    $stmt->execute([$ip, $username, $now]);

    // 2. Audit log
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    $logStmt = $db->prepare("INSERT INTO login_logs (username, ip_address, user_agent, status, failure_reason) VALUES (?, ?, ?, 'FAILED', ?)");
    $logStmt->execute([$username, $ip, $userAgent, $reason]);

    // Cleanup attempts older than 24 hours
    $cleanupTime = $now - 86400;
    $db->prepare("DELETE FROM login_attempts WHERE attempt_time < ?")->execute([$cleanupTime]);

    $failedCount = get_recent_failed_attempts($ip, $username);
    return max(0, MAX_LOGIN_ATTEMPTS - $failedCount);
}

function clear_login_attempts(string $ip, string $username): void {
    $db = get_db();
    $stmt = $db->prepare("DELETE FROM login_attempts WHERE ip_address = ? AND username = ?");
    $stmt->execute([$ip, $username]);
}

function log_successful_login(string $username, string $ip): void {
    $db = get_db();
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    $stmt = $db->prepare("INSERT INTO login_logs (username, ip_address, user_agent, status) VALUES (?, ?, ?, 'SUCCESS')");
    $stmt->execute([$username, $ip, $userAgent]);

    $updateStmt = $db->prepare("UPDATE users SET last_login_at = CURRENT_TIMESTAMP WHERE username = ?");
    $updateStmt->execute([$username]);
}

function log_lockout_event(string $username, string $ip): void {
    $db = get_db();
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    $stmt = $db->prepare("INSERT INTO login_logs (username, ip_address, user_agent, status, failure_reason) VALUES (?, ?, ?, 'LOCKED', 'Max attempts exceeded')");
    $stmt->execute([$username, $ip, $userAgent]);
}
