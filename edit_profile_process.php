<?php
session_start();
require_once 'db.php';

if(!isset($_SESSION['user_id'])) {
    echo 'unauthorized';
    exit;
}

$db = new UniqueDatabase();
$pdo = $db->connect();

$user_id = $_SESSION['user_id'];

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    $stmt = $pdo->prepare('UPDATE users SET email = :email, phone = :phone WHERE user_id = :user_id');
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':user_id', $user_id);

    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error';
    }
}
?>