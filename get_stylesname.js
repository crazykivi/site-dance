$(document).ready(function () {
    $.ajax({
        url: 'functions/get_stylesname.php',
        type: 'GET',
        dataType: 'json',
        success: function (data) {
            var grouped = {};
            data.forEach(function (item) {
                if (!grouped[item.namestyles]) {
                    grouped[item.namestyles] = [];
                }
                grouped[item.namestyles].push(item);
            });

            var content = '';
            Object.keys(grouped).forEach(function (name) {
                content += `<div class="dance-class">
            <img src="img/dance-img/${name}.png" alt="Танец">
            <div class="class-info">
                <h2>${name}</h2>`;
                grouped[name].forEach(function (session, index, array) {
                    if (session.descriptionstyles) {
                        content += `<p class="time">${session.descriptionstyles}</p>`;
                    }
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