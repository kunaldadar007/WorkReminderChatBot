<?php
// tasks/delete_task.php
// Deletes a task owned by the current user.

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config.php';

$userId = $_SESSION['user_id'];
$taskId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($taskId > 0) {
    $stmt = $pdo->prepare('DELETE FROM tasks WHERE id = ? AND user_id = ?');
    $stmt->execute([$taskId, $userId]);
}

header('Location: /WorkReminder/dashboard.php');
exit;

