<?php
/**
 * Login Form Handler
 * Validates CSRF, USN, DOB dropdowns, rate limits, and issues secure session
 */

declare(strict_types=1);

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/auth.php';

// Only handle POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

$isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') 
          || (isset($_SERVER['CONTENT_TYPE']) && str_contains($_SERVER['CONTENT_TYPE'], 'application/json'));

$ip = get_client_ip();
$csrfToken = $_POST['csrf_token'] ?? '';
$username = strtoupper(sanitize_input($_POST['username'] ?? ''));
$dd = sanitize_input($_POST['dd'] ?? '');
$mm = sanitize_input($_POST['mm'] ?? '');
$yyyy = sanitize_input($_POST['yyyy'] ?? '');
$termCode = sanitize_input($_POST['term'] ?? '');

// 1. Verify CSRF
if (!verify_csrf_token($csrfToken)) {
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Security token invalid or expired. Please refresh the page.']);
        exit;
    }
    flash('login_error', 'Security token invalid or expired. Please refresh the page.');
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

// 2. Validate Required Inputs
if (empty($username) || empty($dd) || empty($mm) || empty($yyyy)) {
    $msg = 'Please enter your USN and complete date of birth.';
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => $msg]);
        exit;
    }
    flash('login_error', $msg);
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

// 3. Check for Lockout
$recentFailures = get_recent_failed_attempts($ip, $username);
if ($recentFailures >= MAX_LOGIN_ATTEMPTS) {
    $remainingSeconds = get_lockout_remaining_seconds($ip, $username);
    $mins = ceil($remainingSeconds / 60);
    $lockMsg = "Account temporarily locked due to 5 consecutive failed attempts. Please retry in {$mins} minute(s).";
    
    log_lockout_event($username, $ip);

    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'locked' => true,
            'retry_seconds' => $remainingSeconds,
            'attempts_remaining' => 0,
            'error' => $lockMsg
        ]);
        exit;
    }

    $_SESSION['modal_error'] = [
        'message' => 'You have entered an invalid username or password.',
        'is_locked' => true,
        'retry_seconds' => $remainingSeconds,
        'attempts_remaining' => 0
    ];
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

// 4. Validate Date Correctness (e.g., rejecting 31 Feb, 30 Feb)
$dayInt = (int)$dd;
$monInt = (int)$mm;
$yearInt = (int)$yyyy;

if (!checkdate($monInt, $dayInt, $yearInt)) {
    $rem = record_failed_attempt($ip, $username, 'Impossible date submitted');
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'attempts_remaining' => $rem,
            'error' => 'Invalid calendar date selected.'
        ]);
        exit;
    }

    $_SESSION['modal_error'] = [
        'message' => 'You have entered an invalid username or password.',
        'attempts_remaining' => $rem,
        'is_locked' => ($rem <= 0),
        'retry_seconds' => ($rem <= 0 ? (LOGIN_LOCKOUT_MINUTES * 60) : 0)
    ];
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

$normalizedDob = sprintf('%04d-%02d-%02d', $yearInt, $monInt, $dayInt);

// 5. Query Database for User
$db = get_db();
$stmt = $db->prepare("
    SELECT u.id, u.role_id, u.username, u.password_hash, u.status, s.id as student_id
    FROM users u
    LEFT JOIN students s ON s.user_id = u.id
    WHERE u.username = ?
    LIMIT 1
");
$stmt->execute([$username]);
$user = $stmt->fetch();

// 6. Verify Credentials
if ($user && ($user['status'] === 'ACTIVE') && password_verify($normalizedDob, $user['password_hash'])) {
    // SUCCESSFUL AUTHENTICATION
    clear_login_attempts($ip, $username);
    log_successful_login($username, $ip);

    // Regenerate session ID to prevent session fixation
    session_regenerate_id(true);

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role_id'] = $user['role_id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['student_id'] = $user['student_id'];
    $_SESSION['last_activity'] = time();

    // Determine scoped term
    if (!empty($termCode)) {
        $termStmt = $db->prepare("SELECT id, code, name FROM terms WHERE code = ? LIMIT 1");
        $termStmt->execute([$termCode]);
        $t = $termStmt->fetch();
        if ($t) {
            $_SESSION['term_id'] = $t['id'];
            $_SESSION['term_code'] = $t['code'];
            $_SESSION['term_name'] = $t['name'];
        }
    }

    if (empty($_SESSION['term_id'])) {
        $activeTerm = get_active_term();
        $_SESSION['term_id'] = $activeTerm['id'];
        $_SESSION['term_code'] = $activeTerm['code'];
        $_SESSION['term_name'] = $activeTerm['name'];
    }

    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'redirect' => BASE_URL . '/dashboard.php']);
        exit;
    }

    header('Location: ' . BASE_URL . '/dashboard.php');
    exit;
} else {
    // FAILED AUTHENTICATION
    $remainingAttempts = record_failed_attempt($ip, $username, 'Invalid DOB or USN mismatch');
    $isNowLocked = ($remainingAttempts <= 0);
    $retrySecs = $isNowLocked ? (LOGIN_LOCKOUT_MINUTES * 60) : 0;

    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'attempts_remaining' => $remainingAttempts,
            'locked' => $isNowLocked,
            'retry_seconds' => $retrySecs,
            'error' => 'You have entered an invalid username or password.'
        ]);
        exit;
    }

    $_SESSION['modal_error'] = [
        'message' => 'You have entered an invalid username or password.',
        'attempts_remaining' => $remainingAttempts,
        'is_locked' => $isNowLocked,
        'retry_seconds' => $retrySecs
    ];
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}
