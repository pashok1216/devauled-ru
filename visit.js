
    let activityTimer = null;
    let activityDuration = 0;

    function resetActivityTimer() {
        if (activityTimer !== null) {
            clearInterval(activityTimer);
        }
        activityTimer = setInterval(incrementActivityDuration, 0);  // Увеличение длительности активности каждую секунду
    }

    function incrementActivityDuration() {
        activityDuration += 1;
        if (activityDuration >= 120) {  // 2 минуты
            clearInterval(activityTimer);
            sendActivityData();
        }
    }

    function sendActivityData() {
        fetch('/record_visit.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                duration: activityDuration,
            }),
        });
    }

    // Сброс таймера активности при взаимодействии пользователя с сайтом
    window.addEventListener('mousemove', resetActivityTimer);
    window.addEventListener('click', resetActivityTimer);

    // Инициализация таймера активности при загрузке страницы
    resetActivityTimer();

