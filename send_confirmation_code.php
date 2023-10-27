<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $_SESSION['email'] = $email; // Сохраняем email в сессии
    $confirmationCode = mt_rand(100000, 999999);

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->CharSet = 'UTF-8';
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'yarovpav@gmail.com';
        $mail->Password = 'wqpaixvgnolikdbn';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;


        $mail->setFrom('from@example.com', 'DEVALUED');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Код подтверждения восстановления пароля';
        $mail->Body = 'Ваш код подтверждения: ' . $confirmationCode;

        $mail->send();

        $_SESSION['confirmation_code'] = $confirmationCode;
        echo 'success';
    } catch (Exception $e) {
        error_log("Error sending confirmation code: " . $e->getMessage());
        echo 'error';
    }
}
?>
