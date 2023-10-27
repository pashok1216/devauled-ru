<?php
session_start();
require_once 'db.php';

$db = new UniqueDatabase();
$conn = $db->connect();

$successMessage = "";
$errorMessage = "";

if (!isset($_SESSION['auth'])) {
    header('Location: /products');
    exit();
}

if (isset($_GET['id'])) {
    $productId = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = :productId");
    $stmt->bindParam(':productId', $productId, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $price = $_POST['price'];
            $fabric = $_POST['fabric'];
            $size = isset($_POST['size']) ? implode(',', $_POST['size']) : '';
            $imageUpdate = "";

            if (isset($_POST['image_deleted']) && $_POST['image_deleted'] == '1') {
                $imageUpdate = ", image = NULL";
            }

            if (isset($_FILES['new_image']) && $_FILES['new_image']['error'] == 0) {
                $targetDir = "images/";
                $targetFile = $targetDir . basename($_FILES["new_image"]["name"]);
                $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
                $check = getimagesize($_FILES["new_image"]["tmp_name"]);
                if ($check !== false) {
                    if (move_uploaded_file($_FILES["new_image"]["tmp_name"], $targetFile)) {
                        $imageUpdate = ", image = '" . basename($_FILES["new_image"]["name"]) . "'";
                    } else {
                        $errorMessage = "Ошибка загрузки файла.";
                    }
                } else {
                    $errorMessage = "Файл не является изображением.";
                }
            }

            $updateSql = "UPDATE products SET name = :name, price = :price, fabric = :fabric, size = :size $imageUpdate WHERE id = :productId";
            $stmt = $conn->prepare($updateSql);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':price', $price, PDO::PARAM_STR);
            $stmt->bindParam(':fabric', $fabric, PDO::PARAM_STR);
            $stmt->bindParam(':size', $size, PDO::PARAM_STR);
            $stmt->bindParam(':productId', $productId, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $successMessage = 'Товар успешно обновлен!';
                $selectStmt = $conn->prepare("SELECT * FROM products WHERE id = :productId");
                $selectStmt->bindParam(':productId', $productId, PDO::PARAM_INT);
                $selectStmt->execute();
                $row = $selectStmt->fetch(PDO::FETCH_ASSOC);
            }
        }
    } else {
        echo '<p>Товар не найден.</p>';
    }
} else {
    echo '<p>Идентификатор товара не указан.</p>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Редактирование товара</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="sweetalert2-11.7.29/sweetalert2.min.css">
    <script src="sweetalert2-11.7.29/sweetalert2.all.min.js"></script>
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
            min-width: 127px;
            min-height: 127px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid black;  /* Добавьте эту строку для черной обводки */
            background-color: white;  /* Добавьте эту строку для белого фона */
            border-radius: 10px;
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
            top: 5px;
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

        #success-notification {
            display: <?= empty($successMessage) ? 'none' : 'block' ?>;
            background-color: #4CAF50;
            color: white;
            text-align: center;
            padding: 10px;
            position: fixed;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            z-index: 9999;
            width: 300px;
            border-radius: 5px;
        }

        #error-notification {
            display: <?= empty($errorMessage) ? 'none' : 'block' ?>;
            background-color: #FF0000;
            color: white;
            text-align: center;
            padding: 10px;
            position: fixed;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            z-index: 9999;
            width: 300px;
            border-radius: 5px;
        }
        .custom-button {
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

        .custom-button:hover {
            background-color: #555; /* Цвет фона при наведении */
            color: white; /* Цвет текста при наведении */
            text-decoration: none; /* Убираем подчеркивание у ссылок */
        }
        .delete-button {
            width: 30px;
            height: 30px;
            background-color: transparent;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            position: absolute;
            top: 130px;
            right: 995px;
            z-index: 2;
            font-size: 40px;
            line-height: 30px;
            color: #ff0000;
            cursor: pointer;
            transition: opacity 0.3s, text-shadow 0.3s;
            font-family: "Arial", sans-serif;
            padding-top: 2px;
            outline: none; /* Убираем стандартную обводку */
        }

        .delete-button:before {
            content: '✖';
        }

        .delete-button:hover {
            opacity: 0.9;
        }

        .delete-button:active, .delete-button:focus {
            outline: none; /* Убираем обводку при активации или фокусе на кнопке */
        }
    </style>
</head>
<body>

<div class="product">
    <h3>Редактирование товара</h3>
    <?php if (isset($row) && $row) { ?>
        <div class="image-container">
            <?php
            if (isset($row['image']) && $row['image']) {
                echo '<img src="images/' . htmlspecialchars($row['image']) . '" alt="' . htmlspecialchars($row['name']) . '">';
                echo '<button class="delete-button" onclick="deleteImage(' . $productId . ')"></button>';
            }
            ?>
        </div>
        <div class="add-image-container">
            <div class="horizontal"></div>
            <div class="vertical"></div>
            <input type="file" name="new_image">
        </div>
        <div class="form-container">
            <form method="post" action="" enctype="multipart/form-data">
                <div class="form-field">
                    <label for="name">Название товара:</label>
                    <input type="text" name="name" id="name" value="<?= isset($row['name']) ? htmlspecialchars($row['name']) : '' ?>" required>
                </div>
                <div class="form-field">
                    <label for="price">Цена товара:</label>
                    <input type="number" name="price" id="price" value="<?= htmlspecialchars($row['price']) ?>" required>
                </div>
                <input type="hidden" name="image_deleted" id="image_deleted" value="0">
                <div class="form-field">
                    <label for="fabric">Материал товара:</label>
                    <input type="text" name="fabric" id="fabric" value="<?= htmlspecialchars($row['fabric']) ?>">
                </div>
                <div class="form-field">
                    <label>Размер товара:</label>
                    <?php
                    $availableSizes = explode(',', $row['size']);
                    $sizes = ['XS', 'S', 'M', 'L', 'XL'];

                    foreach ($sizes as $size) {
                        $checked = in_array($size, $availableSizes) ? 'checked' : '';
                        echo '<div class="size-field">';
                        echo '<input type="checkbox" name="size[]" value="' . $size . '" ' . $checked . '>';
                        echo '<div class="size-label">' . $size . '</div>';
                        echo '</div>';
                    }
                    ?>
                </div>
                <input type="submit" value="Сохранить">
            </form>
            <form method="post" action="">
                <input type="hidden" name="delete" value="1">
                <input type="submit" value="Удалить товар">
            </form>
        </div>
        <a href="/adminpanel" class="custom-button">Вернуться в админ панель</a>
        <a href="/delete_product.php" class="custom-button">Вернуться к выбору редактируемого товара</a>
    <?php } else { ?>
        <?= $errorMessage ?>
    <?php } ?>
</div>


<script>
    function deleteImage(productId) {
        Swal.fire({
            title: 'Вы уверены?',
            text: "Вы действительно хотите удалить изображение?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Да, удалить!',
            cancelButtonText: 'Отмена'
        }).then((result) => {
            if (result.isConfirmed) {
                const imageContainer = document.querySelector('.image-container');
                if (imageContainer.querySelector('img')) {
                    imageContainer.querySelector('img').remove();
                }
                document.getElementById('image_deleted').value = '1';
            }
        });
    }

    document.querySelector('.image-container').addEventListener('mouseenter', function() {
        if (document.querySelector('.image-container img')) {
            document.querySelector('.delete-button').style.display = 'block';
        }
    });

    document.querySelector('.image-container').addEventListener('mouseleave', function() {
        if (document.querySelector('.image-container img')) {
            document.querySelector('.delete-button').style.display = 'none';
        }
    });

    document.querySelector('input[name="new_image"]').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                const imageContainer = document.querySelector('.image-container');
                let img = imageContainer.querySelector('img');
                if (!img) {
                    img = document.createElement('img');
                    img.style.maxWidth = '125px';
                    imageContainer.appendChild(img);
                }
                img.src = event.target.result;
            }
            reader.readAsDataURL(file);
        }
    });

    document.querySelector('.add-image-container').addEventListener('click', function() {
        document.querySelector('input[name="new_image"]').click();
    });

    window.onload = function() {
        <?php if (!empty($successMessage)) { ?>
        Swal.fire({
            icon: 'success',
            title: 'Успех',
            text: '<?= $successMessage ?>'
        });
        <?php } ?>
        <?php if (!empty($errorMessage)) { ?>
        Swal.fire({
            icon: 'error',
            title: 'Ошибка',
            text: '<?= $errorMessage ?>'
        });
        <?php } ?>
    };
</script>

</body>
</html>




































