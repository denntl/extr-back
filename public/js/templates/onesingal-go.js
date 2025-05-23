window.OneSignalDeferred = window.OneSignalDeferred || [];
window.addEventListener("serviceWorkerRegistration", (async () => {

}))
try {
    const uaParser = new UAParser;
    OneSignalDeferred.push(async function(OneSignal) {
        if (!onesignalId) {
            window.location.href = redirectLink;
        }
        console.log('[GO] Onesignal init')
        await OneSignal.init({
            appId: onesignalId,
            // serviceWorkerParam: { scope: "/go" },
        });

        function redirectToOffer() {
            console.log('[GO] Redirect to offer')
            // window.location.href = redirectLink;
        }

        function redirectOnGuessBlocked() {
            if (!OneSignal.Notifications.permission) {
                console.log('[GO] Guess user blocked notifications')
                redirectToOffer()
            }
        }

        function redirectOnBlocked() {
            console.log('[GO] Check if user blocked notifications')
            if (OneSignal.Notifications.permissionNative === 'denied') {
                console.log('[GO] User blocked notifications')
                redirectToOffer()
            }
        }

        console.log('[GO] Permission native', OneSignal.Notifications.permissionNative)
        redirectOnBlocked()

        console.log(uaParser.getResult())
        if ("iOS" === uaParser.getOS().name || "Android" === uaParser.getOS().name) {
            console.log('[GO] Login user: ', clientId);
            await OneSignal.login(clientId);
            let redirectTimeout = null;
            let redirectInterval = null;

            console.log('[GO] Notification permission:', OneSignal.Notifications.permission)
            console.log('[GO] User id:', OneSignal.User.onesignalId)
            console.log('[GO] Push sub id:', OneSignal.User.PushSubscription.id)

            OneSignal.User.addEventListener('change', async function (event) {
                console.log('[GO] User changed:', { event });
                setTimeout(() => {
                    redirectToOffer()
                }, 2000)
            });

            if (OneSignal.User.onesignalId && OneSignal.User.PushSubscription.id) {
                console.log('[GO] User already subscribed')
                let tags = await OneSignal.User.getTags()
                if (!tags?.status) {
                    console.log('[GO] Setting status tag install');
                    await OneSignal.User.addTag("status", "install");
                    tags = await OneSignal.User.getTags()
                    console.log('[GO] User tags', tags)
                    setTimeout(() => {
                        redirectToOffer()
                    }, 2000)
                } else {
                    redirectToOffer()
                }
            }

            OneSignal.Notifications.addEventListener('permissionChange', async function (event) {
                console.log('[GO] permissionChange', event)
                clearInterval(redirectInterval)
                clearTimeout(redirectTimeout)
                if (event) {
                    await jQuery.get("/analytic", {
                        com: applicationId,
                        t: 'subscribe'
                    }, function(data) {});
                }
                let tags = OneSignal.User.getTags()
                if (!tags?.status) {
                    console.log('[GO] Setting status tag install')
                    await OneSignal.User.addTag("status", "install");
                }
                tags = await OneSignal.User.getTags()
                console.log('[GO] User tags', tags)
            })

            OneSignal.Notifications.addEventListener('permissionPromptDisplay', function (event) {
                console.log('[GO] permissionPromptDisplay', event)
                clearTimeout(redirectTimeout)
                redirectInterval = setInterval(redirectOnBlocked, 500)
            })
            OneSignal.Notifications.addEventListener('click', function (event) {
                console.log('[GO] click', event)
            })
            OneSignal.Slidedown.addEventListener('slidedownShown', function (event) {
                console.log('[GO] slidedownShown', { event });
                clearTimeout(redirectTimeout)
            });
            OneSignal.Slidedown.addEventListener('slidedownCancelClick', function (event) {
                console.log('[GO] slidedownCancelClick', { event });
                redirectOnGuessBlocked()
            });

            if (!OneSignal.User.PushSubscription.id || OneSignal.Notifications.permission) {
                await OneSignal.Notifications.requestPermission();
                // OneSignal.Slidedown.promptPush()
                // redirectTimeout = setTimeout(redirectOnGuessBlocked, 5000)
            }
        } else {
            console.log('Onesignal login')
            await OneSignal.login(clientId);
            const tags = OneSignal.User.getTags()
            console.log('User tags')
            console.log(tags)
            if (!tags?.status) {
                console.log('Setting status tag install')
                await OneSignal.User.addTag("status", "install");
            }
            console.log('Notification permission:', OneSignal.Notifications.permission)
            console.log('User id:', OneSignal.User.onesignalId)
            console.log('Notification sub id:', OneSignal.User.PushSubscription.id)
            if (OneSignal.Notifications.permission) {
                if (!OneSignal.User.PushSubscription.id) {
                    window.location.reload()
                    return
                } else {
                    setTimeout( async () => {
                        console.log('Redirect')
                        // window.location.href = redirectLink;
                    }, 500)
                }
            }
            setTimeout( async () => {
                console.log('Request permission')
                await OneSignal.Notifications.requestPermission();
                await console.log('Result permission', OneSignal.Notifications.permission)
                if (!OneSignal.Notifications.permission) {
                    console.log('Redirect')
                    // window.location.href = redirectLink;
                }
            }, 500)


            async function permissionChangeListener(permission) {
                console.log('Permission changed:', permission)
                if (permission) {
                    await jQuery.get("/analytic", {
                        com: applicationId,
                        t: 'subscribe'
                    }, function(data) {});
                    setTimeout( async () => {
                        console.log('Redirect')
                        // window.location.href = redirectLink;
                    }, 500)
                } else {
                    console.log('Redirect')
                    // window.location.href = redirectLink;
                }
            }

            OneSignal.Notifications.addEventListener("permissionChange", permissionChangeListener);
        }
    });
} catch (e) {
    window.location.href = redirectLink;
}

