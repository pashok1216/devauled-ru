<?php
require_once 'header.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Подключение к базе данных
$conn = new mysqli('127.0.0.1', 'rovel', '12072004Pavel', 'devauled');

// Проверка подключения
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
echo '<div class="back-button-container">';
echo '<a href="product_details.php?id=' . $productId . '" class="back-button">← Назад</a>';
echo '</div>';
if ($productId > 0) {
    // Запрос информации о товаре
    $productQuery = "SELECT * FROM products WHERE id = $productId";
    $productResult = mysqli_query($conn, $productQuery);

    if ($product = mysqli_fetch_assoc($productResult)) {
        // Вывод информации о товаре с изменениями в стилях
        echo '<div class="product-top-section">';
        echo '<img src="' . htmlspecialchars($product['image']) . '" alt="' . htmlspecialchars($product['name']) . '" style="width: 120px; height: 120px; margin-right: 20px;">';
        echo '<div class="product-details">';
        echo '<h2 class="product-name">' . htmlspecialchars($product['name']) . '</h2>';

        // Запрос рейтинга товара
        $ratingQuery = "SELECT AVG(stars) as average_rating, COUNT(*) as total_reviews FROM reviews WHERE product_id = $productId";
        $ratingResult = mysqli_query($conn, $ratingQuery);

        if ($ratingRow = mysqli_fetch_assoc($ratingResult)) {
            $averageRating = round($ratingRow['average_rating'], 1);
            $totalReviews = $ratingRow['total_reviews'];

            // Генерируем строку звёзд для отображения
            $starsOutput = str_repeat('★', floor($averageRating)) . str_repeat('☆', 5 - floor($averageRating));

            // Выводим информацию о рейтинге
            echo '<div class="product-rating">';
            echo '<div class="rating-and-stars" style="display: flex; align-items: center;">';
            echo '<h3 class="average-rating" style="margin-right: 10px;">Общий рейтинг: ' . $averageRating . ' из 5</h3>';
            echo '<span class="stars-output" style="font-size: 1.5em; color: #000000;">' . $starsOutput . '</span>';
            echo '</div>';
            echo '<p class="total-reviews" style="align-self: flex-start; font-size: 1.2em;">Всего оценок: ' . $totalReviews . '</p>';
            echo '</div>';
        }
        echo '</div>';
        echo '</div>';
    } else {
        echo "Товар не найден.";
        exit;
    }

    $sortOrder = 'r.date_time DESC'; // Сортировка по умолчанию
    $sortByRating = '';
    $sortByDate = 'style="color: gray;"';
    if (isset($_GET['sort'])) {
        if ($_GET['sort'] == 'rating') {
            $sortOrder = 'r.stars DESC';
            $sortByRating = 'style="text-decoration: underline;"';
            $sortByDate = 'style="color: gray; text-decoration: none;"';
        } elseif ($_GET['sort'] == 'date') {
            $sortOrder = 'r.date_time DESC';
            $sortByRating = 'style="color: gray;"';
            $sortByDate = 'style="text-decoration: underline;"';
        }
    }

// Запрос отзывов с учетом выбранной сортировки
    $reviewsQuery = "SELECT r.*, u.photo, u.first_name FROM reviews r JOIN users u ON r.user_id = u.user_id WHERE r.product_id = $productId ORDER BY $sortOrder";
    $reviewsResult = mysqli_query($conn, $reviewsQuery);

// Вывод кнопки "Написать отзыв" и сортировки
    echo '<div class="actions-container" style="display: flex; justify-content: space-between; align-items: center;">';
    echo '<div class="sorting-container">';
    echo '<span>Сортировать по:</span> ';
    echo '<a href="?id=' . $productId . '&sort=date" ' . $sortByDate . '>Дате</a>';
    echo '<a href="?id=' . $productId . '&sort=rating" ' . $sortByRating . '>Оценке</a>';
    echo '</div>';
    echo '<button id="myBtn" class="write-review-button">Написать отзыв</button>';
    echo '</div>';

// Вывод отзывов
    if (mysqli_num_rows($reviewsResult) > 0) {
        echo '<div class="reviews-container">';
        while ($review = mysqli_fetch_assoc($reviewsResult)) {
            // Форматирование даты и вывод отзыва

            if (!empty($review['date_time'])) {
                $reviewDate = new DateTime($review['date_time']);
                $dateString = $reviewDate->format('Y-m-d') === (new DateTime())->format('Y-m-d') ? 'Сегодня' : $reviewDate->format('d.m.Y');
            } else {
                $dateString = 'Неизвестно';
            }
            $userPhoto = !empty($review['photo']) ? $review['photo'] : 'images/user.jpg';

            $filledStars = str_repeat('★', floor($review['stars']));
            $halfStar = (ceil($review['stars']) > floor($review['stars'])) ? '★' : '';
            $emptyStars = str_repeat('☆', 5 - ceil($review['stars']));
            $stars = $filledStars . $halfStar . $emptyStars;

            echo '<div class="review">';
            echo '<div class="review-header">';
            echo '<div class="review-photo"><img src="' . $userPhoto . '" alt="User Photo"></div>';
            echo '<div class="review-stars">' . $stars . '</div>';
            echo '</div>';
            echo '<div class="review-content">';
            echo '<div class="review-name">' . $review['first_name'] . '</div>';
            echo '<div class="review-details">';
            echo '<div class="review-date-time">' . $dateString . '</div>';
            echo '<div class="review-color">Цвет: ' . $review['color'] . '</div>';
            echo '<div class="review-size">Размер: ' . $review['size'] . '</div>';
            echo '</div>';
            echo '<div class="review-text">' . $review['content'] . '</div>';
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';
    } else {
        echo "Отзывы не найдены.";
    }
} else {
    echo "ID товара не указан.";
    exit;
}

// Закрытие подключения
$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <link rel="stylesheet" href="css/all_reviews.css">
    <script src="js/product_details.js"></script>
    <meta charset="UTF-8">
    <title>Детали товара</title>
    <style>
        /* Стили для модального окна */
        .modal {
            display: none; /* Скрыть модальное окно по умолчанию */
            position: fixed; /* Остается на месте даже при прокрутке */
            z-index: 1000; /* Сидит над другими элементами */
            left: 0;
            top: 0;
            width: 100%; /* Полная ширина */
            height: 100%; /* Полная высота */
            overflow: auto; /* Включить прокрутку, если нужно */
            background-color: rgba(0,0,0,0.4)!important; /* Полупрозрачный черный фон вокруг модального окна */
        }

        .modal-content {
            background-color: #ffffff!important; /* Белый фон для модального окна */
            margin: 5% auto; /* 5% от верха и по центру */
            padding: 20px;
            border: 1px solid #888!important;
            width: 35%; /* Ширина модального окна */
            border-radius: 25px; /* Скругленные углы */
        }

        /* Кнопка закрытия */
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        /* Дополнительные стили для звёзд */
        .stars {
            text-align: center;
            font-size: 2em;
            cursor: pointer;
        }

        /* Дополнительные стили для кнопки отправки отзыва */
        .review-btn {
            float: right;
            padding: 10px 20px;
            background-color: #ffffff!important;
            color: #000000!important;
            border: 2px solid #000000 !important;
            border-radius: 15px;
            cursor: pointer;
        }

        .review-btn:hover {
            background-color: #ffffff!important;
        }
        .star {
            color: grey; /* Цвет незакрашенных звезд */
            cursor: pointer;
            font-size: 1.5em;
        }

        /*.star.rated {*/
        /*    color: black; !* Цвет закрашенных звезд *!*/
        /*}*/
        .rated{
            color: black;
        }

    </style>
</head>
<body>

<!-- Кнопка для открытия модального окна -->

<!-- Код модального окна -->
<div id="myModal" class="modal">
    <!-- Модальное содержимое -->
    <div class="modal-content">
        <h2 style="text-align:center;">Написать отзыв</h2>

        <!-- Форма для отправки отзыва -->
        <form id="review-form" method="post">
            <div class="stars" data-rating="0">
                <span class="star" data-star="1">☆</span>
                <span class="star" data-star="2">☆</span>
                <span class="star" data-star="3">☆</span>
                <span class="star" data-star="4">☆</span>
                <span class="star" data-star="5">☆</span>
            </div>

            <!-- Поля для ввода цвета и размера -->
            <div class="review-section">
                <input type="text" name="color" placeholder="Цвет" style="width: 100%; margin-top: 10px;">
                <input type="text" name="size" placeholder="Размер" style="width: 100%; margin-top: 10px;">
            </div>

            <div class="review-section">
                <textarea name="content" placeholder="Ваш комментарий о товаре" style="width: 100%; height: 150px; border-radius: 10px;"></textarea>
            </div>

            <div class="review-section" style="display: flex; justify-content: space-between; align-items: center;">
                <div class="review-rules">
                    <a href="Returns_and_Exchanges_rules.php" class="info-link">Правила публикации отзывов.</a>
                </div>
                <button type="submit" id="submitReview" class="review-btn">Отправить</button>
            </div>

            <!-- Скрытое поле для product_id -->
            <input type="hidden" name="date_time" value="<?php echo date('Y-m-d H:i:s'); ?>">
            <input type="hidden" name="product_id" value="<?php
            $productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            echo $productId; ?>">
        </form>
    </div>
</div>

<script>
    // Получаем модальное окно
    var modal = document.getElementById("myModal");

    // Получаем кнопку, которая открывает модальное окно
    var btn = document.getElementById("myBtn");

    // Получаем элемент <span>, который закрывает модальное окно
    var span = document.getElementsByClassName("close")[0];

    // При клике на кнопку открываем модальное окно
    btn.onclick = function() {
        modal.style.display = "block";
    }


    // При клике вне модального окна, закрываем его
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
    // Получаем контейнер звезд и добавляем обработчик событий для всех звезд внутри
    document.querySelector('.stars').addEventListener('click', function(e) {
        if (e.target.classList.contains('star')) { // Проверяем, что клик был именно по звезде
            var rating = e.target.dataset.star; // Получаем рейтинг звезды
           document.querySelector('.stars').dataset.rating = rating; // Запоминаем рейтинг в data-атрибуте
            // Обновляем внешний вид всех звезд в соответствии с рейтингом
            document.querySelectorAll('.star').forEach(function(star) {
                if (star.dataset.star <= rating) {
                    star.textContent = '★'; // Закрашенная звезда
                    star.classList.add('rated'); // Добавляем класс для закрашенной звезды
                } else {
                    star.textContent = '☆'; // Незакрашенная звезда
                    star.classList.remove('rated'); // Убираем класс для закрашенной звезды
                }
            });
        }
    });
    // Получаем кнопку отправки отзыва
    var submitBtn = document.getElementById("submitReview");


    document.getElementById('review-form').addEventListener('submit', function(e) {
        e.preventDefault(); // Предотвратить стандартную отправку формы

        var formData = new FormData(this); // Собрать данные формы

        // Добавляем рейтинг звезд в formData
        var stars = document.querySelector('.stars').getAttribute('data-rating');
        formData.append('rating', stars);

        // Отправить данные формы на сервер с помощью AJAX
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'submit_review.php', true);

        // Что делать после получения ответа от сервера
        xhr.onload = function() {
            if (xhr.status === 200) {
                // Если сервер вернул статус OK, закрыть модальное окно и возможно отобразить сообщение об успехе
                alert('Отзыв успешно добавлен'); // Или другой способ уведомления пользователя
                modal.style.display = 'none';
                // Тут можно добавить код для обновления списка отзывов, если это необходимо
            } else {
                // Обработать случай, когда произошла ошибка
                alert('Ошибка отправки формы: ' + xhr.statusText);
            }
        };

        // Обработка ошибок при отправке данных на сервер
        xhr.onerror = function() {
            alert('Ошибка отправки формы: ' + xhr.statusText);
        };

        xhr.send(formData); // Отправить данные формы на сервер
    });
</script>

</body>
</html>