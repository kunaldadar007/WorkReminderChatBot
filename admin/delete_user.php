<?php
// admin/delete_user.php
// Delete a non-admin user and cascade their tasks/history (via FK).

require_once __DIR__ . '/../includes/admin_check.php';
require_once __DIR__ . '/../config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    // Prevent deleting admins for safety.
    $stmt = $pdo->prepare('SELECT role FROM users WHERE id = ?');
    $stmt->execute([$id]);
    $role = $stmt->fetchColumn();
    if ($role && $role !== 'admin') {
        $del = $pdo->prepare('DELETE FROM users WHERE id = ?');
        $del->execute([$id]);
    }
}

header('Location: /WorkReminder/admin/index.php');
exit;

