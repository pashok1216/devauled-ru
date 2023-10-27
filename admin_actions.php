<?php
class Database {
    private $pdo;

    public function __construct() {
        $this->pdo = new PDO('mysql:host=127.0.0.1;dbname=devauled', 'rovel', '12072004Pavel');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function select($column, $table, $where, $bindings) {
        $stmt = $this->pdo->prepare("SELECT $column FROM $table WHERE $where");
        $stmt->execute($bindings);
        return $stmt;
    }

    public function insert_into($table, $data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $stmt = $this->pdo->prepare("INSERT INTO $table ($columns) VALUES ($placeholders)");
        $stmt->execute($data);
    }

    public function delete($table, $where, $bindings) {
        $stmt = $this->pdo->prepare("DELETE FROM $table WHERE $where");
        $stmt->execute($bindings);
    }
}

$db = new Database();
$message = "";
$messageType = "";

if (isset($_POST['add_admin'])) {
    $first_name = $_POST['first_name']; // Изменили имя переменной
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Хеширование пароля

    $stmt = $db->select('email', 'users', 'email = :email', ['email' => $email]);
    if ($stmt->rowCount() == 0) {
        $db->insert_into('users', [
            'first_name' => $first_name, // Изменили имя поля
            'email' => $email,
            'password' => $password,
            'role' => 'SuperAdmin'
        ]);
        $message = "Пользователь добавлен как админ!";
        $messageType = "success";
    } else {
        $message = "Пользователь с таким email уже существует!";
        $messageType = "error";
    }
}

if (isset($_POST['remove_admin'])) {
    $email = $_POST['email_remove'];
    $stmt = $db->select('email', 'users', 'email = :email AND role = "SuperAdmin"', ['email' => $email]);
    if ($stmt->rowCount() > 0) {
        $db->delete('users', 'email = :email AND role = "SuperAdmin"', ['email' => $email]);
        $message = "Админ удален!";
        $messageType = "success";
    } else {
        $message = "Пользователь с таким email и ролью админ не найден!";
        $messageType = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">

    <title>Редактирование админов</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="sweetalert2-11.7.29/sweetalert2.min.css">
    <script src="sweetalert2-11.7.29/sweetalert2.all.min.js"></script>
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Редактирование админов</h2>
        <a href="/adminpanel.php" class="btn btn-outline-primary">Вернуться в админ-панель</a> <!-- Измените этот URL на ваш реальный путь к админ-панели -->
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Добавить админа</div>
                <div class="card-body">
                    <form method="post" action="">
                        <div class="form-group">
                            <label for="first_name">Имя пользователя:</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Пароль:</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" name="add_admin" class="btn btn-primary">Добавить</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Удалить админа</div>
                <div class="card-body">
                    <form method="post" action="">
                        <div class="form-group">
                            <label for="email_remove">Email админа для удаления:</label>
                            <input type="email" class="form-control" id="email_remove" name="email_remove" required>
                        </div>
                        <button type="submit" name="remove_admin" class="btn btn-danger">Удалить</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<!-- SweetAlert2 -->

<script>
    <?php
    if ($message) {
        echo "Swal.fire({
            icon: '$messageType',
            title: '$message',
            showConfirmButton: true
        });";
    }
    ?>
</script>
</body>
</html>