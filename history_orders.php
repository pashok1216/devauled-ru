<?php
// Инициализация сессии и подключение к базе данных
session_start();
require_once 'db.php';

// Проверка на авторизацию пользователя
if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$db = new UniqueDatabase();
$pdo = $db->connect();
$user_id = $_SESSION['user_id'];

// Получение заказов пользователя
$stmt = $pdo->prepare('SELECT * FROM order_history WHERE user_id = :user_id');
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$orders = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Личный кабинет</title>
    <link rel="stylesheet" href="css/lkusers.css">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="sweetalert2-11.7.29/sweetalert2.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script src="sweetalert2-11.7.29/sweetalert2.all.min.js"></script>
</head>
<body>
<div id="orders">
    <h2>История Заказов</h2>
    <ul>
        <?php foreach($orders as $order): ?>
            <li>Заказ №<?= htmlspecialchars($order['order_number']) ?> (Статус: <?= htmlspecialchars($order['status']) ?>)</li>
        <?php endforeach; ?>
    </ul>
</div>
</div>
</body>
</html>