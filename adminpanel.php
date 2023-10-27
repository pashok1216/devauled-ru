<?php
session_start();
require_once 'config.php';
require_once 'db.php';

// Проверяем, авторизован ли пользователь
if (!isset($_SESSION['auth']) || $_SESSION['auth'] !== true) {
    header("Location: login.php");
    exit();
}

// Проверяем, является ли пользователь администратором

if (!isset($_SESSION['role']) || (
    $_SESSION['role'] !== 'admin' &&
    $_SESSION['role'] !== 'superadmin' &&
    $_SESSION['role'] !== 'ProductManager' &&
    $_SESSION['role'] !== 'OrderManager' &&
    $_SESSION['role'] !== 'SalesManager' &&
    $_SESSION['role'] !== 'SuperAdmin'
)) {
    echo "У вас нет доступа к админ-панели!";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ панель</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f5f5f5;
        }

        h1 {
            text-align: center;
            margin: 50px 0;
            color: #333;
        }

        .admin-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px;
        }

        .admin-option {
            background-color: #fff;
            border-radius: 25px;
            padding: 20px;
            margin-bottom: 20px;
            width: 300px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s;
            border: 2px solid #333;  /* Добавил эту строку */
        }

        .admin-option:hover {
            background-color: #f8f9fa;
        }

        .admin-option a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            font-size: 18px;
        }
    </style>
</head>
<body>
<h1>Админ панель</h1>
<div class="admin-container">

    <?php if ($_SESSION['auth'] && ($_SESSION['role'] === 'SalesManager' || $_SESSION['role'] === 'SalesManager')) { ?>
        <div class="admin-option">
            <a href="statistics.php">Статистика</a>
        </div>
    <?php } ?>

    <?php if ($_SESSION['auth'] && ($_SESSION['role'] === 'ProductManager' || $_SESSION['role'] === 'ProductManager')) { ?>
    <div class="admin-option">
        <a href="add_product.php">Добавить товары</a>
    </div>
    <div class="admin-option">
        <a href="delete_product.php">Редактировать товары</a>
    </div>
    <?php } ?>

    <?php if ($_SESSION['auth'] && ($_SESSION['role'] === 'OrderManager' || $_SESSION['role'] === 'OrderManager')) { ?>
    <div class="admin-option">
        <a href="orders.php">Заказы</a>
    </div>
    <?php } ?>

    <?php if ($_SESSION['auth'] && ($_SESSION['role'] === 'SuperAdmin' || $_SESSION['role'] === 'SuperAdmin')) { ?>
        <div class="admin-option">
            <a href="statistics.php">Статистика</a>
        </div>
        <div class="admin-option">
            <a href="add_product.php">Добавить товары</a>
        </div>
        <div class="admin-option">
            <a href="delete_product.php">Редактировать товары</a>
        </div>
        <div class="admin-option">
            <a href="orders.php">Заказы</a>
        </div>
        <div class="admin-option">
            <a href="admin_actions.php">Редактирование админов</a>
        </div>
        <div class="admin-option">
            <a href="help_admin.php">Поддержка пользователей</a>
        </div>
    <?php } ?>
    <div class="admin-option">
        <a href="index.php">Выйти</a>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>