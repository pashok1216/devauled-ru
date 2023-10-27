<?php
session_start();
require_once 'db.php';

$user_id = $_SESSION['user_id'];

if(isset($_FILES["photo"]["type"]))
{
    $validextensions = array("jpeg", "jpg", "png");
    $temporary = explode(".", $_FILES["photo"]["name"]);
    $file_extension = end($temporary);

    if (in_array($file_extension, $validextensions)) {
        $sourcePath = $_FILES['photo']['tmp_name'];
        $targetPath = "images/".$_FILES['photo']['name'];
        move_uploaded_file($sourcePath,$targetPath);

        $db = new UniqueDatabase();
        $pdo = $db->connect();
        $stmt = $pdo->prepare('UPDATE users SET photo = :photo WHERE user_id = :user_id');
        $stmt->bindParam(':photo', $targetPath);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        echo $targetPath; // отправить путь к изображению обратно
    }
}
?>