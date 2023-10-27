<?php
session_start();  // Инициализация сессии

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Проверка, было ли уже зафиксировано посещение текущего пользователя
    if (!isset($_SESSION['visit_recorded'])) {
        $data = json_decode(file_get_contents("php://input"), true);
        $duration = $data['duration'];

        $db = new mysqli('127.0.0.1', 'rovel', '12072004Pavel', 'devauled');
        if ($db->connect_error) {
            die("Ошибка подключения: " . $db->connect_error);
        }

        $stmt = $db->prepare("INSERT INTO visits_data (user_id, visit_date, activity_duration) VALUES (?, NOW(), ?)");
        $stmt->bind_param("ii", $_SESSION['user_id'], $duration);
        $stmt->execute();
        $stmt->close();
        $db->close();

        $_SESSION['visit_recorded'] = true;  // Установите флаг, что посещение было записано
    }
}
?>