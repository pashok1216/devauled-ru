<?php
// fetch_chat_ids.php
header('Content-Type: application/json');

// Здесь ваш код для подключения к базе данных
require_once 'db.php';
$db = new UniqueDatabase();
$pdo = $db->connect();

// Запрос к базе данных для получения списка chat_id
$query = "SELECT DISTINCT chat_id FROM chat_messages"; // Измените этот SQL-запрос в соответствии с вашей структурой БД
$result = $pdo->query($query);

$chat_ids = array();
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $chat_ids[] = $row;
}

echo json_encode($chat_ids);
?>