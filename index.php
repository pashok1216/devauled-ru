<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'header.php';
require_once 'db.php';
require_once 'config.php';
require_once 'record_visit.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>

    <link rel="stylesheet" type="text/css" href="css/index.css">
    <script src="visit.js"></script>
    <meta charset="UTF-8">
    <title>Devauled</title>
    <style>
        .background {
            background-image: url('images/img.jpg');
            background-size: cover;
            background-repeat: no-repeat;
        }
    </style>
</head>
<body class="background">
<div class="container">
    <img src="images/650c9ac5790ed.jpg" alt="3D Image" class="image3D">
</div>
<a href="catalog.php?class=sweatshirts">Толстовки</a>
<a href="catalog.php?class=jeans">Джинсы</a>
</div>
<footer>
    <?php
    require_once 'footer.php';
    ?>
</footer>
</body>
</html>