<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
if (!isset($_SESSION['chat_session_id'])) {
    $_SESSION['chat_session_id'] = uniqid();  // создает и сохраняет chat_session_id, если его нет
}
// Debug: Display Session Variables
var_dump($_SESSION);

require_once 'db.php';
echo "Debug: Set receiver_id_admin to " . $_SESSION['receiver_id_admin'];

$chat_id = $_POST['chat_id'] ?? null;
if ($chat_id === null) {
    echo json_encode(['status' => 'error', 'message' => 'Chat ID is not set']);
    exit();
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User ID is not set']);
    exit();
}

$db = new UniqueDatabase();
$pdo = $db->connect();

// Debug: Database Connection
if (!$pdo) {
    die("Could not connect to database");
}

// Fetch receiver_id from the database based on chat_id
$stmt = $pdo->prepare('SELECT receiver_id FROM chat_messages WHERE chat_id = :chat_id LIMIT 1');
$stmt->bindParam(':chat_id', $chat_id, PDO::PARAM_STR);
$stmt->execute();
$chat_info = $stmt->fetch();

if (!$chat_info) {
    echo json_encode(['status' => 'error', 'message' => 'Chat info not found']);
    exit();
}

$receiver_id = $chat_info['receiver_id'];  // Now the receiver_id should be the user's ID

$message = $_POST['message'];

// Debug: Display Message
var_dump($message);

$user_id = $_SESSION['user_id'];
$timestamp = date('Y-m-d H:i:s');

try {
    $stmt = $pdo->prepare('INSERT INTO chat_messages (chat_id, session_id, sender_id, receiver_id, message_text, timestamp, user_id) VALUES (:chat_id, :session_id, :sender_id, :receiver_id, :message_text, :timestamp, :user_id)');
    $stmt->bindParam(':chat_id', $chat_id, PDO::PARAM_STR);
    $stmt->bindParam(':session_id', $_SESSION['chat_session_id'], PDO::PARAM_STR);
    $stmt->bindParam(':sender_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':receiver_id', $receiver_id, PDO::PARAM_INT);  // use the fetched receiver_id
    $stmt->bindParam(':message_text', $message, PDO::PARAM_STR);
    $stmt->bindParam(':timestamp', $timestamp, PDO::PARAM_STR);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    // Debug: SQL Error Check
    if ($stmt->errorCode() != '00000') {
        die("Error: " . json_encode($stmt->errorInfo()));
    }

    echo json_encode(['status' => 'success']);
} catch (PDOException $e) {
    error_log($e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>