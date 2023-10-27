<?php
// Инициализация сессии и подключение к базе данных
session_start();
require_once 'db.php';

// Проверка на авторизацию пользователя
if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$db = new UniqueDatabase();
$pdo = $db->connect();
$user_id = $_SESSION['user_id'];

// Получение данных пользователя
$stmt = $pdo->prepare('SELECT * FROM users WHERE user_id = :user_id');
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$user = $stmt->fetch();

if(!$user) {
    header('Location: login.php');
    exit;
}

// Получение адресов пользователя
$stmt = $pdo->prepare('SELECT * FROM delivery_addresses WHERE user_id = :user_id');
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$addresses = $stmt->fetchAll();

// Получение заказов пользователя
$stmt = $pdo->prepare('SELECT * FROM order_history WHERE user_id = :user_id');
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$orders = $stmt->fetchAll();

$hour = date('H');  // Получаем текущий час в 24-часовом формате
if ($hour >= 6 && $hour < 12) {
    $greeting = "Доброе утро";
} elseif ($hour >= 12 && $hour < 18) {
    $greeting = "Добрый день";
} else {
    $greeting = "Добрый вечер";
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Личный кабинет</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/lkusers.css"> <!-- Пользовательские стили -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <link rel="stylesheet" href="sweetalert2-11.7.29/sweetalert2.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script src="sweetalert2-11.7.29/sweetalert2.all.min.js"></script>
</head>
<body>
<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h1><?= $greeting ?>, <?= $user['first_name'] ?>!</h1>
        </div>
    </div>
</div>

<div class="container mt-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-body d-flex flex-column align-items-center">
                    <img src="<?= !empty($user['photo']) ? $user['photo'] : 'images/no_photo.png' ?>" alt="User Photo" class="card-img-top user-photo img-fluid" id="user-photo">
                    <div class="d-flex justify-content-center align-items-center w-100 p-3">  <!-- Обновленный код -->
                        <form id="photo-upload-form" enctype="multipart/form-data">
                            <span class="btn btn-primary btn-file">
                                Изменить фото <input type="file" name="photo" id="photo" accept="image/*">
                            </span>
                        </form>
                    </div>
                </div>
                <div class="list-group list-group-flush">
                    <a href="profile.php" class="list-group-item list-group-item-action">Профиль</a>
                    <a href="addresses.php" class="list-group-item list-group-item-action">Адреса Доставки</a>
                    <a href="payments.php" class="list-group-item list-group-item-action">Способы оплаты</a>
                    <a href="history_orders.php" class="list-group-item list-group-item-action">История заказов</a>
                    <a href="help_user.php" class="list-group-item list-group-item-action">Тех. Поддержка</a>
                </div>
            </div>
            <div class="row">
                <div class="col-12 text-center">  <!-- Центрирование кнопки -->
                    <button class="btn btn-primary back-to-site" onclick="window.location.href='index.php'">
                        <i class="fas fa-home"></i> Вернуться на сайт
                    </button>
                </div>
            </div>
        </div>


        <!-- Main content -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-body user-info">  <!-- Добавлен класс user-info -->
                    <h3>Статистика пользователя</h3>
                    <p><strong>Количество заказов:</strong> <?= count($orders) ?></p>
                    <p><strong>Количество адресов:</strong> <?= count($addresses) ?></p>
                    <!-- ... другая статистика ... -->
                </div>
            </div>
        </div>
    </div>
</div>



<script>
    $(document).ready(function (e) {
        $("#photo-upload-form").on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: "upload_photo.php",
                type: "POST",
                data:  new FormData(this),
                contentType: false,
                cache: false,
                processData:false,
                success: function(data)
                {
                    $(".user-photo").attr('src', data);
                },
                error: function()
                {
                    console.error("Ошибка загрузки фото");
                }
            });
        });

        // Добавьте этот код, чтобы автоматически отправить форму после выбора файла
        $('#photo').change(function() {
            $('#photo-upload-form').submit();
        });
    });
</script>
</body>
</html>