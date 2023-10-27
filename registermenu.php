<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $db = new UniqueDatabase();
    $pdo = $db->connect();

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare('INSERT INTO users (first_name, last_name, email, password) VALUES (:first_name, :last_name, :email, :password)');
    $stmt->bindParam(':first_name', $firstName);
    $stmt->bindParam(':last_name', $lastName);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashed_password);

    try {
        $stmt->execute();

        // Устанавливаем сессионные переменные
        $_SESSION['user_id'] = $pdo->lastInsertId();
        $_SESSION['role'] = 'user'; // По умолчанию роль 'user'

    } catch (PDOException $e) {
        $message = 'Ошибка: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" type="text/css" href="css/regmenu.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style></style>

    <!-- Bootstrap CSS and JS links -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
<header>
</header>
<div class="modal fade" id="registerModal" tabindex="-1" role="dialog" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog register-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="varperr">
                <h5 class="modal-title" id="registerModalLabel">Регистрация</h5>
            </div>
            <div class="modal-body">
                <form class="register-form" method="POST" action="">
                    <div class="form-group row">
                        <div class="col">
                            <label for="registerFirstName"></label>
                            <input type="text"placeholder="Имя" class="form-control" id="registerFirstName" name="first_name" required>
                        </div>
                        <div class="col">
                            <label for="registerLastName"></label>
                            <input type="text" placeholder="Фамилия" class="form-control" id="registerLastName" name="last_name" required>
                        </div>
                    </div>
                    <div cl
                    <div class="email">
                        <input type="text" placeholder="Почта" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <input type="password"placeholder="Пароль" class="form-control" id="password" name="password" required>
                    </div>

                    <button type="submit" class="btn btnn-primary" name="register">Зарегистрироваться</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    function toggleMenu() {
        const nav = document.querySelector('.nav');
        nav.style.display = (nav.style.display === 'none' || nav.style.display === '') ? 'block' : 'none';
    }

</script>
</body>
</html>