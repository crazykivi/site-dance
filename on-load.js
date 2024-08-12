window.onbeforeunload = function () {
    window.scrollTo(0, 0);
};

var rellax = new Rellax('.rellax', {
    speed: -2,
    center: false,
    wrapper: null,
    round: true,
    vertical: true,
    horizontal: false
});

document.addEventListener('DOMContentLoaded', function () {
    var btn = document.querySelector('.menu-btn');
    var menu = document.querySelector('.mobile');

    btn.addEventListener('click', function () {
        menu.classList.toggle('active'); // Добавляем или удаляем класс 'active'
    });
});

$(document).ready(function () {
    $('.header').css('transform', 'translateY(0)');
    $('.content').css({
        'opacity': '1',
        'transform': 'translateY(0)'
    });
    $('body h2').css({
        'opacity': '1',
        'transform': 'translateY(0)'
    });
    $('.banner, .banner-second').css('opacity', '1');
});