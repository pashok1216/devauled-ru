<?php
require_once 'config.php';
require_once 'db.php'; // Или другой путь, если файл находится в другом месте

$db = new UniqueDatabase();
$conn = $db->connect();

if (!$conn) {
    die('Ошибка подключения к базе данных: ' . $conn->errorInfo()[2]);
}

// Проверка авторизации пользователя
session_start();

if (!isset($_SESSION['auth'])) {
    header('Location: /products');
    exit();
}

// Добавление нового товара
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $size = $_POST['size'];
    $gender = $_POST['gender'];
    $fabric = $_POST['fabric'];
    $manufacturer = $_POST['manufacturer'];

    // Загрузка изображения товара
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image_name = $_FILES['image']['name'];
        $image_tmp = $_FILES['image']['tmp_name'];
        $image_ext = pathinfo($image_name, PATHINFO_EXTENSION);
        $image = uniqid() . '.' . $image_ext;
        move_uploaded_file($image_tmp, 'images/' . $image);
    }

    // Подготовленный запрос с использованием параметров
    $sql = "INSERT INTO products (name, price, image, size, gender, fabric, manufacturer) VALUES (:name, :price, :image, :size, :gender, :fabric, :manufacturer)";

    try {
        $stmt = $conn->prepare($sql);

        // Привязка параметров
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':price', $price, PDO::PARAM_STR);
        $stmt->bindParam(':image', $image, PDO::PARAM_STR);
        $stmt->bindParam(':size', $size, PDO::PARAM_STR);
        $stmt->bindParam(':gender', $gender, PDO::PARAM_STR);
        $stmt->bindParam(':fabric', $fabric, PDO::PARAM_STR);
        $stmt->bindParam(':manufacturer', $manufacturer, PDO::PARAM_STR);

        // Выполнение запроса
        $stmt->execute();
        header('Location: #'); // Укажите корректный URL для перенаправления после добавления товара
        exit();
    } catch (PDOException $e) {
        echo 'Ошибка добавления товара: ' . $e->getMessage();
    }
}
?>

    <!DOCTYPE html>
    <html>
    <head>
        <title>Добавление товара</title>
        <style>
            body {
                font-family: 'Montserrat', sans-serif;
                margin: 0;
                padding: 0;
                background-color: #f5f5f5;
            }

            .product {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                text-align: center;
                padding: 20px;
            }

            h3 {
                font-size: 24px;
                margin-bottom: 20px;
                border-bottom: 2px solid #333;
                padding-bottom: 10px;
            }

            .image-container {
                position: relative;
                margin-bottom: 20px;
            }

            .image-container img {
                max-width: 125px;
                border-radius: 10px;
                border: 1px solid black;
            }

            .delete-button {
                background-color: #ff0000;
                color: #fff;
                padding: 5px 10px;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                position: absolute;
                top: 10px;
                right: 10px;
                z-index: 1;
                display: none;
            }

            .add-image-container {
                width: 30px;
                height: 30px;
                border: 1px solid black;
                border-radius: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                position: relative;
                overflow: hidden;
                margin-bottom: 10px;
            }

            .add-image-container .horizontal,
            .add-image-container .vertical {
                background-color: black;
                position: absolute;
                z-index: 1;
            }

            .horizontal {
                width: 20px;
                height: 2px;
                left: 50%;
                transform: translateX(-50%);
            }

            .vertical {
                width: 2px;
                height: 20px;
                top: 50%;
                transform: translateY(-50%);
            }

            .add-image-container input[type="file"] {
                opacity: 0;
                width: 100%;
                height: 100%;
                position: absolute;
                top: 0;
                left: 0;
                cursor: pointer;
            }

            .form-container {
                max-width: 400px;
                margin: 0 auto;
            }

            .form-field {
                display: flex;
                flex-direction: row;
                align-items: center;
                margin-bottom: 10px;
                text-align: center;
            }

            .form-field label {
                font-weight: bold;
                margin-right: 10px;
                width: 150px;
                text-align: right;
            }

            .form-field input[type="text"],
            .form-field input[type="number"],
            .form-field input[type="file"],
            .form-field input[type="checkbox"] {
                flex: 1;
                padding: 10px;
                border: 1px solid #ddd;
                border-radius: 10px;
            }

            input[type="submit"] {
                background-color: #333;
                color: white;
                padding: 10px 20px;
                border: none;
                border-radius: 10px;
                cursor: pointer;
                margin-top: 10px;
            }

            input[type="submit"]:hover {
                background-color: #555;
            }

            a {
                background-color: #333;
                color: white;
                padding: 10px 20px;
                border: none;
                border-radius: 10px;
                cursor: pointer;
                margin-top: 10px;
                text-decoration: none; /* Убираем подчеркивание у ссылок */
                display: inline-block; /* Делаем кнопку строчным элементом */
                text-align: center; /* Выравнивание текста по центру */
                transition: background-color 0.3s; /* Анимация изменения фона при наведении */
            }

            a:hover {
                background-color: #555; /* Цвет фона при наведении */
                color: white; /* Цвет текста при наведении */
                text-decoration: none; /* Убираем подчеркивание у ссылок */
            }
        </style>
    </head>
    <body>

    <div class="product">
        <h3>Добавление товара</h3>

        <div class="form-container">
            <form method="post" action="" enctype="multipart/form-data">
                <div class="form-field">
                    <label for="name">Название товара:</label>
                    <input type="text" name="name" id="name" required>
                </div>
                <div class="form-field">
                    <label for="price">Цена товара:</label>
                    <input type="text" name="price" id="price">
                </div>
                <div class="form-field">
                    <label for="size">Размер товара:</label>
                    <input type="text" name="size" id="size">
                </div>
                <div class="form-field">
                    <label for="gender">Пол:</label>
                    <input type="text" name="gender" id="gender">
                </div>
                <div class="form-field">
                    <label for="fabric">Материал:</label>
                    <input type="text" name="fabric" id="fabric">
                </div>
                <div class="form-field">
                    <label for="manufacturer">Страна изготовитель:</label>
                    <input type="text" name="manufacturer" id="manufacturer">
                </div>
                <div class="form-field">
                    <label for="image">Изображение товара:</label>
                    <input type="file" name="image" id="image" accept="image/*">
                </div>
                <input type="submit" value="Добавить товар">
            </form>
        </div>
    </div>
    </body>
</html>

