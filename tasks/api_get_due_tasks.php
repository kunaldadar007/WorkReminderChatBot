<?php
// tasks/api_get_due_tasks.php
// Returns tasks that are due now (or past) and not yet reminded.
// Also marks them as reminder_sent = 1 to avoid duplicates.

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

$userId = $_SESSION['user_id'];

// Current server date/time.
$nowDate = date('Y-m-d');
$nowTime = date('H:i:s');

// Fetch tasks that are pending, not yet reminded, and due time <= now.
$stmt = $pdo->prepare('
    SELECT id, title, description, due_date, due_time, priority
    FROM tasks
    WHERE user_id = :user_id
      AND status = "pending"
      AND reminder_sent = 0
      AND (
            due_date < :today
            OR (due_date = :today AND due_time <= :nowtime)
          )
');
$stmt->execute([
    ':user_id' => $userId,
    ':today'   => $nowDate,
    ':nowtime' => $nowTime
]);
$dueTasks = $stmt->fetchAll();

// Mark them as reminded to prevent repeated notifications.
if (!empty($dueTasks)) {
    $ids = array_column($dueTasks, 'id');
    $inPlaceholders = implode(',', array_fill(0, count($ids), '?'));
    $updateSql = "UPDATE tasks SET reminder_sent = 1, updated_at = NOW() WHERE id IN ($inPlaceholders) AND user_id = ?";
    $stmtUpdate = $pdo->prepare($updateSql);
    $stmtUpdate->execute([...$ids, $userId]);
}

echo json_encode([
    'tasks' => $dueTasks,
    'server_time' => $nowDate . ' ' . $nowTime
]);

