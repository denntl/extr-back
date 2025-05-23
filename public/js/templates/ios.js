$(document).ready(function () {
    let installButton =  $('#install-button');
    let loading =  $('.loading');
    let progress =  $('.progress_word');
    let runner = $('.runner')

    $(window).on('startInstalling', function (event) {
        loading.hide()
        installButton.show()
        installButton.text(installButton.data('download'))
        installButton.prop('disabled', true)
    })

    $(window).on('pendingInstalling', function (event) {
        installButton.hide()
        loading.show()
        progress.text(event.detail.text)
        runner.css('width', event.detail.percent)
    })

    $(window).on('runInstalling', function (event) {
        installButton.hide()
        loading.show()
        progress.text(event.detail.text)
        runner.css('width', event.detail.percent)
    })

    $(window).on('stopInstalling', function (event) {
        loading.hide()
        installButton.show()
        installButton.text(installButton.data('installing'))
        installButton.prop('disabled', true)
        setTimeout((() => {
            installButton.text(installButton.data('open'))
            loading.hide()
            installButton.show()
            installButton.prop('disabled', false)
        }), 2e3)
    })

    $('.appDownload__loadBtn').click(function() {
        $('.appDownload__loadBtn').animate({width:0, opacity: 0 }, 1500);
        $('.appDownload__loadBtn').fadeOut();
        $('.appDownload__warning').animate({width:0, opacity: 0}, 1500);
        $('.appDownload__warning').fadeOut();
        setTimeout((() => {
            $('.custom-loader').fadeIn();
            setTimeout((() => {
                $('.custom-loader').fadeOut();
                $('.appDownload__warning').fadeIn();
                $('.appDownload__warning').animate({width:40, opacity: 1}, 1500);
                $('.appDownload__loadBtn').fadeIn();
                $('.appDownload__loadBtn').animate({width:90, opacity: 1 }, 1500);
            }), 3000);
        }), 1500);
    })

    $(window).scroll(function() {
        if ($(window).scrollTop() >= 200) {
            $('.appMobHeader').fadeIn();
            $('.appMobHeader').css({ 'display': 'grid' });
        } else {
            $('.appMobHeader').fadeOut();
        }
    })

    $('.commentsReturn').click(function() {
        $('.appReview__comments').fadeOut();
    })

    $('.appReview__allComments').click(function() {
        $('.appReview__comments').fadeIn();
        $('.appReview__comments').addClass('opened');
    })
})
