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
<div class="tab-content">
    <div class="container">
        <div id="profile" class="container tab-pane active"><br>
            <h2>Профиль Пользователя</h2>
            <p>Имя: <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></p>
            <p>Почта: <?= htmlspecialchars($user['email']) ?></p>
            <p>Телефон: <?= htmlspecialchars($user['phone']) ?? 'Не указано' ?></p>
            <p>Дата рождения: <?= htmlspecialchars($user['birthdate']) ?? 'Не указано' ?></p>
            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#editProfileModal">Изменить профиль</button>
            <a href="change_password.php">Изменить пароль</a>
        </div>
        <!-- Модальное окно для изменения данных пользователя -->
        <div class="modal" id="editProfileModal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Изменить Профиль</h5>
                    </div>
                    <div class="modal-body">
                        <form id="editProfileForm">
                            <div class="form-group">
                                <label for="email">Почта:</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="phone">Телефон:</label>
                                <input type="text" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($user['phone']) ?? '' ?>" required>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="saveChangesButton">Сохранить изменения</button>
                    </div>
                </div>
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

    $(document).ready(function(){
        $('#phone').mask('8 (000) 000 00 00');  // Форматирование поля телефона

        $('#saveChangesButton').click(function(e) {
            e.preventDefault();

            var email = $('#email').val();
            var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!emailPattern.test(email)) {
                Swal.fire({
                    title: 'Ошибка!',
                    text: 'Введите действительный адрес электронной почты.',
                    icon: 'error',
                });
                return;
            }

            $.ajax({
                url: 'edit_profile_process.php',
                type: 'POST',
                data: $('#editProfileForm').serialize(),
                success: function(response) {
                    if (response === 'success') {
                        Swal.fire({
                            title: 'Успешно!',
                            text: 'Данные профиля успешно обновлены.',
                            icon: 'success',
                        });
                        $('#editProfileModal').modal('hide');

                        $('#profile p:contains("Почта:")').text('Почта: ' + $('#email').val());
                        $('#profile p:contains("Телефон:")').text('Телефон: ' + $('#phone').val());
                    } else {
                        Swal.fire({
                            title: 'Ошибка!',
                            text: 'Произошла ошибка при обновлении данных профиля.',
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