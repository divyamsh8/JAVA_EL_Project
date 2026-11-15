<?php
/**
 * Secure Logout Handler
 */

declare(strict_types=1);

require_once __DIR__ . '/config/app.php';

if (session_status() === PHP_SESSION_ACTIVE) {
    // Unset all session values
    $_SESSION = [];

    // Delete session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    session_destroy();
}

header('Location: ' . BASE_URL . '/index.php?logged_out=1');
exit;
