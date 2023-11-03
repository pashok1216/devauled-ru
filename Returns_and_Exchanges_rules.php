<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Скоро будет доступно</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f4f4f4;
            font-family: 'Arial', sans-serif;
        }
        .coming-soon {
            text-align: center;
        }
        .spinner-grow {
            width: 3rem;
            height: 3rem;
            margin-bottom: 15px;
        }
        @keyframes appear {
            0% {
                opacity: 0;
                transform: translateY(-20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-appear {
            animation: appear 1.5s ease-in-out forwards;
        }
        .back-button {
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="coming-soon">
    <div class="spinner-grow text-primary" role="status">
        <span class="sr-only">Загрузка...</span>
    </div>
    <h1 class="animate-appear">Мы работаем над чем-то удивительным!</h1>
    <p class="lead animate-appear">Оставайтесь с нами, впереди много интересного.</p>
    <button class="btn btn-primary back-button" onclick="history.back()">Вернуться назад</button>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>