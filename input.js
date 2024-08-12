document.addEventListener('DOMContentLoaded', function () {
    const phoneInput = document.getElementById('phone');
    const fullNameInput = document.getElementById('fullName');

    phoneInput.addEventListener('input', function () {
        // Разрешаем начальный плюс и цифры, удаляем все остальные символы
        this.value = this.value.replace(/(?!^\+)[^0-9]/g, '');
    });
    fullNameInput.addEventListener('input', function () {
        // Разрешаем только буквы латинского и кириллического алфавита
        this.value = this.value.replace(/[^a-zA-Zа-яА-ЯёЁ\s]/g, '');
    });
});