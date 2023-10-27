<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $confirmationCode = $_POST['confirmationCode'];
    $newPassword = $_POST['newPassword'];
    $email = $_SESSION['email'];

    if ($confirmationCode == $_SESSION['confirmation_code']) {
        try {
            $db = new Database();
            $pdo = $db->connect();

            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare('UPDATE users SET password = :password WHERE email = :email');
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            echo 'success';
        } catch (PDOException $e) {
            echo "Database error: " . $e->getMessage(); // Выводим сообщение об ошибке базы данных
        }
    } else {
        echo "Received: $confirmationCode, Expected: {$_SESSION['confirmation_code']}"; // Выводим полученный и ожидаемый коды
    }
}
?>