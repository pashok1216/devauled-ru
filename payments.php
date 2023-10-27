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

// Получение способов оплаты пользователя
$stmt = $pdo->prepare('SELECT * FROM payment_methods WHERE user_id = :user_id');
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$payments = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Личный кабинет</title>
    <link rel="stylesheet" href="css/lkusers.css">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="sweetalert2-11.7.29/sweetalert2.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script src="sweetalert2-11.7.29/sweetalert2.all.min.js"></script>
</head>
<body>
<div id="payments">
    <h2>Способы Оплаты</h2>
    <ul>
        <?php foreach($payments as $payment): ?>
            <li>
                Номер карты: <?= htmlspecialchars($payment['card_number']) ?><br>
                Срок действия: <?= htmlspecialchars($payment['expiry_date']) ?><br>
                Имя держателя карты: <?= htmlspecialchars($payment['cardholder_name']) ?><br>
            </li>
        <?php endforeach; ?>
    </ul>
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addPaymentModal">
        Добавить новый способ оплаты
    </button>
</div>

<!-- Модальное окно для добавления способа оплаты -->
<div class="modal" id="addPaymentModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Добавить новый способ оплаты</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="credit-card-form">
                    <div class="credit-card-box">
                        <div class="front">
                            <div class="chip"></div>
                            <div class="logo"></div>
                            <form id="addPaymentForm">
                                <input type="text" name="card_number" placeholder="Card Number" class="input-card-number">
                                <input type="text" name="cardholder_name" placeholder="Cardholder Name" class="input-card-holder">
                                <input type="text" name="expiry_date" placeholder="MM/YY" class="input-card-expiration-date">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="addPaymentButton">Добавить</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
<script>
    $('#addPaymentButton').click(function(e) {
        e.preventDefault();

        console.log('Card Number: ', $('#cardNumber').val());
        console.log('Card Holder Name: ', $('#cardHolder').val());
        console.log('Expiry Date: ', $('#expiryDate').val());

        $.ajax({
            url: 'add_payment.php',
            type: 'POST',
            data: $('#addPaymentForm').serialize(),
            success: function(response) {
                if (response === 'success') {
                    Swal.fire({
                        title: 'Успешно!',
                        text: 'Способ оплаты успешно добавлен.',
                        icon: 'success',
                    });

                    var newPayment = $('#cardNumber').val() + ' (' + $('#cardHolder').val() + ')';
                    $('#payments ul').append('<li>' + newPayment + '</li>');

                    $('#addPaymentModal').modal('hide');
                    $('#addPaymentForm')[0].reset();
                } else {
                    Swal.fire({
                        title: 'Ошибка!',
                        text: 'Произошла ошибка при добавлении способа оплаты.',
                        icon: 'error',
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error: ' + status + error);
            }
        });
    });
    });
</script>
</body>
</html>