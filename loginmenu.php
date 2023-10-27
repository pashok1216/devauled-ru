<?php
ini_set('display_errors', '0'); // отключить отображение любых ошибок
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING); // не показывать предупреждения и уведомления
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'db.php';

?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="css/logmenu.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="sweetalert2-11.7.29/sweetalert2.min.css">
    <script src="sweetalert2-11.7.29/sweetalert2.all.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>

<body>
<header></header>
<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="varperr">
                <h5 class="modal-title" id="loginModalLabel">Авторизация</h5>
            </div>
            <div class="modal-body">
                <form class="login-form" method="POST" action="">
                    <div class="email">
                        <input type="text" placeholder="Почта" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Пароль</label>
                        <div class="input-group">
                            <input type="password" placeholder="Пароль" class="form-control pwd" id="password" name="password" required>
                            <div class="input-group-append">
                                <span class="input-group-text reveal" onclick="togglePassword('password')"><i class="fa fa-eye"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="remember-forgot">
                        <label><input type="checkbox">Запомнить меня</label>
                        <a href="#" class="password-recovery-link">Забыли пароль?</a>
                    </div>
                    <div class="register-link">
                        <p>У вас нет аккаунта?<a href="#" class="register-link"> Зарегистрироваться</a></p>
                    </div>
                    <button type="submit" class="btn btn-primary" name="login">Войти</button>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="passwordRecoveryModal" tabindex="-1" role="dialog" aria-labelledby="passwordRecoveryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="passwordRecoveryModalLabel">Смена пароля</h5>
            </div>
            <div class="modal-body">
                <form class="password-recovery-form">
                    <div class="form-group">
                        <input type="email" placeholder="Введите вашу почту" class="form-control" id="recovery-email" name="recovery-email" required>
                    </div>
                    <button type="button" class="btn btn-primary" id="sendConfirmationCode">Отправить код подтверждения</button>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="confirmationCodeModal" tabindex="-1" role="dialog" aria-labelledby="confirmationCodeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmationCodeModalLabel">Подтверждение кода</h5>
            </div>
            <div class="modal-body">
                <form class="confirmation-code-form">
                    <div class="form-group">
                        <label for="confirmation-code">Введите код подтверждения</label>
                        <input type="text" class="form-control" id="confirmation-code" name="confirmation-code" required>
                    </div>
                    <div class="form-group">
                        <label for="new-password">Новый пароль</label>
                        <div class="input-group">
                            <input type="password" class="form-control pwd" id="new-password" name="new-password" required>
                            <div class="input-group-append">
                                <span class="input-group-text reveal" onclick="togglePassword('new-password')"><i class="fa fa-eye"></i></span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="confirm-new-password">Подтвердите новый пароль</label>
                        <div class="input-group">
                            <input type="password" class="form-control pwd" id="confirm-new-password" name="confirm-new-password" required>
                            <div class="input-group-append">
                                <span class="input-group-text reveal" onclick="togglePassword('confirm-new-password')"><i class="fa fa-eye"></i></span>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary" id="confirmCodeButton">Подтвердить</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('.register-link').on('click', function (e) {
            e.preventDefault();
            $('#loginModal').modal('hide');
            $('#registerModal').modal('show');
        });

        $('.password-recovery-link').on('click', function (e) {
            e.preventDefault();
            $('#loginModal').modal('hide');
            $('#passwordRecoveryModal').modal('show');
        });

        $('#sendConfirmationCode').on('click', function () {
            var email = $('#recovery-email').val();

            $.ajax({
                url: 'send_confirmation_code.php',
                method: 'POST',
                data: { email: email },
                success: function (response) {
                    if (response === 'success') {
                        Swal.fire({
                            title: 'Успешно!',
                            text: 'Код подтверждения отправлен на указанный адрес электронной почты.',
                            icon: 'success',
                        });

                        $('#passwordRecoveryModal').modal('hide');
                        $('#confirmationCodeModal').modal('show');
                    } else {
                        Swal.fire({
                            title: 'Ошибка!',
                            text: 'Произошла ошибка при отправке кода подтверждения.',
                            icon: 'error',
                        });
                    }
                },
            });
        });

        $('#confirmCodeButton').on('click', function () {
            var confirmationCode = $('#confirmation-code').val();
            var newPassword = $('#new-password').val();
            var confirmNewPassword = $('#confirm-new-password').val();

            if (newPassword !== confirmNewPassword) {
                Swal.fire({
                    title: 'Ошибка!',
                    text: 'Пароли не совпадают.',
                    icon: 'error',
                });
                return;
            }

            $.ajax({
                url: 'confirm_code_and_change_password.php',
                method: 'POST',
                data: { confirmationCode: confirmationCode, newPassword: newPassword },
                success: function (response) {
                    if (response === 'success') {
                        Swal.fire({
                            title: 'Успешно!',
                            text: 'Пароль успешно изменен.',
                            icon: 'success',
                        });

                        $('#confirmationCodeModal').modal('hide');
                    } else {
                        Swal.fire({
                            title: 'Ошибка!',
                            text: 'Ошибка: ' + response,
                            icon: 'error',
                        });
                    }
                },
            });
        });
    });
    function togglePassword(id) {
        let passwordField = document.getElementById(id);
        let icon = passwordField.nextElementSibling.children[0].children[0];

        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
    $(document).ready(function () {
        $('.login-form').on('submit', function (e) {


            e.preventDefault();

            var email = $('#email').val();
            var password = $('#password').val();

            $.ajax({
                url: 'auth.php',
                method: 'POST',
                data: { email: email, password: password },
                success: function (response) {


                    if (response.status === 'success') {

                        location.reload();
                    } else {
                        Swal.fire({
                            title: 'Ошибка!',
                            text: 'Неправильная почта или пароль.',
                            icon: 'error',
                        });
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {

                }
            });
        });
    });
</script>
</body>

</html>