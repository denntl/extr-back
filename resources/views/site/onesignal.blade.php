@php
    use App\Models\Application;
    use Illuminate\Support\Facades\Storage;

    /**
    * @var $application Application
    * @var $externalId string
    * @var $clientId string
    * @var $link string
    * @var $webhookUrl string
    */

@endphp
    <!DOCTYPE html>
<html lang="{{$application->language}}">
<head>
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>{{$application->app_name}}</title>
    <script>
        const onesignalId = '{{$application->onesignal_id}}';
        const externalId = '{{$externalId}}';
        const clientId = '{{$clientId}}';
        const redirectLink = '{!! $link !!}';
        const applicationId = '{{$application->uuid}}';
    </script>
    <script src="/js/plugins/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
            crossorigin="anonymous"></script>
    <script src="/js/templates/ua-parser.min.js"></script>
    <script src="https://cdn.onesignal.com/sdks/web/v16/OneSignalSDK.page.js" defer></script>
    <script>
        window.OneSignalDeferred = window.OneSignalDeferred || [];
        try {
            const webhookUrl = '{{$webhookUrl}}';
            const uaParser = new UAParser;
            OneSignalDeferred.push(async function (OneSignal) {
                if (!onesignalId) {
                    window.location.href = redirectLink;
                }
                console.log('[GO] Onesignal init')
                await OneSignal.init({
                    appId: onesignalId,
                    serviceWorkerParam: {scope: "/go/"},
                    serviceWorkerPath: "OneSignalSDKWorker.js",
                    webhooks: {
                        cors: false,
                        'notification.willDisplay': webhookUrl,
                        'notification.clicked': webhookUrl,
                        'notification.dismissed': webhookUrl,
                    }
                });

                function redirectToOffer() {
                    console.log('[GO] Redirect to offer')
                    window.location.href = redirectLink;
                }

                function redirectOnGuessBlocked() {
                    if (!OneSignal.Notifications.permission) {
                        console.log('[GO] Guess user blocked notifications')
                        redirectToOffer()
                    }
                }

                function redirectOnBlocked() {
                    console.log('[GO] Check if user blocked notifications')
                    if (Notification?.permission === 'denied') {
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
                    let redirectInterval = null;

                    console.log('[GO] Notification permission:', OneSignal.Notifications.permission)
                    console.log('[GO] User id:', OneSignal.User.onesignalId)
                    console.log('[GO] Push sub id:', OneSignal.User.PushSubscription.id)

                    OneSignal.User.addEventListener('change', async function (event) {
                        console.log('[GO] User changed:', {event});
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
                        if (event) {
                            await jQuery.get("/analytic", {
                                com: applicationId,
                                t: 'subscribe'
                            }, function (data) {
                            });
                        }
                        let tags = OneSignal.User.getTags()
                        if (!tags?.status) {
                            console.log('[GO] Setting status tag install')
                            await OneSignal.User.addTag("status", "install");
                        }
                        tags = await OneSignal.User.getTags()
                        console.log('[GO] User tags', tags)
                        if (!event) {
                            redirectToOffer()
                        }
                    })

                    OneSignal.Notifications.addEventListener('permissionPromptDisplay', function (event) {
                        console.log('[GO] permissionPromptDisplay', event)
                        // redirectInterval = setInterval(redirectOnBlocked, 500)
                    })
                    OneSignal.Notifications.addEventListener('click', function (event) {
                        console.log('[GO] click', event)
                    })
                    OneSignal.Notifications.addEventListener('permissionChangeAsString', async function (event) {
                        console.log('[GO] permissionChangeAsString', event)
                        if (event === 'denied') {
                            let tags = OneSignal.User.getTags()
                            if (!tags?.status) {
                                console.log('[GO] Setting status tag install')
                                await OneSignal.User.addTag("status", "install");
                            }
                            tags = await OneSignal.User.getTags()
                            console.log('[GO] User tags', tags)
                            setTimeout(() => {
                                redirectToOffer()
                            }, 2000)
                        }
                    })
                    OneSignal.Notifications.addEventListener('dismiss', function (event) {
                        console.log('[GO] dismiss', event)
                    })
                    OneSignal.Slidedown.addEventListener('slidedownShown', function (event) {
                        console.log('[GO] slidedownShown', {event});
                    });
                    OneSignal.Slidedown.addEventListener('slidedownCancelClick', function (event) {
                        console.log('[GO] slidedownCancelClick', {event});
                        redirectOnGuessBlocked()
                    });

                    if (!OneSignal.User.PushSubscription.id || OneSignal.Notifications.permission) {
                        await OneSignal.Notifications.requestPermission();
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
                            setTimeout(async () => {
                                console.log('Redirect')
                                window.location.href = redirectLink;
                            }, 500)
                        }
                    }
                    setTimeout(async () => {
                        console.log('Request permission')
                        await OneSignal.Notifications.requestPermission();
                        await console.log('Result permission', OneSignal.Notifications.permission)
                        if (!OneSignal.Notifications.permission) {
                            console.log('Redirect')
                            window.location.href = redirectLink;
                        }
                    }, 500)


                    async function permissionChangeListener(permission) {
                        console.log('Permission changed:', permission)
                        if (permission) {
                            await jQuery.get("/analytic", {
                                com: applicationId,
                                t: 'subscribe'
                            }, function (data) {
                            });
                            setTimeout(async () => {
                                console.log('Redirect')
                                window.location.href = redirectLink;
                            }, 1500)
                        } else {
                            console.log('Redirect')
                            window.location.href = redirectLink;
                        }
                    }

                    OneSignal.Notifications.addEventListener("permissionChange", permissionChangeListener);
                }
            });
        } catch (e) {
            window.location.href = redirectLink;
        }

    </script>
    <style>
        :root, :root.light {
            --lof-bk: #ffffff;
            --lof-lb: #c4c7c5;
            --lof-lf: #0b57d0
        }

        @media (prefers-color-scheme: dark) {
            :root {
                --lof-bk: #131314;
                --lof-lb: #c4c7c5;
                --lof-lf: #a8c7fa
            }
        }

        :root.dark {
            --lof-bk: #131314;
            --lof-lb: #c4c7c5;
            --lof-lf: #a8c7fa
        }

        @keyframes _lof_l1 {
            100% {
                background-size: 100%
            }
        }

        .loadingContainer {
            display: flex;
            width: 100%;
            height: 100%;
            background-color: var(--lof-bk);
            position: fixed;
            top: 0;
            left: 0;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            z-index: 10000;
        }

        .loadingContainer > img {
            width: 96px;
            height: 96px;
            border-radius: 12px;
            box-shadow: 0 0 2px var(--lof-lb)
        }

        .loadingContainer > div {
            width: 96px;
            height: 12px;
            background: linear-gradient(var(--lof-lf) 0 0) 0/0% no-repeat var(--lof-lb);
            animation: _lof_l1 2s infinite linear;
            margin-top: 6px;
            border-radius: 12px
        }
    </style>
</head>

<body data-pwauid="{{$application->uuid}}"
      style="height: 100vh; text-align: center; display: flex; align-items: center;">
<div class="loadingContainer">
    <img src="{{Storage::url($application->icon)}}">
    <div></div>
</div>
{{--<svg aria-labelledby="nh9v9c-aria" role="img" viewBox="0 0 400 160" height="160" width="400" style="margin: auto">--}}
{{--    <title id="nh9v9c-aria">Loading...</title>--}}
{{--    <rect role="presentation" x="0" y="0" width="100%" height="100%" clip-path="url(#nh9v9c-diff)" style="fill: url(&quot;#nh9v9c-animated-diff&quot;);"></rect>--}}
{{--    <defs>--}}
{{--        <clipPath id="nh9v9c-diff"><circle cx="150" cy="86" r="8"></circle><circle cx="194" cy="86" r="8"></circle><circle cx="238" cy="86" r="8"></circle></clipPath>--}}
{{--        <linearGradient id="nh9v9c-animated-diff"><stop offset="0%" stop-color="transparent" stop-opacity="1"><animate attributeName="offset" values="-2; -2; 1" keyTimes="0; 0.25; 1" dur="1.2s" repeatCount="indefinite"></animate></stop><stop offset="50%" stop-color="#eee" stop-opacity="1"><animate attributeName="offset" values="-1; -1; 2" keyTimes="0; 0.25; 1" dur="1.2s" repeatCount="indefinite"></animate></stop><stop offset="100%" stop-color="transparent" stop-opacity="1"><animate attributeName="offset" values="0; 0; 3" keyTimes="0; 0.25; 1" dur="1.2s" repeatCount="indefinite"></animate></stop></linearGradient>--}}
{{--    </defs>--}}
{{--</svg>--}}
</body>
