<?php
session_start();
require_once 'db.php';

$db = new UniqueDatabase();
$pdo = $db->connect();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User ID is not set']);
    exit();
}

if (!isset($_GET['chat_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Chat ID is not set']);
    exit();
}

$chat_id = $_GET['chat_id'];



$stmt = $pdo->prepare('SELECT chat_messages.*, users.First_name, users.Last_name FROM chat_messages JOIN users ON chat_messages.sender_id = users.user_id WHERE chat_id = :chat_id ORDER BY timestamp DESC');
$stmt->bindParam(':chat_id', $chat_id, PDO::PARAM_STR);

if ($stmt === false) {
    // Обработка ошибки
    echo json_encode(['status' => 'error', 'message' => 'Could not prepare SQL statement']);
    exit();
}

// Выполнение запроса
$stmt->execute();

// Проверка на ошибки базы данных
if ($stmt->errorCode() != '00000') {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . json_encode($stmt->errorInfo())]);
    exit();
}

// Получение сообщений
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($messages);
?>