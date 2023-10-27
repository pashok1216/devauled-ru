<?php
session_start(); // начинаем сессию
require_once 'loginmenu.php';
require_once 'registermenu.php'
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link type="image/png" sizes="16x16" rel="icon" href="images/favicon-16x16.png">
    <link type="image/png" sizes="32x32" rel="icon" href="images/favicon-32x32.png">
    <link type="image/png" sizes="96x96" rel="icon" href="images/favicon-96x96.png">
    <link type="image/png" sizes="120x120" rel="icon" href="images/favicon-120x120.png">
    <link type="image/x-icon" rel="shortcut icon" href="images/favicon.ico">
    <link rel="stylesheet" type="text/css" href="css/head.css">
    <title>Devauled</title>
</head>
<body>

<a href="/index.php" class="logo-link">
    <img src="images/devalued.png" alt="Логотип" class="logo-img">
</a>
<header>
    <div id="burger-menu" class="burger-menu">&#9776;</div>
    <div class="menu-items">
        <ul class="button-container">
            <?php if (!isset($_SESSION['user_id'])): // Если пользователь не авторизован ?>
                <li class="button-item">
                    <a class="login-button" href="#" data-toggle="modal" data-target="#loginModal">Войти</a>
                </li>
                <li class="button-item">
                    <a class="register-button" href="#" data-toggle="modal" data-target="#registerModal">Регистрация</a>
                </li>
            <?php else: // Если пользователь авторизован ?>
                <?php if (
                    $_SESSION['role'] === 'admin' ||
                    $_SESSION['role'] === 'superadmin' ||
                    $_SESSION['role'] === 'ProductManager' ||
                    $_SESSION['role'] === 'OrderManager' ||
                    $_SESSION['role'] === 'SalesManager' ||
                    $_SESSION['role'] === 'SuperAdmin'
                ): ?>
                    <li class="button-item">
                        <a class="admin-panel-button" href="/adminpanel.php">Админ Панель</a>
                    </li>
                <?php endif; ?>
                <li class="button-item">
                    <a class="lkusers" href="/lkusers.php">Личный кабинет</a>
                </li>
                <li class="button-item">
                    <a class="cart-button" href="/cart">
                        <img src="images/cart.png" alt="Cart Icon" class="cart-icon">
                        <span id="cartItemCount"></span>
                    </a>
                </li>
                <li class="button-item">
                    <a class="logout-button" href="/logout.php">Выйти</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const burgerMenu = document.getElementById('burger-menu');
        const menuItems = document.querySelector('.menu-items');

        burgerMenu.addEventListener('click', function() {
            menuItems.classList.toggle('open');
        });
    });
</script>
</body>
</html>