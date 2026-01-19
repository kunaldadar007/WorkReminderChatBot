<?php
// chatbot/save_chat.php
// Save a chat message (user or bot) to chatbot_history.

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid method']);
    exit;
}

$sender = $_POST['sender'] ?? '';
$message = trim($_POST['message'] ?? '');

if (!in_array($sender, ['user', 'bot'], true) || $message === '') {
    echo json_encode(['error' => 'Invalid payload']);
    exit;
}

$stmt = $pdo->prepare('INSERT INTO chatbot_history (user_id, sender, message, created_at) VALUES (?, ?, ?, NOW())');
$stmt->execute([$_SESSION['user_id'], $sender, $message]);

echo json_encode(['success' => true]);

