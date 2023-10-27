<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo 'unauthorized';
    exit;
}

// Получение данных из POST-запроса
$card_number = $_POST['card_number'];
$expiry_date = $_POST['expiry_date'];
$cardholder_name = $_POST['cardholder_name'];


$db = new UniqueDatabase();
$pdo = $db->connect();

$user_id = $_SESSION['user_id'];

// Подготовка и выполнение SQL-запроса
$stmt = $pdo->prepare('INSERT INTO payment_methods (user_id, card_number, expiry_date, cardholder_name) VALUES (:user_id, :card_number, :expiry_date, :cardholder_name)');
$stmt->bindParam(':user_id', $user_id);
$stmt->bindParam(':card_number', $card_number);
$stmt->bindParam(':expiry_date', $expiry_date);
$stmt->bindParam(':cardholder_name', $cardholder_name);

if ($stmt->execute()) {
    echo 'success';
} else {
    echo 'error';
}
?>