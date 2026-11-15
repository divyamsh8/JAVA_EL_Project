<?php
/**
 * API - Attendance Data JSON Endpoint
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

if (!is_logged_in()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user = current_user();
$studentId = (int)($user['student_id'] ?? 0);
$activeTerm = get_active_term();
$termId = (int)$activeTerm['id'];

$db = get_db();
$stmt = $db->prepare("
    SELECT a.*, s.code, s.title 
    FROM attendance a 
    JOIN subjects s ON a.subject_id = s.id 
    WHERE a.student_id = ? AND a.term_id = ?
");
$stmt->execute([$studentId, $termId]);
$rows = $stmt->fetchAll();

echo json_encode(['success' => true, 'data' => $rows]);
