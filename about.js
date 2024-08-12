$(document).ready(function () {
    $('.header').css('transform', 'translateY(0)');

    function scrollToElement(element) {
        var elementOffset = element.offset().top;
        var elementHeight = element.outerHeight();
        var windowHeight = $(window).height();
        var headerHeight = $(window).width() <= 1040 ? $('.header').outerHeight() + 100 : 0;
        var scrollPosition = elementOffset - ((windowHeight - headerHeight) / 2) + (elementHeight / 2);

        $('html, body').animate({
            scrollTop: scrollPosition - headerHeight
        }, 800); 
    }
    setTimeout(function () {
        $('.form-container').css({
            'opacity': '1',
            'transform': 'translateY(0)'
        });
        scrollToElement($('.form-container'));
    }, 100); 

    $('.banner, .banner-second').css('opacity', '1');
});