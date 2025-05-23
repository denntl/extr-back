window.addEventListener('scroll', function() {
    if (window.scrollY > $('.gameCard').height()) {
        $('.sticky-header').css('top', 0)
    } else {
        $('.sticky-header').css('top', '-200px')
    }
})

$(document).ready(function(){

    let selectTheme = $('head').data('theme');

    $('.install-button').on('click', function () {
        $('#install-button').trigger('click')
    })

    // GALLERY

    $('.gameGallery').slick({
        infinite: true,
        slidesToShow: 2,
        slidesToScroll: 1,
        arrows: false,
        variableWidth: true,
    })

    $('.gameReviews__feedbackBtn').on('click', function () {
        if ($(this).data('feedback') === 'positive_gameReview1' && !$(this).hasClass('clicked')) {
            const reviews = $(this).closest('.gameReviews__reviewer').find('.gameReviews__feedback_UsersFoundHelpful>span');
            const value = reviews.text();
            reviews.text(parseInt(value) + 1);
        }
    })

    function setProgress(percent) {
        const radius = $('.progress-ring__circle').prop('r').baseVal.value;
        const circumference = 2 * Math.PI * radius;

        const offset = 314 - (circumference / 100) * percent ;
        $('.progress-ring__circle').css('strokeDashoffset', offset);
    }


    let installButton =  $('#install-button');
    let progress =  $('.progress_word');
    let runner = $('.runner')


    if (window.self === window.top) {
        $('.gameCard__loadingBtns').fadeOut()
    } else {
        $('.gameCard__loadingBtns').fadeIn()
    }

    $(window).on('installingReady', function () {
        $('.loader').fadeOut()
        $('.gameCard__loadingBtns').fadeIn()
        $('.gameCard__developer').fadeIn()
        $('.progress_container').fadeOut();
        $('.gameCard__shortcut_img').removeClass('loading');
    })

    $(window).on('startInstalling', function (event) {
        $('.loader').fadeIn().css({'display': 'flex'});
        installButton.text(installButton.data('download'))
        installButton.prop('disabled', true)
        $('.install-button').prop('disabled', true)
        $('.install-button').find('.loader-percent__value').text(installButton.data('download'))
    })

    $(window).on('pendingInstalling', function (event) {

        installButton.text(installButton.data('open'))
        installButton.addClass('installing')

        progress.text(installButton.data('pending'))
        $('.install-button').find('.loader-percent__value').text(installButton.data('pending'))

        $('.gameCard__ratings').fadeOut()
        $('.gameCard__developer').fadeOut()
        $('.gameCard__loadingBtn').fadeIn();
        $('.progress_container').fadeIn();
        $('.gameCard__shortcut_img').addClass('loading');
    })

    $(window).on('runInstalling', function (event) {

        progress.text(event.detail.text)
        runner.css('width', event.detail.percent)

        $('.loader').fadeOut();
        $('.progressCircle').fadeIn();
        setProgress(parseFloat(event.detail.percent))
        $('.install-button').find('.loader-percent__value').text(event.detail.percent)
    })

    $(window).on('stopInstalling', function (event) {
        if (event?.detail?.force) {
            installButton.removeClass('installing')
            installButton.text(installButton.data('open'))
            $('.progress_container').fadeOut();
            $('.gameCard__loadingBtn').fadeIn();
            $('.gameCard__developer').fadeIn();

            $('.gameCard__loadingBtns').fadeIn()
            installButton.prop('disabled', false)
            $('.install-button').prop('disabled', false)
            $('.install-button').find('.loader-percent__value').text(installButton.data('open'))
            $('.loader').fadeOut();
            $('.gameCard__shortcut_img').removeClass('loading');
            return
        }
        installButton.text(installButton.data('open'))
        installButton.prop('disabled', true)
        $('.gameCard__loadingBtn').fadeIn();
        progress.text(installButton.data('installing'))
        $('.install-button').prop('disabled', true)
        $('.install-button').find('.loader-percent__value').text(installButton.data('installing'))
        $('.progressCircle').fadeOut();
        $('.loader').fadeIn().css({'display': 'flex'});

        setTimeout((() => {
            installButton.removeClass('installing')
            installButton.text(installButton.data('open'))
            $('.progress_container').fadeOut();
            $('.gameCard__developer').fadeIn();

            $('.gameCard__loadingBtns').fadeIn()
            installButton.prop('disabled', false)
            $('.install-button').prop('disabled', false)
            $('.install-button').find('.loader-percent__value').text(installButton.data('open'))
            $('.loader').fadeOut();
            $('.gameCard__shortcut_img').removeClass('loading');
        }), 3000)
    })

    // function closeLoader(timer = 2000) {
    //     setTimeout(() => {
    //         $('.loader').hide();
    //         $('.gameCard__installBtn').show();
    //         $('.gameCard__shortcut_img').removeClass('loading');
    //         $('.gameCard__loadingBtns').hide();
    //     }, timer);
    // }

    $('.gameReviews__feedbackBtn').click(function() {
        const btns = $(this).parent().children();
        for (let btn of btns) {
            btn.classList.remove('clicked');
            deleteCookie(btn.dataset.feedback);
        }

        $(this).toggleClass('clicked');
        let coockieBtn = encodeURIComponent($(this).data('feedback'));
        setCookie(coockieBtn, 1);
    })

    function getCookie(name) {
        let matches = document.cookie.match(new RegExp(
            "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
        ));
        return matches ? decodeURIComponent(matches[1]) : undefined;
    }

    function setCookie(name, value, options = {}) {

        options = {
            path: '/',
            ...options
        };

        if (options.expires instanceof Date) {
            options.expires = options.expires.toUTCString();
        }

        let updatedCookie = encodeURIComponent(name) + "=" + encodeURIComponent(value);

        for (let optionKey in options) {
            updatedCookie += "; " + optionKey;
            let optionValue = options[optionKey];
            if (optionValue !== true) {
                updatedCookie += "=" + optionValue;
            }
        }

        document.cookie = updatedCookie;
    }

    function deleteCookie(name) {
        setCookie(name, "", {
            'max-age': -1
        })
    }

    function selectLightTheme() {
        $('.gameCard').removeClass('dark');
        $('.gameHeader').removeClass('dark');
        $('.gameInfo').removeClass('dark');
        $('.gameRequirments').removeClass('dark');
        $('.gameReviews').removeClass('dark');
        //$('body').css({"background-color" : "#ffffff", "color" : "#000000"});
        //$('.lds-ring div').css({ 'border-color': '#0b57cf transparent transparent transparent'})
    }

    function updateProgress() {
        $('.progressBar').each((index,progress) => {
            let width = $(progress).data('amount');
            $(progress).find('.progressThumb').css({ 'width' : `${width}%` });
        })
    }

    function selectDarkTheme() {
        $('.gameCard').addClass('dark');
        $('.gameHeader').addClass('dark');
        $('.gameInfo').addClass('dark');
        $('.gameRequirments').addClass('dark');
        $('.gameReviews').addClass('dark');
        $('body').css({"background-color" : "#1f1f1f", "color" : "#C7C7C7" });
        $('.lds-ring div').css({ 'border-color': '#A8C8FB transparent transparent transparent'})
    }

    switch (selectTheme) {
        case 1:
            break;

        case 2:
            break;

        case 3:
            selectDarkTheme();
            break;

        default:
            selectLightTheme();
    }
    updateProgress()

})
