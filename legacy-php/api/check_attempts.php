<?php
/**
 * API - Check Remaining Login Attempts
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

$username = strtoupper(sanitize_input($_GET['username'] ?? ''));
$ip = get_client_ip();

if (empty($username)) {
    echo json_encode(['attempts_remaining' => MAX_LOGIN_ATTEMPTS, 'is_locked' => false]);
    exit;
}

$failedCount = get_recent_failed_attempts($ip, $username);
$remaining = max(0, MAX_LOGIN_ATTEMPTS - $failedCount);
$isLocked = ($failedCount >= MAX_LOGIN_ATTEMPTS);
$retrySecs = $isLocked ? get_lockout_remaining_seconds($ip, $username) : 0;

echo json_encode([
    'attempts_remaining' => $remaining,
    'is_locked' => $isLocked,
    'retry_seconds' => $retrySecs
]);
