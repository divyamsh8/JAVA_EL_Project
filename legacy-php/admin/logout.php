<?php
/**
 * Admin Logout Handler
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/app.php';

unset(
    $_SESSION['admin_user_id'],
    $_SESSION['admin_role_id'],
    $_SESSION['admin_role_name'],
    $_SESSION['admin_username'],
    $_SESSION['admin_last_activity']
);

header('Location: ' . BASE_URL . '/admin/login.php?logged_out=1');
exit;
