<?php
session_start();
require_once 'config.php';
require_once 'db.php';
require_once 'index.php';




// Проверка отправки формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получение данных из формы
    $orderID = $_POST['order_id'];
    $newStatus = $_POST['new_status'];

    // Подключение к базе данных
    $connection = getDBConnection();

    // Проверка подключения
    if ($connection->connect_error) {
        die("Ошибка подключения к базе данных: " . $connection->connect_error);
    }

    // Подготовленный SQL-запрос для обновления статуса заказа
    $sql = "UPDATE orders SET status = ? WHERE id = ?";
    $statement = $connection->update($sql);
    $statement->bind_param("si", $newStatus, $orderID);

    // Выполнение запроса
    if ($statement->execute()) {
        echo "Статус заказа успешно обновлен.";
    } else {
        echo "Ошибка при обновлении статуса заказа: " . $statement->error;
    }


}
?>

<div class="container">
    <h1>Список заказов</h1>
    <a href="/home">Выйти</a>
    <?php
    // Подключение к базе данных
    $connection = getDBConnection();

    // Проверка подключения
    if ($connection->connect_error) {
        die("Ошибка подключения к базе данных: " . $connection->connect_error);
    }

    // SQL-запрос для получения заказов
    $sql = "SELECT * FROM orders";

    // Выполнение запроса
    $result = $connection->query($sql);

    // Проверка наличия заказов
    if ($result->num_rows > 0) {
        foreach($result->rows as $row){
            ?>
            <div class="order">
                <div class="order-header">
                    <h2>Заказ № <?php echo $row['order_id']; ?></h2>
                    <p>Дата и время заказа: <?php echo $row['order_date']; ?></p>
                </div>
                <div class="order-details">
                    <p>Статус заказа: <?php echo $row['status']; ?></p>
                    <p>Товар: <?php echo $row['product_name']; ?></p>
                    <p>Цена: <?php echo $row['price']; ?></p>
                    <form method="post" action="">
                        <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                        <select name="new_status">
                            <option value="Заказан" <?php if ($row['status'] == 'Заказан') echo 'selected'; ?>>Заказан</option>
                            <option value="Подтвержден" <?php if ($row['status'] == 'Подтвержден') echo 'selected'; ?>>Подтвержден</option>
                            <option value="Отправлен" <?php if ($row['status'] == 'Отправлен') echo 'selected'; ?>>Отправлен</option>
                            <option value="Отменен" <?php if ($row['status'] == 'Отменен') echo 'selected'; ?>>Отменен</option>
                        </select>
                        <button type="submit" class="btn btn-dark">Изменить статус</button>
                    </form>
                </div>
            </div>
            <?php
        }
    } else {
        echo "<p>Нет доступных заказов.</p>";
    }

    // Закрытие соединения с базой данных
    $connection->close();
    ?>
</div>




