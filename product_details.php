<?php
require_once 'header.php';

// Подключение к базе данных
$conn = new mysqli('127.0.0.1', 'rovel', '12072004Pavel', 'devauled');

// Проверка подключения
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Получение ID продукта из URL
$product_id = $_GET['id'];

// Выполнение SQL-запроса
$sql = "SELECT * FROM products WHERE id = $product_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    // Хлебные крошки
    echo '<div class="breadcrumbs">';
    echo '<a href="catalog.php?category=clothing">Одежда</a> / ';
    echo '<a href="catalog.php?subcategory=sweatshirts">Свитшоты</a> / ';
    echo '<span>' . $row['name'] . '</span>';
    echo '</div>';

    // Обертка для карточки товара
    echo '<div class="product-card-wrapper">';

    // Секция изображений
    echo '<div class="product-image-section">';
    $selected_color = urldecode($_GET['color'] ?? 'Белый');
    $sql_images = "SELECT image_url FROM product_images WHERE product_id = $product_id";
    if ($selected_color) {
        $sql_images .= " AND color = '$selected_color'";
    }
    $result_images = $conn->query($sql_images);
    if ($result_images->num_rows > 0) {
        while($row_img = $result_images->fetch_assoc()) {
            echo '<img class="product-image" src="' . $row_img['image_url'] . '" alt="' . $row['name'] . '">';
        }
    } else {
        echo '<p class="image-not-found">No images found for this product.</p>';
    }
    echo '</div>'; // Закрытие product-image-section

    // Секция информации о товаре
    echo '<div class="product-info-section">';
    echo '<div class="favorite-icon">';
    echo '</div>';
    echo '<h2 class="product-name">' . $row['name'] . '</h2>';
    echo '<p class="product-price">' . $row['price'] . '<span class="currency">₽</span></p>';
    echo '<div class="quantity-box">';
    echo '  <button id="decrease" class="quantity-button">-</button>';
    echo '  <div id="quantity" class="quantity-number">1</div>';
    echo '  <button id="increase" class="quantity-button">+</button>';
    echo '</div>';

    // Секция деталей
    echo '<div class="product-details">';
    $colorMap = [
        'Белый' => 'white',
        'Черный' => 'black',
        'Красный' => 'red',
        'Желтый' => 'Yellow',
        'Оранжевый' => 'Orange',
        'Зеленый' => 'green',
        'Синий' => 'blue',
        'Фиолетовый' => 'Purple',
        'Розовый' => 'Pink',
        'Коричневый' => 'Brown',
        'Серый' => 'Grey',
        // добавьте другие цвета по необходимости
    ];

    $colors = explode(",", $row['color']);
    echo '<div id="current-color"><span>None</span></div>';
    echo '<div class="available-colors">';
    foreach ($colors as $color) {
        $cssColor = isset($colorMap[$color]) ? $colorMap[$color] : $color;
        echo '<span class="color-circle" data-color="' . $color . '" data-color-name="' . $color . '" style="background-color:' . $cssColor . ';"></span> ';
    }
    echo '</div>';
    echo '<div class="size-container">';
    echo '<p class="size-title">Размеры</p>'; // Добавляем название "Размеры"
    $sizes = explode(",", $row['size']);
    foreach ($sizes as $size) {
        echo '<span class="size-circle" onclick="selectSize(this)">' . trim($size) . '</span> ';
    }
    echo '</div>';
    echo '<button class="product-add-to-cart">Добавить в корзину</button>';
    echo '</div>'; // Закрытие product-info-section

    echo '</div>'; // Закрытие product-card-wrapper

    echo '<div class="product-info-fullwidth">';
    echo '<div class="info-tab" data-tab="description">Описание</div>';
    echo '<div class="info-tab" data-tab="details">Детали</div>';
    echo '<div class="info-tab" data-tab="care">Уход</div>';
    echo '<div class="info-tab" data-tab="delivery">Доставка и оплата</div>';
    echo '<div class="info-tab" data-tab="reviews">Отзывы</div>';

    echo '<div class="info-content" id="description">' . $row['description'] . '</div>';
    echo '<div class="info-content" id="details">' . $row['details'] . '</div>';
    echo '<div class="info-content" id="care">' . $row['care'] . '</div>';
    echo '<div class="info-content" id="delivery">Информация о доставке и оплате</div>';
    echo '<div class="info-content" id="reviews">' . $row['reviews'] . '</div>';
    echo '</div>';
} else {
    echo '<p class="product-not-found">Product not found</p>';
}


// Закрытие подключения
$conn->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product Details</title>
    <style>
        /* Подключение шрифта */
        @font-face {
            font-family: 'TerminatorCyr';
            src: url('fonts/ofont.ru_Terminator Cyr.ttf'); /* Укажите актуальный путь к файлу шрифта */
        }
        @font-face {
            font-family: 'MontserratLocal';
            src: url('fonts/ofont.ru_Montserrat.ttf') format('truetype');
        }
        @font-face {
            font-family: 'Monserrat_medium';
            src: url('fonts/ofont.ru_Montserrat_medium.ttf') format('truetype');
        }
        @font-face {
            font-family: 'Monserrat_light';
            src: url('fonts/ofont.ru_Montserrat_light.ttf') format('truetype');
        }
        .product-card-wrapper {
            display: flex;
        }

        .product-image-section {

        }
        .product-image-section img {
            max-width: 950px;
            max-height: 950px;
            margin-right: 100px;
            object-fit: contain;  /* Это поможет сохранить пропорции изображения */
        }
        h2 {
            font-family: 'TerminatorCyr', sans-serif;
        }

        .product-sku {
            font-family: 'Monserrat_medium', sans-serif;
            color: black;
        }

        .product-price {
            font-family: 'Monserrat_light', sans-serif;
            color: black;
            font-size: 30px;  /* Увеличенный размер шрифта для цены */
        }

        .currency {
            font-size: 18px;  /* Уменьшенный размер шрифта для символа рубля */
        }
        .breadcrumbs {
            margin-bottom: 10px;  /* Добавляем нижний отступ */
        }
        .product-details {
            /* стили для деталей продукта */
        }
        /* Кружочки для выбора цвета */
        /* Кружочки для выбора цвета */
        .product-details .color-circle {
            display: inline-block;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 5px;
            border: 2px solid #000;  /* Добавлена черная обводка толщиной 2px */
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.15);  /* Добавлена тень */
        }

        /* Кружочки для выбора размера */
        .product-details .size-circle {
            display: inline-flex;  /* Изменено с inline-block на inline-flex */
            align-items: center;  /* Выравнивание по вертикали */
            justify-content: center;  /* Выравнивание по горизонтали */
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 5px;
            border: 2px solid #000;
            cursor: pointer;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.15);
        }

        /* Стиль при активации (нажатии) */
        .product-details .size-circle.active {
            background-color: grey;
            color: white;
        }
        .product-add-to-cart {
            font-family: 'MontserratLocal', sans-serif;
            position: relative;
            overflow: hidden;
            background-color: black;
            color: white;
            padding: 10px 20px;
            border-radius: 20px;
            border: none;
            cursor: pointer;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.15);  /* Добавлена тень */
            z-index: 0;
            margin-top: 40px; /* Увеличиваем отступ сверху */

        }

        /* Псевдоэлемент для анимации */
        .product-add-to-cart::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border-radius: 20px;
            background-color: grey;
            transform-origin: left;
            transform: scaleX(0);
            transition: transform 0.3s ease-in-out;
            z-index: -1;

        }

        /* Стиль при наведении */
        .product-add-to-cart:hover::before {
            transform: scaleX(1);
        }add-to-cart:hover::before {
             transform: scaleX(1);
         }
        .quantity-box {
            /* удалить margin-top и margin-left */
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 150px;
            border-radius: 32px;
            overflow: hidden;
            border: 2px solid black;
        }

        .favorite-icon img {
            /* удалить margin-top и margin-left */
            cursor: pointer;
        }

        .quantity-button {
            width: 40px;
            height: 40px;
            border: none;

            background-color: rgba(204, 204, 204, 0);
            cursor: pointer;
            font-size: 18px;
            text-align: center;
        }

        .quantity-number {
            flex-grow: 1;
            text-align: center;
            font-size: 18px;
            padding: 10px;
            border-left: 1px solid black;
            border-right: 1px solid black;
        }
        .quantity-button:focus {
            outline: none;
        }

        .available-colors .color-circle {
            display: inline-block;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 5px;
            border: 2px solid #000;  /* Добавлена черная обводка толщиной 2px */
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.15);  /* Добавлена тень */
        }
        .product-info-fullwidth {
            width: 100%;
            background-color: rgba(244, 244, 244, 0);
            padding: 20px;
            float: none;
            clear: both;
            display: block;
            position: absolute;
            top: 1065px;
            min-height: 400px; /* Вы можете изменить это значение */
        }
        .info-tab {
            display: inline-block;
            margin-right: 20px;
            cursor: pointer;
        }

        .info-tab span {
            transition: border-bottom 0.3s ease-in-out;
            border-bottom: 2px solid transparent;
            display: inline-block;
        }

        .info-tab.active {
            border-bottom: 2px solid black;
        }

        .info-content {
            display: none;
        }

        .info-content.active {
            display: block;
        }
    </style>
</head>
<body>
<script>
    $(document).ready(function() {
        $(".info-tab").click(function() {
            // Убираем активный класс у всех вкладок и блоков
            $(".info-tab").removeClass("active");
            $(".info-content").removeClass("active");

            // Добавляем активный класс к нажатой вкладке
            $(this).addClass("active");

            // Получаем значение data-tab у нажатой вкладки
            var tab = $(this).data("tab");

            // Делаем соответствующий блок контента активным
            $("#" + tab).addClass("active");
        });
    });
    document.addEventListener("DOMContentLoaded", function() {
        const quantityElement = document.getElementById("quantity");
        const increaseButton = document.getElementById("increase");
        const decreaseButton = document.getElementById("decrease");

        increaseButton.addEventListener("click", function() {
            let currentQuantity = parseInt(quantityElement.textContent, 10);
            currentQuantity += 1;
            quantityElement.textContent = currentQuantity;
        });

        decreaseButton.addEventListener("click", function() {
            let currentQuantity = parseInt(quantityElement.textContent, 10);
            if (currentQuantity > 1) {
                currentQuantity -= 1;
                quantityElement.textContent = currentQuantity;
            }
        });
    });
    // document.addEventListener('DOMContentLoaded', function() {
    //     const heartIcon = document.getElementById('heart-icon');
    //
    //     heartIcon.addEventListener('click', function() {
    //         if (heartIcon.getAttribute('src') === 'images/heart_empty.png') {
    //             heartIcon.setAttribute('src', 'images/heart_filled.png');
    //         } else {
    //             heartIcon.setAttribute('src', 'images/heart_empty.png');
    //         }
    //     });
    // });
    document.addEventListener('DOMContentLoaded', function() {
        // Ваш код для обработки количества товаров, избранных и т.д.
        // ...

        const currentColorElement = document.querySelector('#current-color span');

        // Установка текущего цвета из параметров URL при загрузке страницы
        const initialUrl = new URL(window.location);
        const initialSelectedColor = initialUrl.searchParams.get('color');
        if (initialSelectedColor) {
            currentColorElement.textContent = initialSelectedColor;
        }

        const colorCircles = document.querySelectorAll('.color-circle');

        colorCircles.forEach(function(circle) {
            circle.addEventListener('click', function() {
                const colorValue = circle.getAttribute('data-color');
                const colorName = circle.getAttribute('data-color-name');
                currentColorElement.textContent = colorName || 'None';

                // Обновление параметра цвета в URL
                const newUrl = new URL(window.location);
                newUrl.searchParams.set('color', colorValue);
                history.pushState({}, '', newUrl);

                // Перезагрузка страницы с новым параметром цвета
                window.location.reload();
            });
        });

        const currentColorFromURL = initialUrl.searchParams.get('color') || 'Белый';  // Установлено значение по умолчанию 'Белый'

        const matchingCircle = Array.from(colorCircles).find(
            circle => circle.getAttribute('data-color') === currentColorFromURL
        );

        if (matchingCircle) {
            const colorName = matchingCircle.getAttribute('data-color-name');
            currentColorElement.textContent = colorName || 'None';
        }
    });
    function selectSize(element) {
        // Снимаем выделение со всех кружков
        const circles = document.querySelectorAll('.size-circle');
        circles.forEach(circle => circle.classList.remove('active'));

        // Выделяем выбранный кружок
        element.classList.add('active');
    }
    $(document).ready(function() {
        // Активировать вкладку "Описание" при загрузке
        $('[data-tab="description"]').addClass('active');
        $('#description').addClass('active');

        $(".info-tab").click(function() {
            $(".info-tab").removeClass("active");
            $(".info-content").removeClass("active");

            $(this).addClass("active");
            var tab = $(this).data("tab");
            $("#" + tab).addClass("active");
        });
    });
</script>
</body>
</html>