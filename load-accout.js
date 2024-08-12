
document.addEventListener("DOMContentLoaded", function () {
    // Показать форму при клике на ссылку
    document.getElementById("show-form-button").addEventListener("click", function (event) {
        event.preventDefault(); // Предотвратить переход по ссылке
        loadForm(); // Загрузить и показать форму
    });
    document.getElementById("show-form-button-second").addEventListener("click", function (event) {
        event.preventDefault(); // Предотвратить переход по ссылке
        loadForm(); // Загрузить и показать форму
    });

    // Закрыть форму при клике за ее пределами
    document.getElementById("overlay").addEventListener("click", function (event) {
        if (event.target === this) {
            this.style.display = "none"; // Скрыть затемнение
            this.innerHTML = ""; // Очистить содержимое
        }
    });
});

// Функция для загрузки и отображения формы
function loadForm() {
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "include/login_form.html", true); // Путь к файлу с формой
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            var formContent = xhr.responseText;
            document.getElementById("overlay").innerHTML = formContent; // Поместить форму в затемнение
            document.getElementById("overlay").style.display = "block"; // Показать затемнение
            attachFormSubmitHandler(); // Прикрепить обработчик отправки формы после загрузки формы
        }
    };
    xhr.send();
}

// Функция для добавления обработчика отправки формы
function attachFormSubmitHandler() {
    var form = document.getElementById('employee-form');
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault(); // предотвратить стандартную отправку формы

            const key = document.getElementById('employee-key').value; // получить ключ от пользователя
            const encryptedKey = CryptoJS.SHA256(key).toString(); // шифрование ключа
            console.log("Зашифрованный ключ: ", encryptedKey);

            // Отправить зашифрованный ключ на сервер через AJAX или другой метод
            fetch('functions/login.php', {
                method: 'POST', // метод HTTP
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded', // тип контента
                },
                body: `encrypted-key=${encryptedKey}` // данные, которые мы отправляем
            })
                .then(response => response.json()) // ожидаем JSON ответ от сервера
                .then(data => {
                    if (data.success) {
                        window.location.href = 'list-users.php'; // перенаправить пользователя на другую страницу
                    } else {
                        alert('Неверный ключ! Попробуйте снова.');
                    }
                })
                .catch(error => {
                    console.error('Ошибка:', error);
                });
        });
    } else {
        console.error('Форма не найдена!');
    }
}