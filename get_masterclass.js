$(document).ready(function () {
    $.ajax({
        url: 'functions/get_masterclass.php',
        type: 'GET',
        dataType: 'json',
        success: function (data) {
            // Отбор предстоящих мастер-классов
            var currentDate = new Date();
            var upcomingMasterclasses = data.filter(function (masterclass) {
                var eventDate = new Date(masterclass.dateventmasterclass);
                return eventDate > currentDate;
            });

            // Сортировка мастер-классов по дате
            upcomingMasterclasses.sort(function (a, b) {
                return new Date(a.dateventmasterclass) - new Date(b.dateventmasterclass);
            });

            // Формирование HTML с учетом отсортированных и отфильтрованных данных
            var content = '';
            upcomingMasterclasses.forEach(function (masterclass) {
                content += `<div class="dance-class">
            <img src="${masterclass.imagePath}" alt="Мастер-класс">
            <div class="class-info">
                <h2>${masterclass.namemasterclass}</h2>
                <p class="description">${masterclass.descriptionmasterclass}</p>
                <p class="date">${formatDate(masterclass.dateventmasterclass)}</p>
            </div>
        </div>`;
            });

            $('#dance-classes').html(content);
            $('#dance-classes').css('display', 'flex');
            setTimeout(function () {
                $('#dance-classes').css('opacity', 1);
            }, 100); // небольшая задержка для начала анимации
        }
    });
});

function formatDate(dateString) {
    var date = new Date(dateString);
    var options = { year: 'numeric', month: 'long', day: 'numeric', hour: 'numeric', minute: 'numeric' };
    return date.toLocaleDateString('ru-RU', options);
}