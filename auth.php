<?php
ini_set('display_errors', '0'); // отключить отображение любых ошибок
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $db = new UniqueDatabase();
    $pdo = $db->connect();

    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email');
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
        $_SESSION['auth'] = true;

        error_log("Успешный вход пользователя с email: " . $email);

        // Новый код для установки $_SESSION['receiver_id_admin']
        $admin_query = $pdo->prepare('SELECT user_id FROM users WHERE role = :role LIMIT 1');
        $admin_query->execute([':role' => 'SuperAdmin']);
        $admin_result = $admin_query->fetch();
        if ($admin_result) {
            $_SESSION['receiver_id_admin'] = $admin_result['user_id'];
        } else {
            $_SESSION['receiver_id_admin'] = null;
        }
        // Конец нового кода

        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'role' => $user['role']
        ]);
    } else {
        $message = $user ? 'Неправильный пароль' : 'Пользователь с таким email не найден';
        error_log("Неудачная попытка входа с email: " . $email . ", Причина: " . $message);
        echo 'error';
    }
}
?>