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

    window.addEventListener('iInstalled', () => showPushDialog())

    function permissionChangeListener(permission) {
        if (permission) {

            jQuery.get("/analytic", {
                t: 'subscribe'
            }, function(data) {});

        }
    }

    OneSignal.Notifications.addEventListener("permissionChange", permissionChangeListener);
});
