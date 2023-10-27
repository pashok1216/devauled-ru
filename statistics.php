<?php
// Подключение к базе данных
$db = new mysqli('127.0.0.1', 'rovel', '12072004Pavel', 'devauled');

// Проверка соединения
if ($db->connect_error) {
    die("Ошибка подключения: " . $db->connect_error);
}

// Получение данных о продажах
$sales_result = $db->query("SELECT date, sales FROM sales_data");
$sales_data = [];
while ($row = $sales_result->fetch_assoc()) {
    $sales_data[] = $row;
}

// Получение данных о посещениях и агрегирование по дате
$visits_result = $db->query("SELECT DATE(visit_date) as date, COUNT(*) as visits FROM visits_data GROUP BY DATE(visit_date)");
$visits_data = [];
while ($row = $visits_result->fetch_assoc()) {
    $visits_data[] = $row;
}

// Закрытие соединения с базой данных
$db->close();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Статистика</title>
</head>
<body>
<canvas id="sales-chart"></canvas>
<canvas id="visits-chart"></canvas>

<!-- Подключение библиотеки Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', (event) => {
        // Получение данных из PHP
        const salesDataPHP = <?php echo json_encode($sales_data); ?>;
        const visitsDataPHP = <?php echo json_encode($visits_data); ?>;

        // Преобразование данных в формат, подходящий для Chart.js
        const salesLabels = salesDataPHP.map(item => item.date);
        const salesValues = salesDataPHP.map(item => Number(item.sales));  // Преобразование в числа
        const visitsLabels = visitsDataPHP.map(item => item.date);
        const visitsValues = visitsDataPHP.map(item => Number(item.visits));  // Преобразование в числа

        // Создание графика продаж
        const salesCtx = document.getElementById('sales-chart').getContext('2d');
        new Chart(salesCtx, {
            type: 'bar',  // Изменено с 'line' на 'bar'
            data: {
                labels: salesLabels,
                datasets: [{
                    label: 'Продажи',
                    data: salesValues,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Дата'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Продажи'
                        }
                    }
                }
            }
        });

        // Создание графика посещений
        const visitsCtx = document.getElementById('visits-chart').getContext('2d');
        new Chart(visitsCtx, {
            type: 'bar',  // Изменено с 'line' на 'bar'
            data: {
                labels: visitsLabels,
                datasets: [{
                    label: 'Посещения',
                    data: visitsValues,
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                    borderColor: 'rgba(153, 102, 255, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Дата'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Посещения'
                        }
                    }
                }
            }
        });
    });
</script>
</body>
</html>