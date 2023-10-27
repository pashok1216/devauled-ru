<?php
session_start();
require_once 'db.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User ID is not set']);
    exit();
}

$db = new UniqueDatabase();
$pdo = $db->connect();
// Получение receiver_id из базы данных (например, администратора)
$stmt = $pdo->prepare('SELECT user_id FROM users WHERE role = :role');
$stmt->execute([':role' => 'SuperAdmin']);
$admin = $stmt->fetch();
if ($admin) {
    $receiver_id = $admin['user_id'];
} else {
    echo json_encode(['status' => 'error', 'message' => 'Admin not found']);
    exit();
}
$user_id = $_SESSION['user_id'];
// Проверьте, существует ли chat_id для данного пользователя в базе данных
$stmt = $pdo->prepare('SELECT chat_id FROM chat_messages WHERE sender_id = :user_id OR receiver_id = :user_id LIMIT 1');
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$chatInfo = $stmt->fetch();

if (!$chatInfo) {
    // Если chat_id не существует, генерируем его и сохраняем в базе данных
    $chat_id = md5($user_id . $receiver_id);  // Используйте комбинацию sender_id и receiver_id для создания уникального chat_id
    $stmt = $pdo->prepare('INSERT INTO chats (chat_id, sender_id, receiver_id) VALUES (:chat_id, :sender_id, :receiver_id)');
    $stmt->bindParam(':chat_id', $chat_id, PDO::PARAM_STR);
    $stmt->bindParam(':sender_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':receiver_id', $receiver_id, PDO::PARAM_INT);
    $stmt->execute();
} else {
    // Если chat_id уже существует, получаем его из базы данных
    $chat_id = $chatInfo['chat_id'];
}

$message = $_POST['message'];
$sender_id = $_SESSION['user_id'];


$timestamp = date('Y-m-d H:i:s');

try {
    $stmt = $pdo->prepare('INSERT INTO chat_messages (chat_id, session_id, sender_id, receiver_id, message_text, timestamp) VALUES (:chat_id, :session_id, :sender_id, :receiver_id, :message_text, :timestamp)');
    $stmt->bindParam(':chat_id', $chat_id, PDO::PARAM_STR);
    $stmt->bindParam(':session_id', session_id(), PDO::PARAM_STR);  // Используйте функцию session_id() для уникального session_id
    $stmt->bindParam(':sender_id', $sender_id, PDO::PARAM_INT);
    $stmt->bindParam(':receiver_id', $receiver_id, PDO::PARAM_INT);
    $stmt->bindParam(':message_text', $message, PDO::PARAM_STR);
    $stmt->bindParam(':timestamp', $timestamp, PDO::PARAM_STR);
    $stmt->execute();

    echo json_encode(['status' => 'success']);
} catch (PDOException $e) {
    error_log($e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>