<?php
// chatbot/get_history.php
// Returns recent chat history for the logged-in user.

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

$stmt = $pdo->prepare('
    SELECT sender, message, created_at
    FROM chatbot_history
    WHERE user_id = ?
    ORDER BY created_at ASC
    LIMIT 100
');
$stmt->execute([$_SESSION['user_id']]);
$history = $stmt->fetchAll();

echo json_encode(['history' => $history]);

