<?php
// notifications/log_notification.php
// Simple helper endpoint to log notification attempts.
// Can be called via POST with: task_id, channel, message, status

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid method']);
    exit;
}

$taskId = isset($_POST['task_id']) ? (int)$_POST['task_id'] : null;
$channel = $_POST['channel'] ?? 'web';
$message = $_POST['message'] ?? '';
$status = $_POST['status'] ?? 'sent';

$stmt = $pdo->prepare('
    INSERT INTO notifications_log (user_id, task_id, channel, message, status, created_at)
    VALUES (?, ?, ?, ?, ?, NOW())
');
$stmt->execute([$userId, $taskId, $channel, $message, $status]);

echo json_encode(['success' => true]);

