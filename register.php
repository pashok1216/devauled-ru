<?php
session_start(); // Начинаем сессию
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $db = new UniqueDatabase();
    $pdo = $db->connect();

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare('INSERT INTO users (username, email, password) VALUES (:username, :email, :password)');
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashed_password);

    try {
        $stmt->execute();

        // Устанавливаем сессионные переменные
        $_SESSION['user_id'] = $pdo->lastInsertId();
        $_SESSION['username'] = $username;
        $_SESSION['role'] = 'user'; // По умолчанию роль 'user'

        // Перенаправляем на главную страницу
        header('Location: index.php');
        exit;
    } catch (PDOException $e) {
        $message = 'Ошибка: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация</title>
</head>
<body>
<form action="register.php" method="post">
    <label for="username">Имя пользователя:</label>
    <input type="text" name="username" required><br>

    <label for="email">Email:</label>
    <input type="email" name="email" required><br>

    <label for="password">Пароль:</label>
    <input type="password" name="password" required><br>

    <button type="submit">Регистрация</button>
</form>

<?php if (isset($message)): ?>
    <p><?php echo $message; ?></p>
<?php endif; ?>
</body>
</html>