<?php
// Подключение к базе данных
$conn = new mysqli('127.0.0.1', 'rovel', '12072004Pavel', 'devauled');

// Проверка подключения
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$color = $_GET['color'] ?? '';

// Подготовка и выполнение SQL-запроса
$sql = "SELECT * FROM products WHERE color = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $color);
$stmt->execute();
$result = $stmt->get_result();

// Получение информации о товаре
$productDetails = [];
if ($result->num_rows > 0) {
    $productDetails = $result->fetch_assoc();
}

// Закрытие подключения
$conn->close();

// Вернуть информацию о товаре в формате JSON
header('Content-Type: application/json');
echo json_encode($productDetails);
?>