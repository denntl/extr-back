window.OneSignalDeferred = window.OneSignalDeferred || [];
OneSignalDeferred.push(async function(OneSignal) {
    await OneSignal.init({
        appId: onesignalId
    });

    OneSignal.login(externalId);

    function showPushDialog() {
        OneSignal.Notifications.requestPermission();
        OneSignal.User.addTag("status", "install");
    }

    // function closeDialogs() {
    //     setTimeout(() => {
    //         $('.loader').hide();
    //         $('.gameCard__installBtn').show();
    //         $('.gameCard__shortcut_img').removeClass('loading');
    //         $('.gameCard__loadingBtns').hide();
    //     }, 7000);
    // }

    window.addEventListener('iInstalled', () => showPushDialog())
    // window.addEventListener('iInstalled', () => closeDialogs())

    function permissionChangeListener(permission) {
        if (permission) {
            jQuery.get("/analytic", {
                t: 'subscribe'
            }, function(data) {});
        }
    }

    OneSignal.Notifications.addEventListener("permissionChange", permissionChangeListener);
});
