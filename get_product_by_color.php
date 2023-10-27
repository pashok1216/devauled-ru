<?php
header('Content-Type: application/json');

// Подключение к базе данных
$conn = new mysqli('127.0.0.1', 'rovel', '12072004Pavel', 'devauled');

// Проверка подключения
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Получение цвета из URL
$color = $_GET['color'];

// Выполнение SQL-запроса
$sql = "SELECT * FROM products WHERE color = '$color' LIMIT 1";
$result = $conn->query($sql);

// Вывод результатов
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode($row);
} else {
    echo json_encode(["error" => "Product not found"]);
}

// Закрытие подключения
$conn->close();
?>