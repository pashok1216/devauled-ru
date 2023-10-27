<?php
require_once 'db.php'; // Или другой путь, если файл находится в другом месте

$db = new UniqueDatabase();
$conn = $db->connect();

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование товаров</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        h3 {
            background-color: #333;
            color: white;
            padding: 10px;
            margin-top: 0; /* Добавляем отступ сверху */
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            margin: 10px 0;
        }

        a {
            display: inline-block;
            background-color: #555; /* Серый цвет кнопки */
            color: white; /* Белый цвет текста */
            padding: 10px 20px;
            text-decoration: none !important; /* Убираем подчеркивание и делаем важным, чтобы переопределить стандартные стили */
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        a:hover {
            background-color: #333; /* Темно-серый цвет при наведении */
            color: white; /* Белый цвет текста при наведении */
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .logout {
            text-align: right;
            margin-bottom: 20px;
        }
        /* Кнопка "Вернуться в админ панель" */
        a.return-admin-panel,
            /* Кнопка "Вернуться к выбору редактируемого товара" */
        a.return-edit-product {
            display: inline-block;
            background-color: #555; /* Серый цвет кнопки */
            color: white; /* Белый цвет текста */
            padding: 10px 20px;
            text-decoration: none; /* Убираем подчеркивание */
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        /* Стиль при наведении на кнопки */
        a.return-admin-panel:hover,
        a.return-edit-product:hover {
            background-color: #333; /* Темно-серый цвет при наведении */
            color: white; /* Белый цвет текста при наведении */
        }
    </style>
</head>
<body>
<div class="container">
    <div class="logout">
        <a href="/adminpanel.php" class="return-admin-panel">Вернуться в админ панель</a>
    </div>
    <h3>Выберите товар для редактирования:</h3>
    <ul>
        <?php
        // ... другой код ...

        $sql = "SELECT * FROM products"; // Замените этот запрос на ваш актуальный SQL-запрос
        $result = $conn->query($sql);

        if ($result->rowCount() > 0) {
            while ($row = $result->fetch()) {
                echo '<li><a href="edit_product.php?id=' . $row['id'] . '" class="return-edit-product">' . $row['name'] . '</a></li>';
            }
        } else {
            echo '<li>Нет доступных товаров для редактирования.</li>';
        }
        ?>
    </ul>
</div>
</body>
</html>
