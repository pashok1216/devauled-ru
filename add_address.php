<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo 'unauthorized';
    exit;
}

$db = new UniqueDatabase();
$pdo = $db->connect();

$user_id = $_SESSION['user_id'];
$address = $_POST['address'];

$stmt = $pdo->prepare('INSERT INTO delivery_addresses (user_id, address) VALUES (:user_id, :address)');
$stmt->bindParam(':user_id', $user_id);
$stmt->bindParam(':address', $address);

if ($stmt->execute()) {
    echo 'success';
} else {
    echo 'error';
}
?>