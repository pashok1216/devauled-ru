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
    $sql_images = "SELECT image_url FROM product_images WHERE product_id = $product_id AND color = '$selected_color'";
    $result_images = $conn->query($sql_images);

// Проверяем, есть ли изображения
    if ($result_images->num_rows > 0) {
        // Получаем первое изображение в качестве основного
        $main_image = $result_images->fetch_assoc();
        echo '<img id="main-product-image" class="product-image" src="' . $main_image['image_url'] . '" alt="' . $row['name'] . '">';

        // Выводим остальные изображения как дополнительные
        echo '<div class="product-small-images-wrapper">';
        // Перебираем все изображения и выводим их
        $first = true;
        foreach ($result_images as $row_img) {
            $activeClass = $first ? 'active' : '';
            echo '<img onclick="changeMainImage(this.src, this)" class="product-small-image ' . $activeClass . '" src="' . $row_img['image_url'] . '" alt="' . $row['name'] . '">';
            $first = false;
        }
        echo '</div>';
    } else {
        echo '<p class="image-not-found">No images found for this product.</p>';
    }
    echo '</div>'; // Закрытие product-image-section

    // Секция информации о товаре
    echo '<div class="product-info-section">';
    echo '<div class="favorite-icon">';
    echo '</div>';
    echo '<h2 class="product-name">' . $row['name'] . '</h2>';
    echo '<p class="product-sku">' . $row['sku'] . '</p>'; // Добавление SKU
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
    echo '<div class="info-content" id="delivery"><br>По России действует бесплатная доставка и возврат.<br>
<br>
Курьерская доставка по Москве, Московской области, Санкт-Петербургу, Ленинградской области в течение 2-4 дней.<br>
Курьерская доставка по России в течение 3-7  дней.<br>
<br>
<a href="Delivery_rules.php" class="info-link">Подробнее об условиях доставки.</a><br>
<br>
Способы оплаты:<br>
<br>
Платежная карта | "Долями" от Тинькофф |<br>  "Плати частями" от Сбер<br>
<br>
<a href="Payment_rules.php" class="info-link">Подробнее о способах оплаты.</a><br>
<br>
Бесплатный обмен в течение 7 дней с даты  получения заказа.<br>
<br>
<a href="Returns_and_Exchanges_rules.php" class="info-link">Подробнее о правилах возврата и обмена.</a></div>';
    echo '<div class="info-content" id="reviews">';
    $productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    $query = "SELECT r.*, u.photo, u.first_name FROM reviews r JOIN users u ON r.user_id = u.user_id WHERE r.product_id = $productId ORDER BY r.date_time DESC LIMIT 3";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        // Выводим отзывы, если они есть
        while ($review = mysqli_fetch_assoc($result)) {
            // Форматируем дату
            $reviewDate = new DateTime($review['date_time']);
            $today = new DateTime();
            $dateString = $reviewDate->format('Y-m-d') === $today->format('Y-m-d') ? 'Сегодня' : $reviewDate->format('d.m.Y');

            // Проверяем, есть ли у пользователя фото
            $userPhoto = !empty($review['photo']) ? $review['photo'] : 'images/user.jpg'; // Замените 'images/user.jpg' на ваш путь к изображению по умолчанию

            // Формируем звезды для рейтинга
            $filledStars = str_repeat('★', $review['stars']);
            $emptyStars = str_repeat('☆', 5 - $review['stars']);
            $stars = $filledStars . $emptyStars;

            echo '<div class="review">';
            echo '<div class="review-header">';
            echo '<div class="review-photo"><img src="' . $userPhoto . '" alt="User Photo"></div>'; // Используйте переменную $userPhoto здесь
            echo '<div class="review-stars">' . $stars . '</div>';
            echo '</div>'; // Закрытие review-header
            echo '<div class="review-content">';
            echo '<div class="review-name">' . $review['first_name'] . '</div>';
            echo '<div class="review-details">';
            echo '<div class="review-date-time">' . $dateString . '</div>';
            echo '<div class="review-color">Цвет: ' . $review['color'] . '</div>';
            echo '<div class="review-size">Размер: ' . $review['size'] . '</div>';
            echo '</div>';
            echo '<div class="review-text">' . $review['content'] . '</div>';
            echo '</div>'; // Закрытие review-content
            echo '</div>'; // Закрытие review
        }
    echo '<div class="reviews-buttons">';
        echo '<div class="reviews-buttons">';
        echo '<button onclick="location.href=\'all_reviews.php?id=' . $productId . '\'" class="reviews-button">Смотреть все отзывы</button>';
        echo '<button onclick="location.href=\'add_review.php\'" class="reviews-button">Написать отзыв</button>';
        echo '</div>'; // Конец контейнера кнопок
    } else {
        // Сообщение об отсутствии отзывов и кнопка для добавления нового отзыва
        echo '<div class="review-card">';
        echo '<p>Отзывов пока нет.</p>';
        echo '<p>Напишите его первым!</p>';
        // Здесь кнопка "Смотреть все отзывы" не отображается
        echo '</div>';
        echo '<div class="review-add-button-container">'; // Контейнер для кнопки
        echo '<button onclick="location.href=\'add_review.php\'" class="reviews-button">Написать отзыв</button>';
        echo '</div>'; // Конец контейнера review-add-button-container
    }
    echo '</div>'; // Конец контейнера кнопок
    echo '</div>'; // Конец блока "Отзывы"

} else {
    echo '<p class="product-not-found">Product not found</p>';
}


// Закрытие подключения
$conn->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="css/product_details.css">
    <script src="js/product_details.js"></script>
    <meta charset="UTF-8">
    <title>Product Details</title>
</head>
<body>
<!-- Содержимое тела вашего документа -->
</body>
</html>