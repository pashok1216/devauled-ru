<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User ID is not set']);
    exit;
}

$user_id = $_SESSION['user_id'];

$db = new UniqueDatabase();
$pdo = $db->connect();

// Получение chat_id для текущего пользователя и администратора
$stmt = $pdo->prepare('SELECT chat_id, users.First_name, users.Last_name FROM chat_messages JOIN users ON chat_messages.sender_id = users.user_id WHERE sender_id = :user_id OR receiver_id = :user_id LIMIT 1');
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$chatInfo = $stmt->fetch();

if (!$chatInfo) {
    echo json_encode(['status' => 'error', 'message' => 'Chat not found']);
    exit;
}

$chat_id = $chatInfo['chat_id'];

// Получение всех сообщений для данного chat_id
$stmt = $pdo->prepare('SELECT * FROM chat_messages WHERE chat_id = :chat_id ORDER BY timestamp DESC');
$stmt->bindParam(':chat_id', $chat_id, PDO::PARAM_STR);
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($messages);
?>
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>