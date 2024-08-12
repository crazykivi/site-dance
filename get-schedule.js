$(document).ready(function () {
    $.ajax({
        url: 'functions/get_schedule.php',
        type: 'GET',
        dataType: 'json',
        success: function (data) {
            var grouped = {};
            var daysOfWeek = ["ПОНЕДЕЛЬНИК", "ВТОРНИК", "СРЕДА", "ЧЕТВЕРГ", "ПЯТНИЦА", "СУББОТА", "ВОСКРЕСЕНЬЕ"];

            // Группировка данных по namestyles и dayschedule
            data.forEach(function (item) {
                if (!grouped[item.namestyles]) {
                    grouped[item.namestyles] = {};
                }
                if (!grouped[item.namestyles][item.dayschedule]) {
                    grouped[item.namestyles][item.dayschedule] = [];
                }
                grouped[item.namestyles][item.dayschedule].push(item);
            });

            // Формирование HTML с учетом групп
            var content = '';
            Object.keys(grouped).forEach(function (name) {
                content += `<div class="dance-class">
                <img src="img/dance-img/${name}.png" alt="Танец">
                <div class="class-info">
                    <h2>${name}</h2>`;

                // Сортировка дней по порядку в daysOfWeek
                var sortedDays = Object.keys(grouped[name]).sort(function (a, b) {
                    return daysOfWeek.indexOf(a) - daysOfWeek.indexOf(b);
                });

                sortedDays.forEach(function (day) {
                    var sessions = grouped[name][day];
                    var times = sessions.map(session => session.timeschedule).join(', ');
                    var choreographers = sessions.map(session => session.namechoreographer).join(', ');
                    content += `<p class="day">${day}</p>
                    <p class="time">${times}</p>
                    <p class="choreographer">${choreographers}</p>`;
                    content += `<div class="class-separator"></div>`;
                });

                content += `</div></div>`;
            });

            $('#dance-classes').html(content);
            $('#dance-classes').css('display', 'flex');
            setTimeout(function () {
                $('#dance-classes').css('opacity', 1);
            }, 100); // небольшая задержка для начала анимации
        }
    });
});