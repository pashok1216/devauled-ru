<?php
error_log("Including db.php");
class UniqueDatabase {
    private $host = '127.0.0.1'; // Имя хоста или IP-адрес сервера базы данных
    private $dbname = 'devauled'; // Имя базы данных
    private $user = 'rovel'; // Имя пользователя базы данных
    private $password = '12072004Pavel'; // Пароль пользователя базы данных

    public function connect() {
        try {
            $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname . ';charset=utf8';
            $pdo = new PDO($dsn, $this->user, $this->password);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $pdo->exec("set names utf8"); // Установка кодировки UTF-8
            return $pdo;
        } catch (PDOException $e) {
            echo 'Подключение не удалось: ' . $e->getMessage();
            exit;
        }
    }
}

$db = new UniqueDatabase();
$pdo = $db->connect();