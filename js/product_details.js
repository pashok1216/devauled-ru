$(document).ready(function() {
    $(".info-tab").click(function() {
        // Убираем активный класс у всех вкладок и блоков
        $(".info-tab").removeClass("active");
        $(".info-content").removeClass("active");

        // Добавляем активный класс к нажатой вкладке
        $(this).addClass("active");

        // Получаем значение data-tab у нажатой вкладки
        var tab = $(this).data("tab");

        // Делаем соответствующий блок контента активным
        $("#" + tab).addClass("active");
    });
});
document.addEventListener("DOMContentLoaded", function() {
    const quantityElement = document.getElementById("quantity");
    const increaseButton = document.getElementById("increase");
    const decreaseButton = document.getElementById("decrease");

    increaseButton.addEventListener("click", function() {
        let currentQuantity = parseInt(quantityElement.textContent, 10);
        currentQuantity += 1;
        quantityElement.textContent = currentQuantity;
    });

    decreaseButton.addEventListener("click", function() {
        let currentQuantity = parseInt(quantityElement.textContent, 10);
        if (currentQuantity > 1) {
            currentQuantity -= 1;
            quantityElement.textContent = currentQuantity;
        }
    });
});
// document.addEventListener('DOMContentLoaded', function() {
//     const heartIcon = document.getElementById('heart-icon');
//
//     heartIcon.addEventListener('click', function() {
//         if (heartIcon.getAttribute('src') === 'images/heart_empty.png') {
//             heartIcon.setAttribute('src', 'images/heart_filled.png');
//         } else {
//             heartIcon.setAttribute('src', 'images/heart_empty.png');
//         }
//     });
// });
document.addEventListener('DOMContentLoaded', function() {
    // Ваш код для обработки количества товаров, избранных и т.д.
    // ...

    const currentColorElement = document.querySelector('#current-color span');

    // Установка текущего цвета из параметров URL при загрузке страницы
    const initialUrl = new URL(window.location);
    const initialSelectedColor = initialUrl.searchParams.get('color');
    if (initialSelectedColor) {
        currentColorElement.textContent = initialSelectedColor;
    }

    const colorCircles = document.querySelectorAll('.color-circle');

    colorCircles.forEach(function(circle) {
        circle.addEventListener('click', function() {
            const colorValue = circle.getAttribute('data-color');
            const colorName = circle.getAttribute('data-color-name');
            currentColorElement.textContent = colorName || 'None';

            // Обновление параметра цвета в URL
            const newUrl = new URL(window.location);
            newUrl.searchParams.set('color', colorValue);
            history.pushState({}, '', newUrl);

            // Перезагрузка страницы с новым параметром цвета
            window.location.reload();
        });
    });

    const currentColorFromURL = initialUrl.searchParams.get('color') || 'Белый';  // Установлено значение по умолчанию 'Белый'

    const matchingCircle = Array.from(colorCircles).find(
        circle => circle.getAttribute('data-color') === currentColorFromURL
    );

    if (matchingCircle) {
        const colorName = matchingCircle.getAttribute('data-color-name');
        currentColorElement.textContent = colorName || 'None';
    }
});
function selectSize(element) {
    // Снимаем выделение со всех кружков
    const circles = document.querySelectorAll('.size-circle');
    circles.forEach(circle => circle.classList.remove('active'));

    // Выделяем выбранный кружок
    element.classList.add('active');
}
$(document).ready(function() {
    // По умолчанию активируем вкладку "Описание"
    $('.info-tab[data-tab="description"]').addClass('active');
    $('#description').addClass('active');

    // Обработчик клика по вкладке
    $(".info-tab").click(function() {
        // Убираем активный класс у всех вкладок и блоков контента
        $(".info-tab, .info-content").removeClass("active");

        // Добавляем активный класс к выбранной вкладке
        $(this).addClass("active");

        // Получаем значение data-tab у выбранной вкладки и делаем соответствующий блок контента активным
        var tab = $(this).data("tab");
        $("#" + tab).addClass("active");
    });
});
function changeMainImage(src, element) {
    // Находим основное изображение по id и меняем его src на src кликнутого изображения
    document.getElementById('main-product-image').src = src;

    // Снимаем класс 'active' со всех маленьких изображений
    const smallImages = document.querySelectorAll('.product-small-image');
    smallImages.forEach(img => img.classList.remove('active'));

    // Добавляем класс 'active' на выбранное маленькое изображение
    element.classList.add('active');
}
// Функция для создания звезд рейтинга
