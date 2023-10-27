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
<div class="modal fade" id="passwordRecoveryModal" tabindex="-1" role="dialog" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog register-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="varperr">
                <h5 class="modal-title" id="registerModalLabel">Смена пароля</h5>
            </div>
            <div class="modal-body">
                <form class="login-form" method="POST" action="">
                    <div class="form-group row">
                        <div class="col">
                            <label for="registerFirstName">Имя</label>
                            <input type="text"placeholder="Имя" class="form-control" id="registerFirstName" name="first_name" required>
                        </div>
                        <div class="col">
                            <label for="registerLastName">Фамилия</label>
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

                    <button type="submit" class="btn btn-primary" name="login">Войти</button>
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