$(document).ready(function () {
    // Перемещаем header на своё место
    $('.header').css('transform', 'translateY(0)');

    // Функция для скроллинга к элементу с учётом header на мобильных устройствах
    function scrollToElement(element) {
        var elementOffset = element.offset().top;
        var elementHeight = element.outerHeight();
        var windowHeight = $(window).height();
        var headerHeight = $(window).width(); //<= 1040 ? $('.header').outerHeight() + 100 : 0;
        var headerHeight = $(window).width() <= 1040 ? $('.header').outerHeight() + 100 : 0;
        //var headerHeight = ($(window).width() <= 1040 || windowHeight < 860) ? $('.header').outerHeight() + 100 : $('.header').outerHeight();
        var scrollPosition = elementOffset - ((windowHeight - headerHeight) / 2) + (elementHeight / 2);

        $('html, body').animate({
            scrollTop: scrollPosition - headerHeight
        }, 800); // Продолжительность анимации скролла в мс
    }

    // Анимация появления form-container и плавный скролл к ней
    setTimeout(function () {
        $('.form-container').css({
            'opacity': '1',
            'transform': 'translateY(0)'
        });
        scrollToElement($('.form-container')); // Вызываем функцию скроллинга к .form-container
    }, 100); // Задержка перед началом анимации

    // Показываем баннеры после загрузки страницы
    $('.banner, .banner-second').css('opacity', '1');
});
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('.form-container form');
    const messageDiv = document.createElement('div');
    form.appendChild(messageDiv);

    form.addEventListener('submit', function (event) {
        event.preventDefault(); // Предотвратим стандартную отправку формы

        // Собираем данные из полей формы по идентификаторам
        const formData = {
            fullName: document.getElementById('fullName').value,
            birthDate: document.getElementById('birthDate').value,
            phone: document.getElementById('phone').value,
            forWhom: [],
            dancedBefore: document.querySelector('input[name="dancedBefore"]:checked').value,
            directions: document.getElementById('directions').value,
            additional: document.getElementById('additional').value
        };

        // Проверяем выбранные чекбоксы
        document.querySelectorAll('input[name="forWhom"]:checked').forEach((checkbox) => {
            formData.forWhom.push(checkbox.value);
        });

        // Отправляем данные в формате JSON
        fetch('functions/submit_application.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        })
            .then(response => response.json()) // Убедимся, что ответ преобразуется в JSON
            .then(data => {
                if (data.success) {
                    console.log('Success:', data.success);
                    messageDiv.textContent = 'Форма успешно отправлена!';
                    messageDiv.className = 'success-message';
                } else if (data.error) {
                    console.error('Error:', data.error);
                    messageDiv.textContent = 'Ошибка при отправке формы: ' + data.error;
                    messageDiv.className = 'error-message';
                }
            })
            .catch((error) => {
                console.error('Error:', error);
                messageDiv.textContent = 'Ошибка при отправке формы.';
                messageDiv.className = 'error-message';
            });
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const select = document.getElementById('directions');

    function loadDirections() {
        fetch('functions/get_directions.php')
            .then(response => response.json())
            .then(data => {
                while (select.options.length > 1) {
                    select.remove(1);
                }
                data.forEach(direction => {
                    const option = new Option(direction.name, direction.id);
                    select.add(option);
                });
                document.querySelector('.form-group').classList.add('my-form-group');
                select.classList.add('my-select');
            })
            .catch(error => console.error('Ошибка загрузки направлений:', error));
    }
    loadDirections();

    const phoneInput = document.getElementById('phone');
    function handlePhoneInput(event) {
        let input = event.target;
        let cleanedInput = input.value.replace(/[^\d]/g, '');
        if (!cleanedInput.startsWith('7')) {
            cleanedInput = '7' + cleanedInput;
        }
        input.value = '+' + cleanedInput; 
    }

    phoneInput.addEventListener('input', handlePhoneInput);

    if (!phoneInput.value) {
        phoneInput.value = '+7';
    }
});