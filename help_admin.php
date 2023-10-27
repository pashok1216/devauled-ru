<?php
error_reporting(1);
ini_set('display_errors', '1');
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <title>Чат с администратором</title>

    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
        }

        .container {
            height: 85vh; /* Высота всего вьюпорта */
            display: flex;
            flex-direction: column;
        }

        .card {
            flex: 1; /* Занимает все доступное пространство */
            display: flex;
            flex-direction: column;
        }

        .card-body {
            flex: 1;
            overflow-y: scroll;
        }

        #chatBox {
            display: flex;
            flex-direction: column-reverse;
        }

        .admin-message p {
            max-width: 80%; /* Максимальная ширина сообщения */
            display: inline-block;
            background-color: #f1f1f1;
            border-radius: 10px;
            padding: 10px;
            margin: 5px;
            float: right;
            clear: both;
        }

        .user-message p {
            max-width: 80%; /* Максимальная ширина сообщения */
            display: inline-block;
            background-color: #e6e6e6;
            border-radius: 10px;
            padding: 10px;
            margin: 5px;
            float: left;
            clear: both;
        }
        @media (max-width: 768px) {
            .admin-message p, .user-message p {
                max-width: 100%;
            }
        }
    </style>

</head>
<body>
<select id="chatIdSelect">
    <!-- Заполните этот список доступными chat_id -->
</select>
<div class="container mt-5">
    <div class="card">
        <div class="card-header">
            Чат с пользователем
        </div>
        <div class="card-body chat-box" id="chatBox" style="height:300px; overflow-y:scroll;">
            <!-- Сообщения будут добавляться здесь динамически -->
        </div>
        <div class="card-footer">
            <input type="text" class="form-control" id="messageInput" placeholder="Введите ваше сообщение">
            <button class="btn btn-primary mt-2" id="sendButton">Отправить</button>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script>
    // Функция для загрузки доступных chat_id
    function fetchChatIds() {
        $.ajax({
            url: 'fetch_chat_ids.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                if (Array.isArray(data)) {
                    let optionsHtml = '';
                    data.forEach(function(item) {
                        const chatId = item.chat_id;
                        optionsHtml += `<option value="${chatId}">${chatId}</option>`;
                    });
                    $('#chatIdSelect').html(optionsHtml);
                } else {
                    console.error('Unexpected response format');
                }
            }
        });
    }

    // Функция для загрузки сообщений
    function fetchMessages(chatId) {
        $.ajax({
            url: 'admin_fetch_messages.php',
            type: 'GET',
            data: { chat_id: chatId },
            success: function(data) {
                console.log("Received data:", data);
                if (typeof data !== 'string' || (data[0] !== '{' && data[0] !== '[')) {
                    console.error('Invalid server response:', data);
                    return;
                }
                try {
                    const response = JSON.parse(data);
                    if (response.status === 'error') {
                        console.error(response.message);
                    } else if (Array.isArray(response)) {
                        let html = '';
                        response.forEach(message => {
                            const alignClass = (message.sender_id === currentAdminId) ? 'admin-message' : 'user-message';
                            html += `<div class="${alignClass}"><p><strong>${message.First_name}:</strong> ${message.message_text}</p></div>`;
                        });
                        $('#chatBox').html(html);
                    } else {
                        console.error('Unexpected response format');
                    }
                } catch (e) {
                    console.error('Error parsing server response', e);
                }
            },
            error: function() {
                console.error('Error fetching messages');
            }
        });
    }

    // Загрузка доступных chat_id и установка обработчиков при загрузке страницы
    $(document).ready(function() {
        fetchChatIds();

        $('#chatIdSelect').change(function() {
            const selectedChatId = $(this).val();
            console.log("Selected Chat ID:", selectedChatId); // Добавьте эту строку для отладки
            fetchMessages(selectedChatId);
        });

        $('#sendButton').on('click', function(e) {
            e.preventDefault();
            const message = $('#messageInput').val();
            const chatId = $('#chatIdSelect').val();  // Получите chat_id из выпадающего списка
            $.ajax({
                url: 'admin_send_message.php',
                type: 'POST',
                data: { message: message, chat_id: chatId },  // Передайте chat_id
                success: function(data) {
                    console.log(data);
                    fetchMessages(chatId);  // Обновите чат
                    $('#messageInput').val('');  // Очистите поле ввода
                },
                error: function() {
                    console.error('Error sending message');
                }
            });
        });
    });
    const currentAdminId = <?php echo json_encode($_SESSION['user_id']); ?>;
    function scrollToBottom() {
        const chatBox = document.getElementById("chatBox");
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    // вызывайте эту функцию после добавления каждого нового сообщения
    scrollToBottom();
</script>
</body>
</html>