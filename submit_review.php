<?php
// submit_review.php
session_start(); // Добавляем запуск сессии, если вы используете $_SESSION

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Подключение к базе данных
    $conn = new mysqli('127.0.0.1', 'rovel', '12072004Pavel', 'devauled');

    // Проверяем подключение к базе данных
    if ($conn->connect_error) {
        die("Ошибка подключения: " . $conn->connect_error);
    }

    // Получение данных из POST-запроса
    $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'Аноним';
    $color = $_POST['color'];
    $size = $_POST['size'];
    $stars = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
    $content = $_POST['content'];
    $date_time = $_POST['date_time'];
    // Составление запроса для вставки данных в БД
    $query = "INSERT INTO reviews (product_id, user_id, color, size, stars, content, date_time) VALUES (?, ?, ?, ?, ?, ?, ?)";

    // Подготовка запроса
    $stmt = $conn->prepare($query);

    // Проверяем подготовку запроса
    if (!$stmt) {
        die("Ошибка подготовки запроса: " . $conn->error);
    }

    // Привязка параметров
    $stmt->bind_param("iississ", $productId, $userId, $color, $size, $stars, $content, $date_time);

    // Выполнение запроса
    if ($stmt->execute()) {
        echo "Отзыв успешно добавлен";
    } else {
        echo "Ошибка при добавлении отзыва: " . $stmt->error;
    }

    // Закрытие соединения
    $stmt->close();
    $conn->close();
} else {
    echo "Неверный запрос";
}
?>