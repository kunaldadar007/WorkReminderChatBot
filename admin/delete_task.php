<?php
// admin/delete_task.php
// Delete any task (admin override).

require_once __DIR__ . '/../includes/admin_check.php';
require_once __DIR__ . '/../config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $stmt = $pdo->prepare('DELETE FROM tasks WHERE id = ?');
    $stmt->execute([$id]);
}

header('Location: /WorkReminder/admin/index.php');
exit;

