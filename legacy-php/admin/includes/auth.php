<?php
/**
 * Admin Panel Authentication & RBAC Guard
 */

declare(strict_types=1);

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../includes/csrf.php';

function is_admin_logged_in(): bool {
    return !empty($_SESSION['admin_user_id']) && in_array((int)($_SESSION['admin_role_id'] ?? 0), [1, 2, 3], true);
}

function require_admin_auth(): void {
    if (!is_admin_logged_in()) {
        header('Location: ' . BASE_URL . '/admin/login.php?auth_required=1');
        exit;
    }
}

function current_admin(): ?array {
    if (!is_admin_logged_in()) {
        return null;
    }
    static $adminUser = null;
    if ($adminUser !== null) {
        return $adminUser;
    }
    $db = get_db();
    $stmt = $db->prepare("
        SELECT u.id, u.username, u.email, u.phone, u.role_id, r.name as role_name
        FROM users u
        JOIN roles r ON u.role_id = r.id
        WHERE u.id = ?
        LIMIT 1
    ");
    $stmt->execute([(int)$_SESSION['admin_user_id']]);
    $adminUser = $stmt->fetch() ?: null;
    return $adminUser;
}

function has_admin_role(array $allowedRoles): bool {
    $admin = current_admin();
    if (!$admin) return false;
    return in_array($admin['role_name'], $allowedRoles, true);
}
