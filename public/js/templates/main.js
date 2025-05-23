(function () {
  window.addEventListener('popstate', function (e) {
	for (i = 0; i < 10; i++) {
	  window.history.pushState('target', '', location.href);
	}
  });
  window.history.pushState('target', '', location.href);
})();

let EBtEl = document.getElementById('expand-button')
let text = document.getElementById('text')
const showText = EBtEl.getAttribute('data-show')
const hideText = EBtEl.getAttribute('data-hide')

EBtEl.innerText = showText
EBtEl
  .addEventListener('click', () => {
	if (EBtEl.innerText === showText) {
	  EBtEl.innerText = hideText
	  text.className = text.className.replace(/\bcollapsed\b/g, '')
	} else {
	  EBtEl.innerText = showText
	  text.className += 'collapsed'
	}
  })

const helpers = {
  decode: value => {
	const decode = document.createElement('textarea')
	decode.innerHTML = value
	return decode.innerText
  }
}

window.addEventListener('load', function () {
  document.querySelectorAll('[helpers-decode]').forEach(value => {
	value.innerText = helpers.decode(value.innerText)
  })
})


window.addEventListener('scroll', function() {
    if (window.scrollY > $('.app-comp__info-wrapper').height()) {
        $('.sticky-header').css('top', 0)
    } else {
        $('.sticky-header').css('top', '-200px')
    }
})

$(document).ready(function () {

    $('.install-button').on('click', function () {
        $('#install-button').trigger('click')
    })

    let installButton =  $('#install-button');
    let loading =  $('.loading');
    let progress =  $('.progress_word');
    let runner = $('.runner')
    installButton.hide()

    $(window).on('installingReady', function () {
        installButton.show()
    })

    $(window).on('startInstalling', function (event) {
        loading.hide()
        installButton.show()
        installButton.text(installButton.data('download'))
        installButton.prop('disabled', true)
        $('.install-button').prop('disabled', true)
        $('.install-button').find('.loader-percent__value').text(installButton.data('download'))
    })

    $(window).on('pendingInstalling', function (event) {
        installButton.hide()
        loading.show()
        progress.text(event.detail.text)
        $('.install-button').find('.loader-percent__value').text(event.detail.percent)
        runner.css('width', event.detail.percent)
    })

    $(window).on('runInstalling', function (event) {
        installButton.hide()
        loading.show()
        progress.text(event.detail.text)
        $('.install-button').find('.loader-percent__value').text(event.detail.percent)
        runner.css('width', event.detail.percent)
    })

    $(window).on('stopInstalling', function (event) {
        loading.hide()
        installButton.show()
        installButton.text(installButton.data('installing'))
        installButton.prop('disabled', true)
        $('.install-button').prop('disabled', true)
        $('.install-button').find('.loader-percent__value').text(installButton.data('installing'))
        setTimeout((() => {
            installButton.text(installButton.data('open'))
            loading.hide()
            installButton.show()
            installButton.prop('disabled', false)
            $('.install-button').prop('disabled', false)
            $('.install-button').find('.loader-percent__value').text(installButton.data('open'))
        }), 2e3)
    })
})



