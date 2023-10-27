<?php
header('Location: /index.php');
require_once 'config.php';
require_once 'index.php';
session_start();

// Очистка сессии
session_unset();
session_destroy();

// Перенаправление на страницу авторизации
exit();
?>
