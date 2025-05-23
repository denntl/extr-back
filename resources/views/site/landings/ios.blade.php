@php
use App\Models\Application;
use Illuminate\Support\Facades\Storage;

/**
 * @var $application Application
 * @var $link string
 * @var $isPreview bool
 * @var $externalId string
 */

@endphp
    <!DOCTYPE html>
<html lang="{{$application->language}}">
<head>
    <script>
        const FbPixel = '{{$application->pixel_id}}';
        const externalId = '{{$externalId}}';
        const appUuid = '{{$application->uuid}}';
        const isPreview = {{$isPreview ? 'true' : 'false'}};
        const onesignalId = '{{$application->onesignal_id}}';
    </script>
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link href="https://fonts.cdnfonts.com/css/sf-pro-display" rel="stylesheet">
    <title>{{$application->app_name}}</title>
    <link rel="manifest" href="/manifest?app_uuid={{$application->uuid}}" crossorigin="use-credentials">

    <script src="/js/plugins/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script type="module">
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register("/pwabuilder-sw.js", {
                scope: '/'
            })
                .then(function (reg) {
                    window.serviceWorkerRegistration = reg
                    window.dispatchEvent(new Event("serviceWorkerRegistration"));
                })
        }
    </script>

    <link rel="icon" type="image/ico" href="{{Storage::url($application->icon)}}">
    <link rel="apple-touch-icon" href="{{Storage::url($application->icon)}}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{Storage::url($application->icon)}}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{Storage::url($application->icon)}}">
    <link rel="apple-touch-icon" sizes="167x167" href="{{Storage::url($application->icon)}}">
    <link rel="apple-touch-startup-image" href="{{Storage::url($application->icon)}}">
    <meta name="apple-mobile-web-app-title" content="{{$application->developer_name}}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes"/>

    <link rel="stylesheet" type="text/css" href="/css/templates/ios.css?v={{date('m')}}" />
    <script id="prevButton" data-prevbutton="{{$link}}">
    </script>

    @if($application->pixel_id)
    <!-- Facebook Pixel Code -->
    <script src="/js/templates/fb.js?v={{date('m')}}"></script>
    <noscript><img height="1" width="1" style="display:none" alt="facebook" src="https://www.facebook.com/tr?id={{$application->pixel_id}}&ev=PageView&noscript=1" /></noscript>
    <!-- End Facebook Pixel Code -->
    @endif

    @if($application->onesignal_id)
{{--    <script src="https://cdn.onesignal.com/sdks/web/v16/OneSignalSDK.page.js" defer></script>--}}
{{--    <script src="/js/templates/onesignal.js"></script>--}}
    @endif
</head>
<body data-pwauid="{{$application->uuid}}">
<header class="appHeader mobContainer">
    <a href="" onclick="(event) => {event.preventDefault();}">
        <img src="/img/ios/returnBtn.svg" class="header__returnBtn" alt="return">
    </a>

    <img src="{{Storage::url($application->icon)}}" alt="header" class="appHeader__img">
</header>

<div class="appMobHeader mobContainer" style="display: none">
    <a class="appMobHeader__returnBtn" href="#" onclick="(event) => {event.preventDefault();}">
        <svg width="10" height="12" viewBox="0 0 11 18" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M8.51811 17.7202C8.69426 17.8964 8.92223 18 9.19167 18C9.73055 18 10.1347 17.5958 10.1347 17.057C10.1347 16.7979 10.0311 16.5595 9.86523 16.3834L2.32124 9.00517L9.86523 1.62695C10.0311 1.4508 10.1347 1.20209 10.1347 0.953345C10.1347 0.414507 9.73055 0 9.19167 0C8.92223 0 8.69426 0.103638 8.51811 0.279787L0.310869 8.31088C0.113984 8.47668 0 8.73573 0 9.00517C0 9.26422 0.113984 9.50258 0.310869 9.69947L8.51811 17.7202Z" fill="#007AFF"/>
        </svg>
        <span>{{__('template.ios_application')}}</span>
    </a>

    <img class="appMobHeader__logo" src="{{Storage::url($application->icon)}}" alt="logo">

    <div class="appMobHeader__downloadBlock">
        <p class="appMobHeader__warn">{{__('template.ios_in_app_purchases')}}</p>

        <button class="appDownload__loadBtn install-button" type="button" onclick="NewOpenW()" id="">
            <span>{{__('template.ios_get')}}</span>
        </button>
    </div>
</div>

<section class="appDownload mobContainer">
    <div class="imageContainer">
        <img src="{{Storage::url($application->icon)}}" class="appAvatar" alt="avatar">
    </div>
    <div class="appDownload__panel">
        <h3 class="appDownload__title">{{$application->app_name}}</h3>
        <p class="appDownload__subtitle">{{$application->developer_name}}</p>
        <div class="appDownload__btnGroup">
            <div class="custom-loader"></div>
            <button type="button" class="appDownload__loadBtn install-button" onclick="NewOpenW()" id="">
                <span>{{__('template.ios_get')}}</span>
            </button>
            <p class="appDownload__warning">
                {{__('template.ios_in_app_purchases')}}
            </p>

            <button class="appDownload__share">
                <svg width="16" height="20" viewBox="0 0 16 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M7.86889 13.0615C8.24992 13.0615 8.56895 12.7426 8.56895 12.3704V3.26975L8.51575 1.9494L9.11835 2.57858L10.4564 4.00524C10.5805 4.14702 10.7666 4.21791 10.9349 4.21791C11.3071 4.21791 11.5818 3.95208 11.5818 3.59763C11.5818 3.40269 11.5109 3.2609 11.378 3.12797L8.38286 0.239176C8.20561 0.0618925 8.04613 0 7.86889 0C7.6828 0 7.53217 0.0618925 7.35492 0.239176L4.35981 3.12797C4.22687 3.2609 4.1471 3.40269 4.1471 3.59763C4.1471 3.95208 4.41297 4.21791 4.78515 4.21791C4.96236 4.21791 5.14846 4.14702 5.27251 4.00524L6.61943 2.57858L7.22202 1.94056L7.15998 3.26975V12.3704C7.15998 12.7426 7.48785 13.0615 7.86889 13.0615ZM2.78248 20H12.9465C14.8073 20 15.7289 19.0784 15.7289 17.253V8.51568C15.7289 6.69025 14.8073 5.76867 12.9465 5.76867H10.483V7.19533H12.9287C13.7971 7.19533 14.3023 7.66499 14.3023 8.58657V17.1821C14.3023 18.1037 13.7971 18.5733 12.9287 18.5733H2.80906C1.92294 18.5733 1.42666 18.1037 1.42666 17.1821V8.58657C1.42666 7.66499 1.92294 7.19533 2.80906 7.19533H5.25477V5.76867H2.78248C0.939314 5.76867 0 6.6814 0 8.51568V17.253C0 19.0873 0.939314 20 2.78248 20Z"
                        fill="#007AFF" />
                </svg>
            </button>
        </div>
    </div>
</section>

<section class="appAchievements mobContainer">
    <ul class="appAchievements__list">
        <li class="appAchievements__listItem">
            <p class="appAchievements__title">{{__('template.ios_ratings')}}</p>
            <p class="appAchievements__mark">
                {{round((float)$application->rating, 1)}}
            </p>

            <ul class="appAchievements__grade">
                <li class="appAchievements__gradeItem">
                    <svg width="11" height="10" viewBox="0 0 11 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2.00838 9.91712C2.20165 10.0632 2.44674 10.0114 2.73897 9.79929L5.2324 7.97046L7.73054 9.79929C8.02277 10.0114 8.26315 10.0632 8.46112 9.91712C8.65437 9.77099 8.6968 9.53061 8.57895 9.18653L7.59383 6.25475L10.1108 4.44478C10.4031 4.2374 10.5209 4.02058 10.4455 3.78491C10.3701 3.55866 10.1485 3.44554 9.78558 3.45024L6.69827 3.46911L5.76031 0.523181C5.64719 0.17438 5.47749 0 5.2324 0C4.99202 0 4.82232 0.17438 4.7092 0.523181L3.77121 3.46911L0.683911 3.45024C0.320972 3.44554 0.0994315 3.55866 0.0240161 3.78491C-0.0561051 4.02058 0.0664499 4.2374 0.358679 4.44478L2.87566 6.25475L1.89056 9.18653C1.77271 9.53061 1.81514 9.77099 2.00838 9.91712Z" fill="#8E8E93"/>
                    </svg>
                </li>

                <li class="appAchievements__gradeItem">
                    <svg width="11" height="10" viewBox="0 0 11 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2.00838 9.91712C2.20165 10.0632 2.44674 10.0114 2.73897 9.79929L5.2324 7.97046L7.73054 9.79929C8.02277 10.0114 8.26315 10.0632 8.46112 9.91712C8.65437 9.77099 8.6968 9.53061 8.57895 9.18653L7.59383 6.25475L10.1108 4.44478C10.4031 4.2374 10.5209 4.02058 10.4455 3.78491C10.3701 3.55866 10.1485 3.44554 9.78558 3.45024L6.69827 3.46911L5.76031 0.523181C5.64719 0.17438 5.47749 0 5.2324 0C4.99202 0 4.82232 0.17438 4.7092 0.523181L3.77121 3.46911L0.683911 3.45024C0.320972 3.44554 0.0994315 3.55866 0.0240161 3.78491C-0.0561051 4.02058 0.0664499 4.2374 0.358679 4.44478L2.87566 6.25475L1.89056 9.18653C1.77271 9.53061 1.81514 9.77099 2.00838 9.91712Z" fill="#8E8E93"/>
                    </svg>
                </li>

                <li class="appAchievements__gradeItem">
                    <svg width="11" height="10" viewBox="0 0 11 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2.00838 9.91712C2.20165 10.0632 2.44674 10.0114 2.73897 9.79929L5.2324 7.97046L7.73054 9.79929C8.02277 10.0114 8.26315 10.0632 8.46112 9.91712C8.65437 9.77099 8.6968 9.53061 8.57895 9.18653L7.59383 6.25475L10.1108 4.44478C10.4031 4.2374 10.5209 4.02058 10.4455 3.78491C10.3701 3.55866 10.1485 3.44554 9.78558 3.45024L6.69827 3.46911L5.76031 0.523181C5.64719 0.17438 5.47749 0 5.2324 0C4.99202 0 4.82232 0.17438 4.7092 0.523181L3.77121 3.46911L0.683911 3.45024C0.320972 3.44554 0.0994315 3.55866 0.0240161 3.78491C-0.0561051 4.02058 0.0664499 4.2374 0.358679 4.44478L2.87566 6.25475L1.89056 9.18653C1.77271 9.53061 1.81514 9.77099 2.00838 9.91712Z" fill="#8E8E93"/>
                    </svg>
                </li>

                <li class="appAchievements__gradeItem">
                    <svg width="11" height="10" viewBox="0 0 11 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2.00838 9.91712C2.20165 10.0632 2.44674 10.0114 2.73897 9.79929L5.2324 7.97046L7.73054 9.79929C8.02277 10.0114 8.26315 10.0632 8.46112 9.91712C8.65437 9.77099 8.6968 9.53061 8.57895 9.18653L7.59383 6.25475L10.1108 4.44478C10.4031 4.2374 10.5209 4.02058 10.4455 3.78491C10.3701 3.55866 10.1485 3.44554 9.78558 3.45024L6.69827 3.46911L5.76031 0.523181C5.64719 0.17438 5.47749 0 5.2324 0C4.99202 0 4.82232 0.17438 4.7092 0.523181L3.77121 3.46911L0.683911 3.45024C0.320972 3.44554 0.0994315 3.55866 0.0240161 3.78491C-0.0561051 4.02058 0.0664499 4.2374 0.358679 4.44478L2.87566 6.25475L1.89056 9.18653C1.77271 9.53061 1.81514 9.77099 2.00838 9.91712Z" fill="#8E8E93"/>
                    </svg>
                </li>

                <li class="appAchievements__gradeItem">
                    <svg width="11" height="10" viewBox="0 0 11 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2.00838 9.91712C2.20165 10.0632 2.44674 10.0114 2.73897 9.79929L5.2324 7.97046L7.73054 9.79929C8.02277 10.0114 8.26315 10.0632 8.46112 9.91712C8.65437 9.77099 8.6968 9.53061 8.57895 9.18653L7.59383 6.25475L10.1108 4.44478C10.4031 4.2374 10.5209 4.02058 10.4455 3.78491C10.3701 3.55866 10.1485 3.44554 9.78558 3.45024L6.69827 3.46911L5.76031 0.523181C5.64719 0.17438 5.47749 0 5.2324 0C4.99202 0 4.82232 0.17438 4.7092 0.523181L3.77121 3.46911L0.683911 3.45024C0.320972 3.44554 0.0994315 3.55866 0.0240161 3.78491C-0.0561051 4.02058 0.0664499 4.2374 0.358679 4.44478L2.87566 6.25475L1.89056 9.18653C1.77271 9.53061 1.81514 9.77099 2.00838 9.91712Z" fill="#8E8E93"/>
                    </svg>
                </li>
            </ul>
        </li>

        <li class="appAchievements__listItem">
            <p class="appAchievements__title">{{__('template.ios_age')}}</p>
            <p class="appAchievements__mark">
                18+
            </p>
        </li>

        <li class="appAchievements__listItem">
            <p class="appAchievements__title">{{__('template.ios_rating')}}</p>
            <p class="appAchievements__mark">
                <span class="appAchievements__mark_position">№</span>
                <span class="appAchievements__mark_positionValue">31</span>
            </p>
            <p class="appAchievements__descript">{{__('template.ios_category_name')}}</p>
        </li>

        <li class="appAchievements__listItem">
            <p class="appAchievements__title">{{__('template.ios_developer')}}</p>
            <p class="appAchievements__mark">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3.06641 17.998H14.9121C15.9342 17.998 16.7008 17.7457 17.2119 17.2412C17.723 16.7367 17.9785 15.9798 17.9785 14.9707V3.04688C17.9785 2.03776 17.723 1.28092 17.2119 0.776367C16.7008 0.27181 15.9342 0.0195312 14.9121 0.0195312H3.06641C2.04427 0.0195312 1.27767 0.27181 0.766602 0.776367C0.255534 1.28092 0 2.03776 0 3.04688V14.9707C0 15.9798 0.255534 16.7367 0.766602 17.2412C1.27767 17.7457 2.04427 17.998 3.06641 17.998ZM3.08594 16.4258C2.59766 16.4258 2.22331 16.2972 1.96289 16.04C1.70248 15.7829 1.57227 15.4004 1.57227 14.8926V3.125C1.57227 2.61719 1.70248 2.2347 1.96289 1.97754C2.22331 1.72038 2.59766 1.5918 3.08594 1.5918H14.8926C15.3743 1.5918 15.747 1.72038 16.0107 1.97754C16.2744 2.2347 16.4062 2.61719 16.4062 3.125V14.8926C16.4062 15.4004 16.2744 15.7829 16.0107 16.04C15.747 16.2972 15.3743 16.4258 14.8926 16.4258H3.08594ZM2.27539 17.002H15.7129C15.5696 16.3184 15.2994 15.6869 14.9023 15.1074C14.5052 14.528 14.0088 14.0267 13.4131 13.6035C12.8174 13.1804 12.1452 12.8516 11.3965 12.6172C10.6478 12.3828 9.84698 12.2656 8.99414 12.2656C8.14779 12.2656 7.35026 12.3828 6.60156 12.6172C5.85287 12.8516 5.17904 13.1804 4.58008 13.6035C3.98112 14.0267 3.48307 14.528 3.08594 15.1074C2.6888 15.6869 2.41862 16.3184 2.27539 17.002ZM8.99414 10.6348C9.61263 10.6413 10.1742 10.485 10.6787 10.166C11.1832 9.847 11.5853 9.40918 11.8848 8.85254C12.1843 8.2959 12.334 7.66927 12.334 6.97266C12.334 6.32161 12.1843 5.72428 11.8848 5.18066C11.5853 4.63704 11.1832 4.20247 10.6787 3.87695C10.1742 3.55143 9.61263 3.38867 8.99414 3.38867C8.36914 3.38867 7.80436 3.55143 7.2998 3.87695C6.79525 4.20247 6.39486 4.63704 6.09863 5.18066C5.80241 5.72428 5.6543 6.32161 5.6543 6.97266C5.66081 7.66927 5.81217 8.29264 6.1084 8.84277C6.40462 9.3929 6.80338 9.82748 7.30469 10.1465C7.80599 10.4655 8.36914 10.6283 8.99414 10.6348Z" fill="#8E8E93"/>
                </svg>
            </p>
            <p class="appAchievements__descript">{{$application->developer_name}}</p>
        </li>

        <li class="appAchievements__listItem">
            <p class="appAchievements__title">{{__('template.ios_language')}}</p>
            <p class="appAchievements__mark">
                {{$application->language}}
            </p>
            <p class="appAchievements__descript">{{__('template.ios_more')}}</p>
        </li>

        <li class="appAchievements__listItem">
            <p class="appAchievements__title">{{__('template.ios_size')}}</p>
            <p class="appAchievements__mark">
                291
            </p>
            <p class="appAchievements__descript">{{__('template.ios_mb')}}</p>
        </li>
    </ul>
</section>

<section class="appGallery mobContainer">
    @foreach($application->files as $file)
    <img class="appGallery__galleryPic" src="{{Storage::url($file->path)}}" alt="gallery">
    @endforeach
</section>

<article class="appSupporting mobContainer">
    <div class="appSupporting__supportingIcons">
        <svg width="10" height="15" viewBox="0 0 10 15" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="0.5" y="0.5" width="9" height="14" rx="1.5" stroke="#8A8A8D"/>
            <rect x="3" y="2" width="4" height="1" fill="#8A8A8D"/>
            <rect x="3" y="13" width="4" height="0.5" fill="#8A8A8D"/>
        </svg>

        <svg width="12" height="17" viewBox="0 0 12 17" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="0.5" y="0.5" width="11" height="16" rx="1.5" stroke="#8A8A8D"/>
            <rect x="3" y="15" width="6" height="0.5" fill="#8A8A8D"/>
        </svg>

        <svg width="18" height="16" viewBox="0 0 18 16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M5.7505 13.1521C6.24777 12.9816 6.75462 12.9444 7.2804 13.0566C7.70381 13.1168 8.13393 13.1473 8.56924 13.1473C12.7036 13.1473 15.9611 10.4137 15.9611 7.18314C15.9611 3.95323 12.7035 1.21972 8.56924 1.21972C4.43452 1.21972 1.17733 3.953 1.17733 7.18314C1.17733 8.85079 2.04155 10.4297 3.55704 11.5588C3.59503 11.5867 3.68201 11.648 3.77755 11.7142C3.81982 11.7434 3.86033 11.7713 3.89113 11.7923C3.88472 11.7879 3.90942 11.8045 3.87548 11.7844L3.9386 11.8218L3.99772 11.8654C4.60216 12.3104 4.89066 12.9065 4.93124 13.5567C5.2555 13.3764 5.56713 13.2149 5.7505 13.1521ZM0 7.19025C0 3.21956 3.83595 0 8.56924 0C13.3018 0 17.1385 3.21956 17.1385 7.19025C17.1385 11.1617 13.3018 14.3812 8.56924 14.3812C8.05534 14.3812 7.55291 14.3432 7.0641 14.2708C7.06339 14.2708 7.06195 14.2701 7.06124 14.2701C5.90372 13.9942 5.41347 15.2814 2.7759 15.9487C2.23405 16.0863 2.16883 15.9494 2.58382 15.5093C3.33495 14.7145 4.05885 13.4186 3.30628 12.8646C3.28335 12.851 2.99307 12.651 2.88269 12.57C1.11452 11.2527 0 9.32969 0 7.19025Z" fill="#8A8A8D"/>
        </svg>
    </div>
    <p class="appSupporting__devices">{{__('template.ios_supports')}}</p>
</article>

<section class="appAnotation mobContainer">
    <p class="appAnotation__description" style="white-space: pre-wrap;">{{$application->description}}</p>
    <div class="appAnotation__dev">
        <div>
            <p class="appAnotation__devName">{{$application->developer_name}}</p>
            <p>{{__('template.ios_developer2')}}</p>
        </div>

        <svg width="9" height="16" viewBox="0 0 9 16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0.84693 16C1.0771 16 1.27963 15.9079 1.4453 15.7514L8.73648 8.6168C8.91139 8.44189 9.00342 8.23013 9.00342 8C9.00342 7.76063 8.91139 7.53969 8.73648 7.38321L1.4453 0.257748C1.28882 0.0920694 1.0771 0 0.84693 0C0.368239 0 0 0.368239 0 0.84693C0 1.06787 0.101261 1.28886 0.248517 1.44534L6.94128 8L0.248517 14.5547C0.101261 14.7111 0 14.9229 0 15.153C0 15.6318 0.368239 16 0.84693 16Z" fill="#8A8A8D"/>
        </svg>
    </div>
</section>

<section class="appReview mobContainer">
    <hr>
    <div class="appReview__title">
        <h3>{{__('template.ios_ratings_and_reviews')}}</h3>
        <button type="button" class="appReview__allComments">
            <span>{{__('template.ios_see_all')}}</span>
        </button>
    </div>
    <div class="appReview__degree">
        <div class="appReview__leftBlock">
            <p class="appReview__currentDegree">{{round((float)$application->rating, 1)}}</p>
        </div>

        <div class="appReview__rightBlock">
            <ul class="appReview__rating">
                <li class="appReview__ratingItem">
                    <ul class="appReview__ratingItem_stars">
                        <li class="appReview__ratingItem_star">
                            <img src="/img/ios/VectorStar.svg" alt="star">
                        </li>
                        <li class="appReview__ratingItem_star">
                            <img src="/img/ios/VectorStar.svg" alt="star">
                        </li>
                        <li class="appReview__ratingItem_star">
                            <img src="/img/ios/VectorStar.svg" alt="star">
                        </li>
                        <li class="appReview__ratingItem_star">
                            <img src="/img/ios/VectorStar.svg" alt="star">
                        </li>
                        <li class="appReview__ratingItem_star">
                            <img src="/img/ios/VectorStar.svg" alt="star">
                        </li>
                    </ul>
                    <div class="appReview__rateScale">
                        <div class="appReview__rateValue" id="star5" style="width: 90%"></div>
                    </div>
                </li>

                <li class="appReview__ratingItem">
                    <ul class="appReview__ratingItem_stars">
                        <li class="appReview__ratingItem_star">
                            <img src="/img/ios/VectorStar.svg" alt="star">
                        </li>
                        <li class="appReview__ratingItem_star">
                            <img src="/img/ios/VectorStar.svg" alt="star">
                        </li>
                        <li class="appReview__ratingItem_star">
                            <img src="/img/ios/VectorStar.svg" alt="star">
                        </li>
                        <li class="appReview__ratingItem_star">
                            <img src="/img/ios/VectorStar.svg" alt="star">
                        </li>
                    </ul>
                    <div class="appReview__rateScale">
                        <div class="appReview__rateValue" id="star4" style="width: 5%"></div>
                    </div>
                </li>

                <li class="appReview__ratingItem">
                    <ul class="appReview__ratingItem_stars">
                        <li class="appReview__ratingItem_star">
                            <img src="/img/ios/VectorStar.svg" alt="star">
                        </li>
                        <li class="appReview__ratingItem_star">
                            <img src="/img/ios/VectorStar.svg" alt="star">
                        </li>
                        <li class="appReview__ratingItem_star">
                            <img src="/img/ios/VectorStar.svg" alt="star">
                        </li>

                    </ul>
                    <div class="appReview__rateScale">
                        <div class="appReview__rateValue" id="star3" style="width: 3%"></div>
                    </div>
                </li>

                <li class="appReview__ratingItem">
                    <ul class="appReview__ratingItem_stars">
                        <li class="appReview__ratingItem_star">
                            <img src="/img/ios/VectorStar.svg" alt="star">
                        </li>
                        <li class="appReview__ratingItem_star">
                            <img src="/img/ios/VectorStar.svg" alt="star">
                        </li>
                    </ul>
                    <div class="appReview__rateScale">
                        <div class="appReview__rateValue" id="star2" style="width: 2%"></div>
                    </div>
                </li>

                <li class="appReview__ratingItem">
                    <ul class="appReview__ratingItem_stars">
                        <li class="appReview__ratingItem_star">
                            <img src="/img/ios/VectorStar.svg" alt="star">
                        </li>
                    </ul>
                    <div class="appReview__rateScale">
                        <div class="appReview__rateValue" id="star1" style="width: 1%"></div>
                    </div>
                </li>
            </ul>
        </div>
    </div>

    <div class="appReview__degreeSubtitle">
        <p>{{__('template.ios_out')}} 5</p>
        <p>{{__('template.ios_ratings2')}} <span>129</span> {{__('template.ios_thousands')}}</p>
    </div>

    @php
        $currentDate = new DateTime();
        /**
         * @var $reviews \App\Models\ApplicationComment[]
         */
        $reviews = $application->applicationComments()->orderByRaw('date DESC NULLS LAST')->limit(1)->get();
    @endphp
    @foreach($reviews as $comment)
    <div class="appReview__comment">
        <div class="appReview__commentTitle">
            @php
                if ($comment->date) {
                    $currentDate->setDate(...explode('-', $comment->date));
                } else {
                    $currentDate->sub(new DateInterval('P'.rand(1,15).'D'));
                }
            @endphp
            <h4></h4>
            <p>{{$currentDate->format('d.m.y')}}</p>
        </div>

        <div class="appReview__commentRate">
            <ul class="appReview__commentStars">
                @for($i = 0; $i < $comment->stars; $i++)
                <li class="appReview__commentStarsItem active">
                    <svg width="12" height="10" viewBox="0 0 12 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6.25068 7.76874L6.10287 7.66052L5.95515 7.76887L3.46273 9.59696C3.46259 9.59706 3.46246 9.59716 3.46232 9.59726C3.33196 9.69181 3.23364 9.73417 3.16434 9.74622C3.10508 9.75653 3.06681 9.7457 3.02978 9.71771C2.98413 9.68319 2.95964 9.6433 2.95008 9.58535C2.93916 9.51917 2.94644 9.41712 2.99768 9.26754L2.99814 9.26616L3.98325 6.33438L4.04201 6.15949L3.89222 6.05178L1.37524 4.24181L1.37524 4.24181L1.37397 4.2409C1.24451 4.14903 1.17565 4.07056 1.14399 4.00985C1.11711 3.95831 1.11414 3.91592 1.13132 3.86537L1.13179 3.86396C1.14787 3.81574 1.176 3.78128 1.22998 3.75385C1.29136 3.72267 1.39302 3.69817 1.55127 3.70023L1.55299 3.70024L4.64029 3.7191L4.82423 3.72023L4.88004 3.54495L5.81761 0.600307C5.81768 0.600087 5.81775 0.599868 5.81782 0.599649C5.86802 0.445092 5.92288 0.355466 5.97105 0.306973C6.01231 0.265435 6.05162 0.25 6.10301 0.25C6.15807 0.25 6.19852 0.266416 6.23968 0.307572C6.28793 0.355824 6.34268 0.445053 6.3929 0.599667C6.39297 0.59988 6.39304 0.600093 6.39311 0.600307L7.33066 3.54495L7.38647 3.72023L7.57041 3.7191L10.6577 3.70024L10.6594 3.70022C10.8177 3.69817 10.9194 3.72267 10.9807 3.75385C11.0342 3.781 11.0623 3.81505 11.0784 3.86252C11.0949 3.91494 11.0911 3.95916 11.0645 4.0108C11.0332 4.07141 10.9654 4.14958 10.8367 4.24088L10.8355 4.24181L8.31848 6.05178L8.16869 6.15949L8.22745 6.33438L9.21257 9.26616L9.21305 9.26754C9.26428 9.41712 9.27156 9.51917 9.26064 9.58535C9.25116 9.6428 9.22701 9.6825 9.18212 9.71682C9.14138 9.74652 9.10192 9.75624 9.04482 9.74626C8.97648 9.73432 8.8794 9.69227 8.7484 9.59726C8.74826 9.59716 8.74813 9.59706 8.74799 9.59696L6.25068 7.76874ZM1.55452 3.45024C1.19158 3.44554 0.970037 3.55866 0.894622 3.78491L1.55452 3.45024Z" stroke-width="0.5"/>
                    </svg>
                </li>
                @endfor

                @for($i = 0; $i < (5 - $comment->stars); $i++)
                <li class="appReview__commentStarsItem">
                    <svg width="12" height="10" viewBox="0 0 12 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6.25068 7.76874L6.10287 7.66052L5.95515 7.76887L3.46273 9.59696C3.46259 9.59706 3.46246 9.59716 3.46232 9.59726C3.33196 9.69181 3.23364 9.73417 3.16434 9.74622C3.10508 9.75653 3.06681 9.7457 3.02978 9.71771C2.98413 9.68319 2.95964 9.6433 2.95008 9.58535C2.93916 9.51917 2.94644 9.41712 2.99768 9.26754L2.99814 9.26616L3.98325 6.33438L4.04201 6.15949L3.89222 6.05178L1.37524 4.24181L1.37524 4.24181L1.37397 4.2409C1.24451 4.14903 1.17565 4.07056 1.14399 4.00985C1.11711 3.95831 1.11414 3.91592 1.13132 3.86537L1.13179 3.86396C1.14787 3.81574 1.176 3.78128 1.22998 3.75385C1.29136 3.72267 1.39302 3.69817 1.55127 3.70023L1.55299 3.70024L4.64029 3.7191L4.82423 3.72023L4.88004 3.54495L5.81761 0.600307C5.81768 0.600087 5.81775 0.599868 5.81782 0.599649C5.86802 0.445092 5.92288 0.355466 5.97105 0.306973C6.01231 0.265435 6.05162 0.25 6.10301 0.25C6.15807 0.25 6.19852 0.266416 6.23968 0.307572C6.28793 0.355824 6.34268 0.445053 6.3929 0.599667C6.39297 0.59988 6.39304 0.600093 6.39311 0.600307L7.33066 3.54495L7.38647 3.72023L7.57041 3.7191L10.6577 3.70024L10.6594 3.70022C10.8177 3.69817 10.9194 3.72267 10.9807 3.75385C11.0342 3.781 11.0623 3.81505 11.0784 3.86252C11.0949 3.91494 11.0911 3.95916 11.0645 4.0108C11.0332 4.07141 10.9654 4.14958 10.8367 4.24088L10.8355 4.24181L8.31848 6.05178L8.16869 6.15949L8.22745 6.33438L9.21257 9.26616L9.21305 9.26754C9.26428 9.41712 9.27156 9.51917 9.26064 9.58535C9.25116 9.6428 9.22701 9.6825 9.18212 9.71682C9.14138 9.74652 9.10192 9.75624 9.04482 9.74626C8.97648 9.73432 8.8794 9.69227 8.7484 9.59726C8.74826 9.59716 8.74813 9.59706 8.74799 9.59696L6.25068 7.76874ZM1.55452 3.45024C1.19158 3.44554 0.970037 3.55866 0.894622 3.78491L1.55452 3.45024Z" stroke-width="0.5"/>
                    </svg>
                </li>
                @endfor
            </ul>
            <p>{{$comment->author_name}}</p>
        </div>

        <p class="appReview__commentText">
            {{$comment->text}}
        </p>
    </div>
    @endforeach
</section>

<div class="appReview__comments mobContainer">
    <button type="button" class="commentsReturn">
        <svg width="11" height="11" viewBox="0 0 11 18" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M8.51811 17.7202C8.69426 17.8964 8.92223 18 9.19167 18C9.73055 18 10.1347 17.5958 10.1347 17.057C10.1347 16.7979 10.0311 16.5595 9.86523 16.3834L2.32124 9.00517L9.86523 1.62695C10.0311 1.4508 10.1347 1.20209 10.1347 0.953345C10.1347 0.414507 9.73055 0 9.19167 0C8.92223 0 8.69426 0.103638 8.51811 0.279787L0.310869 8.31088C0.113984 8.47668 0 8.73573 0 9.00517C0 9.26422 0.113984 9.50258 0.310869 9.69947L8.51811 17.7202Z" fill="#007AFF"/>
        </svg>

        <span>{{__('template.ios_back')}}</span>
    </button>
    <h2>{{__('template.ios_reviews_and_reviews')}}</h2>
    <div class="appReview__degree">
        <div class="appReview__leftBlock">
            <p class="appReview__currentDegree">{{round((float)$application->rating, 1)}}</p>
        </div>

        <div class="appReview__rightBlock">
            <ul class="appReview__rating">
                <li class="appReview__ratingItem">
                    <ul class="appReview__ratingItem_stars">
                        <li class="appReview__ratingItem_star">
                            <img src="/img/ios/VectorStar.svg" alt="star">
                        </li>
                        <li class="appReview__ratingItem_star">
                            <img src="/img/ios/VectorStar.svg" alt="star">
                        </li>
                        <li class="appReview__ratingItem_star">
                            <img src="/img/ios/VectorStar.svg" alt="star">
                        </li>
                        <li class="appReview__ratingItem_star">
                            <img src="/img/ios/VectorStar.svg" alt="star">
                        </li>
                        <li class="appReview__ratingItem_star">
                            <img src="/img/ios/VectorStar.svg" alt="star">
                        </li>
                    </ul>
                    <div class="appReview__rateScale">
                        <div class="appReview__rateValue" id="star5" style="width: 90%"></div>
                    </div>
                </li>

                <li class="appReview__ratingItem">
                    <ul class="appReview__ratingItem_stars">
                        <li class="appReview__ratingItem_star">
                            <img src="/img/ios/VectorStar.svg" alt="star">
                        </li>
                        <li class="appReview__ratingItem_star">
                            <img src="/img/ios/VectorStar.svg" alt="star">
                        </li>
                        <li class="appReview__ratingItem_star">
                            <img src="/img/ios/VectorStar.svg" alt="star">
                        </li>
                        <li class="appReview__ratingItem_star">
                            <img src="/img/ios/VectorStar.svg" alt="star">
                        </li>
                    </ul>
                    <div class="appReview__rateScale">
                        <div class="appReview__rateValue" id="star4" style="width: 5%"></div>
                    </div>
                </li>

                <li class="appReview__ratingItem">
                    <ul class="appReview__ratingItem_stars">
                        <li class="appReview__ratingItem_star">
                            <img src="/img/ios/VectorStar.svg" alt="star">
                        </li>
                        <li class="appReview__ratingItem_star">
                            <img src="/img/ios/VectorStar.svg" alt="star">
                        </li>
                        <li class="appReview__ratingItem_star">
                            <img src="/img/ios/VectorStar.svg" alt="star">
                        </li>

                    </ul>
                    <div class="appReview__rateScale">
                        <div class="appReview__rateValue" id="star3" style="width: 3%"></div>
                    </div>
                </li>

                <li class="appReview__ratingItem">
                    <ul class="appReview__ratingItem_stars">
                        <li class="appReview__ratingItem_star">
                            <img src="/img/ios/VectorStar.svg" alt="star">
                        </li>
                        <li class="appReview__ratingItem_star">
                            <img src="/img/ios/VectorStar.svg" alt="star">
                        </li>
                    </ul>
                    <div class="appReview__rateScale">
                        <div class="appReview__rateValue" id="star2" style="width: 2%"></div>
                    </div>
                </li>

                <li class="appReview__ratingItem">
                    <ul class="appReview__ratingItem_stars">
                        <li class="appReview__ratingItem_star">
                            <img src="/img/ios/VectorStar.svg" alt="star">
                        </li>
                    </ul>
                    <div class="appReview__rateScale">
                        <div class="appReview__rateValue" id="star1" style="width: 1%"></div>
                    </div>
                </li>
            </ul>
        </div>
    </div>

    <div class="appReview__degreeSubtitle">
        <p>{{__('template.ios_out')}} 5</p>
        <p>{{__('template.ios_ratings2')}} <span>129</span> {{__('template.ios_thousands')}}</p>
    </div>

    @php
        $currentDate = new DateTime();
        /**
         * @var $reviews \App\Models\ApplicationComment[]
         */
        $reviews = $application->applicationComments()->orderByRaw('date DESC NULLS LAST')->limit(10)->get();
    @endphp
    @foreach($reviews as $comment)
        <div class="appReview__comment">
            <div class="appReview__commentTitle">
                @php
                    if ($comment->date) {
                        $currentDate->setDate(...explode('-', $comment->date));
                    } else {
                        $currentDate->sub(new DateInterval('P'.rand(1,15).'D'));
                    }
                @endphp
                <h4></h4>
                <p>{{$currentDate->format('d.m.y')}}</p>
            </div>

            <div class="appReview__commentRate">
                <ul class="appReview__commentStars">
                    @for($i = 0; $i < $comment->stars; $i++)
                        <li class="appReview__commentStarsItem active">
                            <svg width="12" height="10" viewBox="0 0 12 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6.25068 7.76874L6.10287 7.66052L5.95515 7.76887L3.46273 9.59696C3.46259 9.59706 3.46246 9.59716 3.46232 9.59726C3.33196 9.69181 3.23364 9.73417 3.16434 9.74622C3.10508 9.75653 3.06681 9.7457 3.02978 9.71771C2.98413 9.68319 2.95964 9.6433 2.95008 9.58535C2.93916 9.51917 2.94644 9.41712 2.99768 9.26754L2.99814 9.26616L3.98325 6.33438L4.04201 6.15949L3.89222 6.05178L1.37524 4.24181L1.37524 4.24181L1.37397 4.2409C1.24451 4.14903 1.17565 4.07056 1.14399 4.00985C1.11711 3.95831 1.11414 3.91592 1.13132 3.86537L1.13179 3.86396C1.14787 3.81574 1.176 3.78128 1.22998 3.75385C1.29136 3.72267 1.39302 3.69817 1.55127 3.70023L1.55299 3.70024L4.64029 3.7191L4.82423 3.72023L4.88004 3.54495L5.81761 0.600307C5.81768 0.600087 5.81775 0.599868 5.81782 0.599649C5.86802 0.445092 5.92288 0.355466 5.97105 0.306973C6.01231 0.265435 6.05162 0.25 6.10301 0.25C6.15807 0.25 6.19852 0.266416 6.23968 0.307572C6.28793 0.355824 6.34268 0.445053 6.3929 0.599667C6.39297 0.59988 6.39304 0.600093 6.39311 0.600307L7.33066 3.54495L7.38647 3.72023L7.57041 3.7191L10.6577 3.70024L10.6594 3.70022C10.8177 3.69817 10.9194 3.72267 10.9807 3.75385C11.0342 3.781 11.0623 3.81505 11.0784 3.86252C11.0949 3.91494 11.0911 3.95916 11.0645 4.0108C11.0332 4.07141 10.9654 4.14958 10.8367 4.24088L10.8355 4.24181L8.31848 6.05178L8.16869 6.15949L8.22745 6.33438L9.21257 9.26616L9.21305 9.26754C9.26428 9.41712 9.27156 9.51917 9.26064 9.58535C9.25116 9.6428 9.22701 9.6825 9.18212 9.71682C9.14138 9.74652 9.10192 9.75624 9.04482 9.74626C8.97648 9.73432 8.8794 9.69227 8.7484 9.59726C8.74826 9.59716 8.74813 9.59706 8.74799 9.59696L6.25068 7.76874ZM1.55452 3.45024C1.19158 3.44554 0.970037 3.55866 0.894622 3.78491L1.55452 3.45024Z" stroke-width="0.5"/>
                            </svg>
                        </li>
                    @endfor

                    @for($i = 0; $i < (5 - $comment->stars); $i++)
                        <li class="appReview__commentStarsItem">
                            <svg width="12" height="10" viewBox="0 0 12 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6.25068 7.76874L6.10287 7.66052L5.95515 7.76887L3.46273 9.59696C3.46259 9.59706 3.46246 9.59716 3.46232 9.59726C3.33196 9.69181 3.23364 9.73417 3.16434 9.74622C3.10508 9.75653 3.06681 9.7457 3.02978 9.71771C2.98413 9.68319 2.95964 9.6433 2.95008 9.58535C2.93916 9.51917 2.94644 9.41712 2.99768 9.26754L2.99814 9.26616L3.98325 6.33438L4.04201 6.15949L3.89222 6.05178L1.37524 4.24181L1.37524 4.24181L1.37397 4.2409C1.24451 4.14903 1.17565 4.07056 1.14399 4.00985C1.11711 3.95831 1.11414 3.91592 1.13132 3.86537L1.13179 3.86396C1.14787 3.81574 1.176 3.78128 1.22998 3.75385C1.29136 3.72267 1.39302 3.69817 1.55127 3.70023L1.55299 3.70024L4.64029 3.7191L4.82423 3.72023L4.88004 3.54495L5.81761 0.600307C5.81768 0.600087 5.81775 0.599868 5.81782 0.599649C5.86802 0.445092 5.92288 0.355466 5.97105 0.306973C6.01231 0.265435 6.05162 0.25 6.10301 0.25C6.15807 0.25 6.19852 0.266416 6.23968 0.307572C6.28793 0.355824 6.34268 0.445053 6.3929 0.599667C6.39297 0.59988 6.39304 0.600093 6.39311 0.600307L7.33066 3.54495L7.38647 3.72023L7.57041 3.7191L10.6577 3.70024L10.6594 3.70022C10.8177 3.69817 10.9194 3.72267 10.9807 3.75385C11.0342 3.781 11.0623 3.81505 11.0784 3.86252C11.0949 3.91494 11.0911 3.95916 11.0645 4.0108C11.0332 4.07141 10.9654 4.14958 10.8367 4.24088L10.8355 4.24181L8.31848 6.05178L8.16869 6.15949L8.22745 6.33438L9.21257 9.26616L9.21305 9.26754C9.26428 9.41712 9.27156 9.51917 9.26064 9.58535C9.25116 9.6428 9.22701 9.6825 9.18212 9.71682C9.14138 9.74652 9.10192 9.75624 9.04482 9.74626C8.97648 9.73432 8.8794 9.69227 8.7484 9.59726C8.74826 9.59716 8.74813 9.59706 8.74799 9.59696L6.25068 7.76874ZM1.55452 3.45024C1.19158 3.44554 0.970037 3.55866 0.894622 3.78491L1.55452 3.45024Z" stroke-width="0.5"/>
                            </svg>
                        </li>
                    @endfor
                </ul>
                <p>{{$comment->author_name}}</p>
            </div>

            <p class="appReview__commentText">
                {{$comment->text}}
            </p>
        </div>
    @endforeach

</div>

<section class="appUpdates mobContainer">
    <hr>
    <div class="appUpdates__title">
        <h3>{{__('template.ios_whats_new')}}</h3>
        <a href="#"><span>{{__('template.ios_version_history')}}</span></a>
    </div>

    <div class="appUpdates__versions">
        <p>{{__('template.ios_version')}}</p>
        <p>{{__('template.ios_days_ago')}}</p>
    </div>

    <p class="appUpdates__txt">{{__('template.ios_fixes')}}</p>
</section>

<section class="appConfidence mobContainer">
    <hr>
    <div class="appConfidence__title">
        <h3>{{__('template.ios_privacy')}}</h3>
        <a href="#">
            <span>{{__('template.ios_details')}}</span>
        </a>
    </div>

    <p class="appConfidence__politics">
        {{__('template.ios_secure1')}}
        {{__('template.ios_secure2')}} <a href="#">{{__('template.ios_secure3')}}</a>.
    </p>

    <div class="appConfidence__userDataTracking">
        <img src="/img/ios/dataTrack.png" alt="dataTrack">

        <h3>{{__('template.ios_secure4')}}</h3>

        <p>{{__('template.ios_secure5')}}</p>

        <ul class="appConfidence__userDataTracking_list">
            <li class="appConfidence__userDataTracking_listItem">
                <svg width="14" height="17" viewBox="0 0 14 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M2.66071 16.755H11.5201C12.974 16.755 13.8178 15.9113 13.8178 14.2991V6.50195C13.8178 4.88978 12.9665 4.04603 11.3393 4.04603H10.2771C10.2394 2.35853 8.80049 0.957306 6.99999 0.957306C5.20702 0.957306 3.76059 2.35853 3.72293 4.04603H2.66071C1.03347 4.04603 0.18219 4.88978 0.18219 6.50195V14.2991C0.18219 15.9113 1.03347 16.755 2.66071 16.755ZM6.99999 2.29073C8.03208 2.29073 8.79296 3.06668 8.82309 4.04603H5.17689C5.20702 3.06668 5.9679 2.29073 6.99999 2.29073Z" fill="black"/>
                </svg>
                <span>{{__('template.ios_purchases')}}</span>
            </li>

            <li class="appConfidence__userDataTracking_listItem">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M2.12947 8.66043L7.73438 8.68303C7.84738 8.68303 7.88505 8.72823 7.88505 8.84123L7.90012 14.4085C7.90012 15.7042 9.51982 15.998 10.0924 14.7475L15.7952 2.40764C16.4054 1.08928 15.3809 0.215393 14.1228 0.795471L1.72266 6.51338C0.570041 7.04073 0.811112 8.65289 2.12947 8.66043Z" fill="black"/>
                </svg>

                <span>{{__('template.ios_location')}}</span>
            </li>

            <li class="appConfidence__userDataTracking_listItem">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g clip-path="url(#clip0_1_1096)">
                        <circle cx="8" cy="8" r="8" fill="black"/>
                        <path d="M8.62612 11.062C8.62612 11.2487 8.71945 11.356 8.90612 11.384L9.52212 11.482V12H6.63812V11.482L7.25412 11.384C7.44078 11.356 7.53412 11.2487 7.53412 11.062V7.24C7.53412 7.05333 7.44545 6.95067 7.26812 6.932L6.55412 6.834V6.33C6.85278 6.23667 7.17012 6.17133 7.50612 6.134C7.85145 6.08733 8.17345 6.05933 8.47212 6.05L8.62612 6.218V11.062ZM8.71012 4.482C8.71012 4.69667 8.64012 4.874 8.50012 5.014C8.36012 5.14467 8.17812 5.21 7.95412 5.21C7.73012 5.21 7.54812 5.14467 7.40812 5.014C7.26812 4.874 7.19812 4.69667 7.19812 4.482C7.19812 4.258 7.26812 4.08067 7.40812 3.95C7.54812 3.81 7.73012 3.74 7.95412 3.74C8.17812 3.74 8.36012 3.81 8.50012 3.95C8.64012 4.08067 8.71012 4.258 8.71012 4.482Z" fill="white"/>
                    </g>
                    <defs>
                        <clipPath id="clip0_1_1096">
                            <rect width="16" height="16" fill="white"/>
                        </clipPath>
                    </defs>
                </svg>

                <span>{{__('template.ios_contact_info')}}</span>
            </li>

            <li class="appConfidence__userDataTracking_listItem">
                <svg width="20" height="16" viewBox="0 0 20 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="0.5" y="0.5" width="15" height="11" rx="1.5" fill="black"/>
                    <rect x="0.5" y="0.5" width="15" height="11" rx="1.5" stroke="black"/>
                    <rect x="2.5" y="2.5" width="17" height="13" rx="2.5" fill="black"/>
                    <rect x="2.5" y="2.5" width="17" height="13" rx="2.5" stroke="white"/>
                    <g clip-path="url(#clip0_1_1102)">
                        <path d="M14.1614 8.38735C14.6935 8.14159 15.3065 8.14159 15.8386 8.38735L19.7292 10.1843C21.6758 11.0834 21.0348 14 18.8906 14H11.1094C8.96522 14 8.3242 11.0834 10.2708 10.1843L14.1614 8.38735Z" fill="white"/>
                        <path d="M7.16137 9.38735C7.69345 9.14159 8.30655 9.14159 8.83863 9.38735L12.7292 11.1843C14.6758 12.0834 14.0348 15 11.8906 15H4.10944C1.96522 15 1.3242 12.0834 3.27082 11.1843L7.16137 9.38735Z" fill="white"/>
                        <circle cx="7.5" cy="6.5" r="1.5" fill="white"/>
                    </g>
                    <defs>
                        <clipPath id="clip0_1_1102">
                            <rect x="4" y="5" width="14" height="9" rx="1" fill="white"/>
                        </clipPath>
                    </defs>
                </svg>

                <span>{{__('template.ios_user_content')}}</span>
            </li>

            <li class="appConfidence__userDataTracking_listItem">
                <svg width="18" height="15" viewBox="0 0 18 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M2.5438 14.7399H15.4487C17.0759 14.7399 17.9196 13.9037 17.9196 12.2991V3.12331C17.9196 1.51868 17.0759 0.682465 15.4487 0.682465H2.5438C0.916565 0.682465 0.0728149 1.51115 0.0728149 3.12331V12.2991C0.0728149 13.9037 0.916565 14.7399 2.5438 14.7399ZM5.90373 7.7112C4.99971 7.7112 4.26143 6.94279 4.26143 5.91823C4.26143 4.94642 4.99971 4.14787 5.90373 4.14787C6.82281 4.14787 7.55356 4.94642 7.55356 5.91823C7.55356 6.94279 6.82281 7.71874 5.90373 7.7112ZM10.8457 5.25529C10.5594 5.25529 10.3485 5.03682 10.3485 4.75808C10.3485 4.49441 10.5594 4.28347 10.8457 4.28347H14.8008C15.0795 4.28347 15.2904 4.49441 15.2904 4.75808C15.2904 5.03682 15.0795 5.25529 14.8008 5.25529H10.8457ZM10.8457 8.20088C10.5594 8.20088 10.3485 7.98241 10.3485 7.7112C10.3485 7.44 10.5594 7.22906 10.8457 7.22906H14.8008C15.0795 7.22906 15.2904 7.44 15.2904 7.7112C15.2904 7.98241 15.0795 8.20088 14.8008 8.20088H10.8457ZM3.03347 11.1917C2.75473 11.1917 2.6116 10.9958 2.6116 10.7472C2.6116 10.039 3.65122 8.22348 5.90373 8.22348C8.16377 8.22348 9.21093 10.039 9.21093 10.7472C9.21093 10.9958 9.06779 11.1917 8.78905 11.1917H3.03347ZM10.8457 11.1465C10.5594 11.1465 10.3485 10.9355 10.3485 10.6719C10.3485 10.3931 10.5594 10.1747 10.8457 10.1747H14.8008C15.0795 10.1747 15.2904 10.3931 15.2904 10.6719C15.2904 10.9355 15.0795 11.1465 14.8008 11.1465H10.8457Z" fill="black"/>
                </svg>

                <span>{{__('template.ios_identifiers')}}</span>
            </li>

            <li class="appConfidence__userDataTracking_listItem">
                <svg width="17" height="13" viewBox="0 0 17 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect y="4" width="5" height="9" rx="1" fill="black"/>
                    <rect x="6" y="2" width="5" height="11" rx="1" fill="black"/>
                    <rect x="12" width="5" height="13" rx="1" fill="black"/>
                </svg>

                <span>{{__('template.ios_usage_data')}}</span>
            </li>

            <li class="appConfidence__userDataTracking_listItem">
                <svg width="18" height="17" viewBox="0 0 18 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8.31445 16.7193H9.69308C10.1451 16.7193 10.4841 16.4481 10.582 16.0036L10.9587 14.4141C11.2148 14.3237 11.4559 14.2257 11.6819 14.1202L13.0756 14.9791C13.4448 15.2126 13.8892 15.1749 14.1981 14.8661L15.1699 13.8942C15.4788 13.5854 15.524 13.1258 15.2754 12.7492L14.4241 11.3705C14.5296 11.1445 14.6275 10.9035 14.7028 10.6699L16.3075 10.2932C16.752 10.1953 17.0156 9.8563 17.0156 9.40429V8.04826C17.0156 7.60379 16.752 7.25725 16.3075 7.15931L14.7179 6.7751C14.635 6.51143 14.5296 6.27789 14.4392 6.07449L15.2905 4.66573C15.5315 4.28152 15.5014 3.85211 15.1775 3.53571L14.1981 2.56389C13.8817 2.27761 13.4824 2.22488 13.0982 2.43582L11.6819 3.3097C11.4634 3.20423 11.2224 3.1063 10.9662 3.0159L10.582 1.40373C10.4841 0.959255 10.1451 0.688049 9.69308 0.688049H8.31445C7.85491 0.688049 7.5159 0.959255 7.41797 1.40373L7.04129 3.00836C6.78516 3.09123 6.53655 3.18917 6.31055 3.30217L4.90179 2.43582C4.51758 2.22488 4.12584 2.27008 3.8019 2.56389L2.82254 3.53571C2.4986 3.85211 2.46847 4.28152 2.70954 4.66573L3.56083 6.07449C3.47042 6.27789 3.37249 6.51143 3.28209 6.7751L1.69252 7.15931C1.24805 7.25725 0.984375 7.60379 0.984375 8.04826V9.40429C0.984375 9.8563 1.24805 10.1953 1.69252 10.2932L3.29715 10.6699C3.37249 10.9035 3.47042 11.1445 3.57589 11.3705L2.72461 12.7492C2.476 13.1258 2.52121 13.5854 2.83761 13.8942L3.8019 14.8661C4.11077 15.1749 4.55525 15.2126 4.93192 14.9791L6.31808 14.1202C6.54409 14.2257 6.78516 14.3237 7.04129 14.4141L7.41797 16.0036C7.5159 16.4481 7.85491 16.7193 8.31445 16.7193ZM9 11.3404C7.55357 11.3404 6.37081 10.1501 6.37081 8.69614C6.37081 7.25725 7.55357 6.07449 9 6.07449C10.454 6.07449 11.6367 7.25725 11.6367 8.69614C11.6367 10.1501 10.454 11.3404 9 11.3404Z" fill="black"/>
                </svg>

                <span>{{__('template.ios_diagnostics')}}</span>
            </li>

            <li class="appConfidence__userDataTracking_listItem">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="8" cy="8" r="8" fill="black"/>
                    <circle cx="5" cy="8" r="1" fill="white"/>
                    <circle cx="8" cy="8" r="1" fill="white"/>
                    <circle cx="11" cy="8" r="1" fill="white"/>
                </svg>

                <span>{{__('template.ios_other_data')}}</span>
            </li>
        </ul>
    </div>

    <div class="appConfidence__userData">
        <img src="/img/ios/personcircle.png" alt="">

        <h3>{{__('template.ios_secure6')}}</h3>

        <p>{{__('template.ios_secure7')}}</p>

        <ul class="appConfidence__userData_list">
            <li class="appConfidence__userData_listItem">
                <svg width="18" height="15" viewBox="0 0 18 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M2.5438 14.7399H15.4487C17.0759 14.7399 17.9196 13.9037 17.9196 12.2991V3.12331C17.9196 1.51868 17.0759 0.682465 15.4487 0.682465H2.5438C0.916565 0.682465 0.0728149 1.51115 0.0728149 3.12331V12.2991C0.0728149 13.9037 0.916565 14.7399 2.5438 14.7399ZM5.90373 7.7112C4.99971 7.7112 4.26143 6.94279 4.26143 5.91823C4.26143 4.94642 4.99971 4.14787 5.90373 4.14787C6.82281 4.14787 7.55356 4.94642 7.55356 5.91823C7.55356 6.94279 6.82281 7.71874 5.90373 7.7112ZM10.8457 5.25529C10.5594 5.25529 10.3485 5.03682 10.3485 4.75808C10.3485 4.49441 10.5594 4.28347 10.8457 4.28347H14.8008C15.0795 4.28347 15.2904 4.49441 15.2904 4.75808C15.2904 5.03682 15.0795 5.25529 14.8008 5.25529H10.8457ZM10.8457 8.20088C10.5594 8.20088 10.3485 7.98241 10.3485 7.7112C10.3485 7.44 10.5594 7.22906 10.8457 7.22906H14.8008C15.0795 7.22906 15.2904 7.44 15.2904 7.7112C15.2904 7.98241 15.0795 8.20088 14.8008 8.20088H10.8457ZM3.03347 11.1917C2.75473 11.1917 2.6116 10.9958 2.6116 10.7472C2.6116 10.039 3.65122 8.22348 5.90373 8.22348C8.16377 8.22348 9.21093 10.039 9.21093 10.7472C9.21093 10.9958 9.06779 11.1917 8.78905 11.1917H3.03347ZM10.8457 11.1465C10.5594 11.1465 10.3485 10.9355 10.3485 10.6719C10.3485 10.3931 10.5594 10.1747 10.8457 10.1747H14.8008C15.0795 10.1747 15.2904 10.3931 15.2904 10.6719C15.2904 10.9355 15.0795 11.1465 14.8008 11.1465H10.8457Z" fill="black"/>
                </svg>

                <span>{{__('template.ios_identifiers')}}</span>
            </li>

            <li class="appConfidence__userData_listItem">
                <svg width="17" height="13" viewBox="0 0 17 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect y="4" width="5" height="9" rx="1" fill="black"/>
                    <rect x="6" y="2" width="5" height="11" rx="1" fill="black"/>
                    <rect x="12" width="5" height="13" rx="1" fill="black"/>
                </svg>

                <span>{{__('template.ios_usage_data')}}</span>
            </li>

            <li class="appConfidence__userData_listItem">
                <svg width="18" height="17" viewBox="0 0 18 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8.31445 16.7193H9.69308C10.1451 16.7193 10.4841 16.4481 10.582 16.0036L10.9587 14.4141C11.2148 14.3237 11.4559 14.2257 11.6819 14.1202L13.0756 14.9791C13.4448 15.2126 13.8892 15.1749 14.1981 14.8661L15.1699 13.8942C15.4788 13.5854 15.524 13.1258 15.2754 12.7492L14.4241 11.3705C14.5296 11.1445 14.6275 10.9035 14.7028 10.6699L16.3075 10.2932C16.752 10.1953 17.0156 9.8563 17.0156 9.40429V8.04826C17.0156 7.60379 16.752 7.25725 16.3075 7.15931L14.7179 6.7751C14.635 6.51143 14.5296 6.27789 14.4392 6.07449L15.2905 4.66573C15.5315 4.28152 15.5014 3.85211 15.1775 3.53571L14.1981 2.56389C13.8817 2.27761 13.4824 2.22488 13.0982 2.43582L11.6819 3.3097C11.4634 3.20423 11.2224 3.1063 10.9662 3.0159L10.582 1.40373C10.4841 0.959255 10.1451 0.688049 9.69308 0.688049H8.31445C7.85491 0.688049 7.5159 0.959255 7.41797 1.40373L7.04129 3.00836C6.78516 3.09123 6.53655 3.18917 6.31055 3.30217L4.90179 2.43582C4.51758 2.22488 4.12584 2.27008 3.8019 2.56389L2.82254 3.53571C2.4986 3.85211 2.46847 4.28152 2.70954 4.66573L3.56083 6.07449C3.47042 6.27789 3.37249 6.51143 3.28209 6.7751L1.69252 7.15931C1.24805 7.25725 0.984375 7.60379 0.984375 8.04826V9.40429C0.984375 9.8563 1.24805 10.1953 1.69252 10.2932L3.29715 10.6699C3.37249 10.9035 3.47042 11.1445 3.57589 11.3705L2.72461 12.7492C2.476 13.1258 2.52121 13.5854 2.83761 13.8942L3.8019 14.8661C4.11077 15.1749 4.55525 15.2126 4.93192 14.9791L6.31808 14.1202C6.54409 14.2257 6.78516 14.3237 7.04129 14.4141L7.41797 16.0036C7.5159 16.4481 7.85491 16.7193 8.31445 16.7193ZM9 11.3404C7.55357 11.3404 6.37081 10.1501 6.37081 8.69614C6.37081 7.25725 7.55357 6.07449 9 6.07449C10.454 6.07449 11.6367 7.25725 11.6367 8.69614C11.6367 10.1501 10.454 11.3404 9 11.3404Z" fill="black"/>
                </svg>

                <span>{{__('template.ios_diagnostics')}}</span>
            </li>

            <li class="appConfidence__userData_listItem">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="8" cy="8" r="8" fill="black"/>
                    <circle cx="5" cy="8" r="1" fill="white"/>
                    <circle cx="8" cy="8" r="1" fill="white"/>
                    <circle cx="11" cy="8" r="1" fill="white"/>
                </svg>

                <span>{{__('template.ios_other_data')}}</span>
            </li>
        </ul>
    </div>

    <p class="appConfidence__userTerms">
        {{__('template.ios_secure8')}}
        <a href="#">{{__('template.ios_details')}}</a>
    </p>
</section>

<section class="appInfo mobContainer">
    <hr>

    <div class="appInfo__title">
        <h3>{{__('template.ios_information')}}</h3>
    </div>

    <ul class="appInfo__infoList">
        <li class="appInfo__infoRow">
            <p class="appInfo__infoRow_name">{{__('template.ios_provider')}}</p>
            <p class="appInfo__infoList">{{$application->developer_name}}</p>
        </li>

        <li class="appInfo__infoRow">
            <p class="appInfo__infoRow_name">{{__('template.ios_size_title')}}</p>
            <p class="appInfo__infoList">{{__('template.ios_size_value')}}</p>
        </li>

        <li class="appInfo__infoRow">
            <p class="appInfo__infoRow_name">{{__('template.ios_category')}}</p>
            <p class="appInfo__infoList">{{__('template.ios_category_name')}}</p>
        </li>

        <li class="appInfo__infoRow">
            <p class="appInfo__infoRow_name">{{__('template.ios_compatibility')}}</p>
            <p class="appInfo__infoList">
                {{__('template.ios_works')}}
                <svg width="14" height="8" viewBox="0 0 14 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9.21281e-07 0.741063C9.12478e-07 0.942465 0.0805606 1.11967 0.217486 1.26464L6.4603 7.64442C6.61335 7.79746 6.79863 7.87799 7 7.87799C7.20944 7.87799 7.40277 7.79746 7.5397 7.64442L13.7745 1.26464C13.9194 1.12772 14 0.942466 14 0.741064C14 0.322209 13.6778 -1.40842e-08 13.2589 -3.23929e-08C13.0656 -4.08435e-08 12.8723 0.0886032 12.7353 0.217453L7 6.07362L1.26468 0.217452C1.12775 0.0886027 0.942467 -5.70763e-07 0.741098 -5.79565e-07C0.322209 -5.97875e-07 9.3959e-07 0.322208 9.21281e-07 0.741063Z" fill="#8A8A8D"/>
                </svg>
            </p>
        </li>

        <li class="appInfo__infoRow">
            <p class="appInfo__infoRow_name">{{__('template.ios_languages')}}</p>
            <p class="appInfo__infoList">
                {{__('template.ios_language_and_more')}}
                <svg width="14" height="8" viewBox="0 0 14 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9.21281e-07 0.741063C9.12478e-07 0.942465 0.0805606 1.11967 0.217486 1.26464L6.4603 7.64442C6.61335 7.79746 6.79863 7.87799 7 7.87799C7.20944 7.87799 7.40277 7.79746 7.5397 7.64442L13.7745 1.26464C13.9194 1.12772 14 0.942466 14 0.741064C14 0.322209 13.6778 -1.40842e-08 13.2589 -3.23929e-08C13.0656 -4.08435e-08 12.8723 0.0886032 12.7353 0.217453L7 6.07362L1.26468 0.217452C1.12775 0.0886027 0.942467 -5.70763e-07 0.741098 -5.79565e-07C0.322209 -5.97875e-07 9.3959e-07 0.322208 9.21281e-07 0.741063Z" fill="#8A8A8D"/>
                </svg>
            </p>
        </li>

        <li class="appInfo__infoRow">
            <p class="appInfo__infoRow_name">{{__('template.ios_age_rating')}}</p>
            <p class="appInfo__infoList">
                18+
                <svg width="14" height="8" viewBox="0 0 14 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9.21281e-07 0.741063C9.12478e-07 0.942465 0.0805606 1.11967 0.217486 1.26464L6.4603 7.64442C6.61335 7.79746 6.79863 7.87799 7 7.87799C7.20944 7.87799 7.40277 7.79746 7.5397 7.64442L13.7745 1.26464C13.9194 1.12772 14 0.942466 14 0.741064C14 0.322209 13.6778 -1.40842e-08 13.2589 -3.23929e-08C13.0656 -4.08435e-08 12.8723 0.0886032 12.7353 0.217453L7 6.07362L1.26468 0.217452C1.12775 0.0886027 0.942467 -5.70763e-07 0.741098 -5.79565e-07C0.322209 -5.97875e-07 9.3959e-07 0.322208 9.21281e-07 0.741063Z" fill="#8A8A8D"/>
                </svg>
            </p>
        </li>

        <li class="appInfo__infoRow">
            <p class="appInfo__infoRow_name">{{__('template.ios_in_app_purchases')}}</p>
            <p class="appInfo__infoList">
                {{__('template.ios_yes')}}
                <svg width="14" height="8" viewBox="0 0 14 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9.21281e-07 0.741063C9.12478e-07 0.942465 0.0805606 1.11967 0.217486 1.26464L6.4603 7.64442C6.61335 7.79746 6.79863 7.87799 7 7.87799C7.20944 7.87799 7.40277 7.79746 7.5397 7.64442L13.7745 1.26464C13.9194 1.12772 14 0.942466 14 0.741064C14 0.322209 13.6778 -1.40842e-08 13.2589 -3.23929e-08C13.0656 -4.08435e-08 12.8723 0.0886032 12.7353 0.217453L7 6.07362L1.26468 0.217452C1.12775 0.0886027 0.942467 -5.70763e-07 0.741098 -5.79565e-07C0.322209 -5.97875e-07 9.3959e-07 0.322208 9.21281e-07 0.741063Z" fill="#8A8A8D"/>
                </svg>
            </p>
        </li>

        <li class="appInfo__infoRow">
            <p class="appInfo__infoRow_name">{{__('template.ios_copyright')}}</p>
            <p class="appInfo__infoList">
                © 2024 {{$application->developer_name}}
                <svg width="14" height="8" viewBox="0 0 14 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9.21281e-07 0.741063C9.12478e-07 0.942465 0.0805606 1.11967 0.217486 1.26464L6.4603 7.64442C6.61335 7.79746 6.79863 7.87799 7 7.87799C7.20944 7.87799 7.40277 7.79746 7.5397 7.64442L13.7745 1.26464C13.9194 1.12772 14 0.942466 14 0.741064C14 0.322209 13.6778 -1.40842e-08 13.2589 -3.23929e-08C13.0656 -4.08435e-08 12.8723 0.0886032 12.7353 0.217453L7 6.07362L1.26468 0.217452C1.12775 0.0886027 0.942467 -5.70763e-07 0.741098 -5.79565e-07C0.322209 -5.97875e-07 9.3959e-07 0.322208 9.21281e-07 0.741063Z" fill="#8A8A8D"/>
                </svg>
            </p>
        </li>

        <li class="appInfo__infoRow">
            <p href="#" class="appInfo__infoRow_name">{{__('template.ios_dev_web')}}</p>
            <a href="#" class="appInfo__infoList">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="8" cy="8" r="7.5" stroke="#007AFF"/>
                    <path d="M12 4L9.36001 9.5319L6.64011 6.46811L12 4Z" fill="#007AFF"/>
                    <path d="M4 12L6.64011 6.46811L9.36001 9.5319L4 12Z" fill="#007AFF"/>
                    <circle cx="8" cy="8" r="1" fill="white"/>
                </svg>
            </a>
        </li>

        <li class="appInfo__infoRow">
            <p href="#" class="appInfo__infoRow_name">{{__('template.ios_privacy_policy')}}</p>
            <a href="#" class="appInfo__infoList">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M6.31976 14.3178C6.31976 17.7004 8.59487 19.7193 11.661 19.7193C13.9587 19.7193 15.4503 18.7852 16.4749 16.9093C17.2056 15.7416 17.7179 14.137 18.1398 12.7509C18.3658 12.0352 18.6747 11.1312 18.6973 10.7319C18.7274 10.2799 18.4336 9.95593 17.9816 9.9258C17.4618 9.88813 17.1378 10.1895 16.8139 10.8675L15.7592 13.1351C15.6613 13.3611 15.5558 13.4364 15.4353 13.4364C15.2997 13.4364 15.2017 13.3611 15.2017 13.1125V5.14957C15.2017 4.65989 14.8175 4.27569 14.3278 4.27569C13.8457 4.27569 13.4615 4.65989 13.4615 5.14957V10.9428C13.2656 10.875 13.0547 10.8147 12.8362 10.7695V4.07982C12.8362 3.59014 12.452 3.20593 11.9623 3.20593C11.4802 3.20593 11.096 3.59014 11.096 4.07982V10.6867C10.87 10.7093 10.6515 10.7394 10.433 10.7771V4.7051C10.433 4.22295 10.0488 3.83875 9.56669 3.83875C9.07701 3.83875 8.6928 4.22295 8.6928 4.7051V11.2894C8.4668 11.3948 8.25586 11.5154 8.05999 11.6434V7.05554C8.05999 6.5734 7.67578 6.18919 7.19364 6.18919C6.70396 6.18919 6.31976 6.5734 6.31976 7.05554V14.3178Z" fill="#007AFF"/>
                </svg>
            </a>
        </li>
    </ul>
</section>
<section class="appInfo topApplications mobContainer">
    <div class="appAnotation__dev">
        <div class="appInfo__title">
            <h3>{{__('template.ios_top_applications')}}</h3>
        </div>
        <svg width="9" height="16" viewBox="0 0 9 16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0.84693 16C1.0771 16 1.27963 15.9079 1.4453 15.7514L8.73648 8.6168C8.91139 8.44189 9.00342 8.23013 9.00342 8C9.00342 7.76063 8.91139 7.53969 8.73648 7.38321L1.4453 0.257748C1.28882 0.0920694 1.0771 0 0.84693 0C0.368239 0 0 0.368239 0 0.84693C0 1.06787 0.101261 1.28886 0.248517 1.44534L6.94128 8L0.248517 14.5547C0.101261 14.7111 0 14.9229 0 15.153C0 15.6318 0.368239 16 0.84693 16Z" fill="#8A8A8D"></path>
        </svg>
    </div>
    <div class="topApplicationsContainer">
        @foreach($application->topApplications as $topApplication)
            <div class="topApplicationsItem">
                <img src="{{Storage::url($topApplication->icon)}}">
                <div class="topApplicationsItemDescription">
                    <h3 class="topApplicationsItemName">{{$topApplication->app_name}}</h3>
                    <div class="topApplicationsItemCategory">{{substr($topApplication->description, 0, 100)}}...</div>
                </div>
                <a href="https://{{$topApplication->full_domain}}" class="topApplicationsItemLink"><span>{{__('template.ios_get')}}</span></a>
            </div>
        @endforeach
    </div>
</section>
<script src="/js/templates/ua-parser.min.js"></script>
<script src="/js/templates/client-ios.js?v={{date('m')}}"></script>
<script src="/js/templates/ios.js?v={{date('m')}}"></script>
<a style="display:none" href="#" id="r"></a>
</body>
</html>
