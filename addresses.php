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

// Получение адресов пользователя
$stmt = $pdo->prepare('SELECT * FROM delivery_addresses WHERE user_id = :user_id');
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$addresses = $stmt->fetchAll();

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
<div id="addresses">
    <h2>Адреса Доставки</h2>
    <ul>
        <?php foreach($addresses as $address): ?>
            <li>
                <?= htmlspecialchars($address['address']) ?>
                <!-- Кнопка удаления -->
                <button type="button" class="btn btn-danger btn-sm delete-address-button" data-address-id="<?= htmlspecialchars($address['id']) ?>" data-toggle="modal" data-target="#deleteAddressModal">Удалить</button>
            </li>
        <?php endforeach; ?>
    </ul>
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addAddressModal">
        Добавить новый адрес
    </button>
</div>

<!-- Модальное окно для подтверждения удаления адреса -->
<div class="modal" id="deleteAddressModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Подтвердите удаление</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                Вы уверены, что хотите удалить этот адрес?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" id="confirmDeleteButton">Удалить</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно для добавления адреса -->
<div class="modal" id="addAddressModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Добавить новый адрес</h5>
            </div>
            <div class="modal-body">
                <form id="addAddressForm">
                    <div class="form-group">
                        <label for="address">Адрес:</label>
                        <input type="text" class="form-control" id="address" name="address" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="addAddressButton">Добавить</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        var addressIdToDelete;

        // Устанавливаем обработчик для каждой кнопки удаления адреса
        $('.delete-address-button').click(function() {
            addressIdToDelete = $(this).data('address-id');  // Сохраняем ID адреса для удаления
        });

        // Устанавливаем обработчик для кнопки подтверждения удаления
        $('#confirmDeleteButton').click(function() {
            $.ajax({
                url: 'delete_address.php',
                type: 'POST',
                data: {
                    address_id: addressIdToDelete  // Передаем ID адреса для удаления
                },
                success: function(response) {
                    if (response === 'success') {
                        Swal.fire({
                            title: 'Успешно!',
                            text: 'Адрес успешно удален.',
                            icon: 'success',
                        });

                        // Удаляем адрес из списка на странице
                        $('button[data-address-id="' + addressIdToDelete + '"]').parent().remove();
                    } else {
                        Swal.fire({
                            title: 'Ошибка!',
                            text: 'Произошла ошибка при удалении адреса.',
                            icon: 'error',
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error: ' + status + error);
                }
            });

            // Закрыть модальное окно
            $('#deleteAddressModal').modal('hide');
        });
    });


    $('#addAddressButton').click(function(e) {
        e.preventDefault();

        $.ajax({
            url: 'add_address.php',
            type: 'POST',
            data: $('#addAddressForm').serialize(),
            success: function(response) {
                if (response === 'success') {
                    Swal.fire({
                        title: 'Успешно!',
                        text: 'Адрес успешно добавлен.',
                        icon: 'success',
                    });

                    var newAddress = $('#address').val();

                    if(newAddress.trim() !== "") {
                        $('#addresses ul').append('<li>' + newAddress +
                            ' <button type="button" class="btn btn-danger btn-sm delete-address-button" data-toggle="modal" data-target="#deleteAddressModal">Удалить</button>' +
                            '</li>');
                    }

                    $('#addAddressModal').modal('hide');
                    $('#addAddressForm')[0].reset();
                } else {
                    Swal.fire({
                        title: 'Ошибка!',
                        text: 'Произошла ошибка при добавлении адреса.',
                        icon: 'error',
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error: ' + status + error);
            }
        });
    });
</script>
</body>
</html>