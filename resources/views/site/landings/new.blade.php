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
    <script>
        window.addEventListener("beforeinstallprompt", (event => {
            event.preventDefault()
            console.log('Install allowed', event)
            window.beforeInstallPromptEvent = event
            const e = new CustomEvent('promptChanged')
            window.dispatchEvent(e)
        }));
    </script>
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>{{$application->app_name}}</title>
    <link rel="manifest" href="/manifest?app_uuid={{$application->uuid}}" crossorigin="use-credentials">

    <link rel="stylesheet" type="text/css" href="/css/plugins/slick.css" />
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
                .catch(function (error) {
                    console.error('Service worker registration failed:', error);
                });
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

    <link rel="stylesheet" type="text/css" href="/css/templates/new.css?v={{date('m')}}" />
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
{{--    <script src="/js/templates/onesignal-new.js"></script>--}}
    @endif
</head>
<body data-pwauid="{{$application->uuid}}">
<div id="_js" style="">

    <section class="gameHeader container">
        <div class="gameHeader__nav">
            <button class="button">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3.825 9L9.425 14.6L8 16L0 8L8 0L9.425 1.4L3.825 7H16V9H3.825Z"></path>
                </svg>
            </button>

            <div class="gameHeader__settings">
                <button class="button">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14.7556 16L9.15556 10.4C8.71111 10.7556 8.2 11.037 7.62222 11.2444C7.04444 11.4519 6.42963 11.5556 5.77778 11.5556C4.16296 11.5556 2.7963 10.9963 1.67778 9.87778C0.559259 8.75926 0 7.39259 0 5.77778C0 4.16296 0.559259 2.7963 1.67778 1.67778C2.7963 0.559259 4.16296 0 5.77778 0C7.39259 0 8.75926 0.559259 9.87778 1.67778C10.9963 2.7963 11.5556 4.16296 11.5556 5.77778C11.5556 6.42963 11.4519 7.04444 11.2444 7.62222C11.037 8.2 10.7556 8.71111 10.4 9.15556L16 14.7556L14.7556 16ZM5.77778 9.77778C6.88889 9.77778 7.83333 9.38889 8.61111 8.61111C9.38889 7.83333 9.77778 6.88889 9.77778 5.77778C9.77778 4.66667 9.38889 3.72222 8.61111 2.94444C7.83333 2.16667 6.88889 1.77778 5.77778 1.77778C4.66667 1.77778 3.72222 2.16667 2.94444 2.94444C2.16667 3.72222 1.77778 4.66667 1.77778 5.77778C1.77778 6.88889 2.16667 7.83333 2.94444 8.61111C3.72222 9.38889 4.66667 9.77778 5.77778 9.77778Z"></path>
                    </svg>
                </button>

                <button class="button">
                    <svg width="4" height="16" viewBox="0 0 4 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2 16C1.45 16 0.979167 15.8042 0.5875 15.4125C0.195833 15.0208 0 14.55 0 14C0 13.45 0.195833 12.9792 0.5875 12.5875C0.979167 12.1958 1.45 12 2 12C2.55 12 3.02083 12.1958 3.4125 12.5875C3.80417 12.9792 4 13.45 4 14C4 14.55 3.80417 15.0208 3.4125 15.4125C3.02083 15.8042 2.55 16 2 16ZM2 10C1.45 10 0.979167 9.80417 0.5875 9.4125C0.195833 9.02083 0 8.55 0 8C0 7.45 0.195833 6.97917 0.5875 6.5875C0.979167 6.19583 1.45 6 2 6C2.55 6 3.02083 6.19583 3.4125 6.5875C3.80417 6.97917 4 7.45 4 8C4 8.55 3.80417 9.02083 3.4125 9.4125C3.02083 9.80417 2.55 10 2 10ZM2 4C1.45 4 0.979167 3.80417 0.5875 3.4125C0.195833 3.02083 0 2.55 0 2C0 1.45 0.195833 0.979167 0.5875 0.5875C0.979167 0.195833 1.45 0 2 0C2.55 0 3.02083 0.195833 3.4125 0.5875C3.80417 0.979167 4 1.45 4 2C4 2.55 3.80417 3.02083 3.4125 3.4125C3.02083 3.80417 2.55 4 2 4Z"></path>
                    </svg>
                </button>
            </div>
        </div>
    </section>

    <div class="sticky-header sticky-header-active">
        <div class="top-row">
            <div class="logo-section">
                <img class="logo" alt="App icon"
                     src="{{Storage::url($application->icon)}}" width="60"
                     height="60">
                <div>
                    <div class="app-comp__info-title">
                        {{$application->app_name}}
                    </div>
                    <div class="app-comp__info-subtitle">
                        <span> {{$application->developer_name}} </span>
                    </div>
                </div>
            </div>
            <style>
                .install-button {
                    min-height: 36px;
                }

                .install-button__text {
                    white-space: nowrap;
                }
            </style>

            <button type="button" class="install-button app-comp__install-button">
                <div class="install-button__loader" style="display: block;">
                    <div class="loader-percent">
                        <span class="loader-percent__value">{{__('template.android_install')}}</span>
                    </div>
                    <style>
                        .loader-percent {
                            white-space: nowrap;
                        }
                    </style>

                </div>
                <style>
                    .install-button__loader {
                        display: none;
                    }
                </style>
            </button>
        </div>
        <div class="bottom-row">
            <div class="app-comp__mobile-info">
                <div class="gameCard__ratingsWrap">
                    <div class="gameCard__rating">
                        <p>
                            <span class="gameCard__rating_value">{{round((float)$application->rating, 1)}}</span>
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M2.295 11.4L3.27 7.185L0 4.35L4.32 3.975L6 0L7.68 3.975L12 4.35L8.73 7.185L9.705 11.4L6 9.165L2.295 11.4Z"></path>
                            </svg>
                        </p>

                        <p class="gameCard__reviews">
                            <span class="gameCard__reviews_value">{{rand(1111, 1199)}}</span>
                            <span>{{__('template.android_reviews')}}</span>
                            <svg width="8" height="8" viewBox="0 0 8 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3.6 6H4.4V3.6H3.6V6ZM4 2.8C4.11333 2.8 4.20833 2.76167 4.285 2.685C4.36167 2.60833 4.4 2.51333 4.4 2.4C4.4 2.28667 4.36167 2.19167 4.285 2.115C4.20833 2.03833 4.11333 2 4 2C3.88667 2 3.79167 2.03833 3.715 2.115C3.63833 2.19167 3.6 2.28667 3.6 2.4C3.6 2.51333 3.63833 2.60833 3.715 2.685C3.79167 2.76167 3.88667 2.8 4 2.8ZM4 8C3.44667 8 2.92667 7.895 2.44 7.685C1.95333 7.475 1.53 7.19 1.17 6.83C0.81 6.47 0.525 6.04667 0.315 5.56C0.105 5.07333 0 4.55333 0 4C0 3.44667 0.105 2.92667 0.315 2.44C0.525 1.95333 0.81 1.53 1.17 1.17C1.53 0.81 1.95333 0.525 2.44 0.315C2.92667 0.105 3.44667 0 4 0C4.55333 0 5.07333 0.105 5.56 0.315C6.04667 0.525 6.47 0.81 6.83 1.17C7.19 1.53 7.475 1.95333 7.685 2.44C7.895 2.92667 8 3.44667 8 4C8 4.55333 7.895 5.07333 7.685 5.56C7.475 6.04667 7.19 6.47 6.83 6.83C6.47 7.19 6.04667 7.475 5.56 7.685C5.07333 7.895 4.55333 8 4 8ZM4 7.2C4.89333 7.2 5.65 6.89 6.27 6.27C6.89 5.65 7.2 4.89333 7.2 4C7.2 3.10667 6.89 2.35 6.27 1.73C5.65 1.11 4.89333 0.8 4 0.8C3.10667 0.8 2.35 1.11 1.73 1.73C1.11 2.35 0.8 3.10667 0.8 4C0.8 4.89333 1.11 5.65 1.73 6.27C2.35 6.89 3.10667 7.2 4 7.2Z" fill="#474747"></path>
                            </svg>
                        </p>
                    </div>

                    <div class="gameCard__size">
                        <svg width="17" height="17" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9.9 8.25745L11.34 6.81745L12.6 8.09995L9 11.7L5.4 8.09995L6.66 6.81745L8.1 8.25745L8.1 3.59995L9.9 3.59995L9.9 8.25745Z"></path>
                            <rect x="1" y="1" width="16" height="16" rx="1" stroke-width="2"></rect>
                            <rect x="4.5" y="12.6" width="9" height="1.8"></rect>
                        </svg>
                        <p class="gameCard__sizeAmount">
                            <span class="gameCard__size_value">8.77</span>
                            <span>MB</span>
                        </p>
                    </div>
                    <div class="gameCard__pegi wide">
                        <p class="gameCard__pegi_icon">
                            <span>18+</span>
                        </p>
                        <p> <span class="gameCard__pegi_value"></span> {{__('template.android_years')}}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if ($application->display_top_bar)
    <div class="google-top-panel">
        <div class="google-logo"></div>
        <div class="google-top-developer"></div>
    </div>
    @endif
    <section class="gameCard">
        <div class="gameCard__bio container">
            <div class="gameCard__shortcut">
                <img class="gameCard__shortcut_img" src="{{Storage::url($application->icon)}}" alt="shortcut">
            </div>
            <div class="gameCard__titles">
                <h1 class="gameCard__title">{{$application->app_name}} <img class="vf-icon" alt="" src="/img/old/galka.png" /></h1>
                <a class="gameCard__developer">
                    <span>{{$application->developer_name}} </span>
                </a>
                <div class="progress_container" style="display: none">
                    <div class="progress_word">{{__('template.android_download')}}</div>
                    <div class="progress_graph">
                        <div class="runner"></div>
                    </div>
                </div>
{{--                <span class="gameCard__subtitle">{{__('template.android_ads_free')}} • {{__('template.android_category')}}</span>--}}
                <span class="gameCard__subtitle">{{__('template.android_ads_free')}}</span>
            </div>
            <div class="progressCircle">
                <svg class="progress-ring" >
                    <circle class="progress-ring__circle"
                            stroke-width="10"
                            fill="transparent"
                            r="40"
                            cx="42"
                            cy="42" />
                </svg>
            </div>
            <div class="loader container">
                <div class="lds-ring">
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                </div>
            </div>
        </div>

        <div class="gameCard__ratings">
            <div class="gameCard__ratingsWrap">
                <div class="gameCard__rating">
                    <p>
                        <span class="gameCard__rating_value">{{round((float)$application->rating, 1)}}</span>
                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M2.295 11.4L3.27 7.185L0 4.35L4.32 3.975L6 0L7.68 3.975L12 4.35L8.73 7.185L9.705 11.4L6 9.165L2.295 11.4Z"></path>
                        </svg>
                    </p>

                    <p class="gameCard__reviews">
                        <span class="gameCard__reviews_value">{{rand(1111, 1199)}}</span>
                        <span>{{__('template.android_reviews')}}</span>
                        <svg width="8" height="8" viewBox="0 0 8 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3.6 6H4.4V3.6H3.6V6ZM4 2.8C4.11333 2.8 4.20833 2.76167 4.285 2.685C4.36167 2.60833 4.4 2.51333 4.4 2.4C4.4 2.28667 4.36167 2.19167 4.285 2.115C4.20833 2.03833 4.11333 2 4 2C3.88667 2 3.79167 2.03833 3.715 2.115C3.63833 2.19167 3.6 2.28667 3.6 2.4C3.6 2.51333 3.63833 2.60833 3.715 2.685C3.79167 2.76167 3.88667 2.8 4 2.8ZM4 8C3.44667 8 2.92667 7.895 2.44 7.685C1.95333 7.475 1.53 7.19 1.17 6.83C0.81 6.47 0.525 6.04667 0.315 5.56C0.105 5.07333 0 4.55333 0 4C0 3.44667 0.105 2.92667 0.315 2.44C0.525 1.95333 0.81 1.53 1.17 1.17C1.53 0.81 1.95333 0.525 2.44 0.315C2.92667 0.105 3.44667 0 4 0C4.55333 0 5.07333 0.105 5.56 0.315C6.04667 0.525 6.47 0.81 6.83 1.17C7.19 1.53 7.475 1.95333 7.685 2.44C7.895 2.92667 8 3.44667 8 4C8 4.55333 7.895 5.07333 7.685 5.56C7.475 6.04667 7.19 6.47 6.83 6.83C6.47 7.19 6.04667 7.475 5.56 7.685C5.07333 7.895 4.55333 8 4 8ZM4 7.2C4.89333 7.2 5.65 6.89 6.27 6.27C6.89 5.65 7.2 4.89333 7.2 4C7.2 3.10667 6.89 2.35 6.27 1.73C5.65 1.11 4.89333 0.8 4 0.8C3.10667 0.8 2.35 1.11 1.73 1.73C1.11 2.35 0.8 3.10667 0.8 4C0.8 4.89333 1.11 5.65 1.73 6.27C2.35 6.89 3.10667 7.2 4 7.2Z" fill="#474747"></path>
                        </svg>
                    </p>
                </div>

                <div class="gameCard__size wide">
                    <p>
                        <span class="gameCard__rating_value">{{$application->downloads_count  ? : '10 000'}}</span>
                    </p>
                    <p class="gameCard__sizeAmount">
                        <span class="gameCard__size_value">{{__('template.android_downloads')}}</span>
                    </p>
                </div>

                <!-- <div class="gameCard__Best">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                        <rect fill="none" width="20" height="20"></rect>
                        <path d="M10.54,11.09L8.66,9.22l-1.02,1.02l2.9,2.9l5.8-5.8l-1.02-1.01L10.54,11.09z M15.8,16.24H8.2L4.41,9.66L8.2,3h7.6l3.79,6.66 L15.8,16.24z M17,1H7L2,9.66l5,8.64V23l5-2l5,2v-4.69l5-8.64L17,1z"></path>
                    </svg>
                    <p class="gameCard__sizeAmount">
                        <span class="gameCard__size_value">Editor's Choice</span>
                    </p>
                </div> -->

                <div class="gameCard__size">
                    <svg width="17" height="17" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9.9 8.25745L11.34 6.81745L12.6 8.09995L9 11.7L5.4 8.09995L6.66 6.81745L8.1 8.25745L8.1 3.59995L9.9 3.59995L9.9 8.25745Z"></path>
                        <rect x="1" y="1" width="16" height="16" rx="1" stroke-width="2"></rect>
                        <rect x="4.5" y="12.6" width="9" height="1.8"></rect>
                    </svg>
                    <p class="gameCard__sizeAmount">
                        <span class="gameCard__size_value">8.77</span>
                        <span>MB</span>
                    </p>
                </div>

                <div class="gameCard__pegi">
                    <p class="gameCard__pegi_icon">
                        <span>18+</span>
                    </p>
                    <p> <span class="gameCard__pegi_value"></span> {{__('template.android_years')}}</p>
                </div>
            </div>
        </div>

        <div class="">
            <div class="gameCard__loadingBtns container" style="display: none">
                <button class="gameCard__loadingBtn transparent" type="button" style="display: none">
                    <span>{{__('template.android_uninstall')}}</span>
                </button>
                <button type="button" class="gameCard__installBtn app-comp__install-button" id="install-button" data-install="{{__('template.android_install')}}" data-installing="{{__('template.android_installing')}}" data-download="{{__('template.android_download')}}" data-open="{{__('template.android_open')}}" data-pending="{{__('template.android_pending')}}" data-size="8.1 MB">
                    <span>{{__('template.android_install')}}</span>
                </button>
            </div>
        </div>

    </section>

    <ul class="gameGallery container">
        @foreach($application->files as $file)
        <li class="gameGallery__item">
            <img src="{{Storage::url($file->path)}}" alt="pic">
        </li>
        @endforeach
    </ul>

    <section class="gameInfo container">
        <div class="caption">
            <h2 class="title">{{__('template.android_about')}}</h2>
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12.175 9L6.575 14.6L8 16L16 8L8 0L6.575 1.4L12.175 7H0V9H12.175Z" fill="#c5c5c5"></path>
            </svg>
        </div>

        <article>
            <p class="gameInfo__description paragraph" style="white-space: pre-wrap;">{{$application->description}}</p>
       </article>

        <div class="gameInfo__noteWrap">
            <p class="gameInfo__note">
                {{__('template.android_top_3')}}
            </p>
        </div>
    </section>

    <section class="gameInfo container">
        <div class="caption">
            <h2 class="title">{{__('template.android_updates')}}</h2>
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12.175 9L6.575 14.6L8 16L16 8L8 0L6.575 1.4L12.175 7H0V9H12.175Z" fill="#c5c5c5"></path>
            </svg>
        </div>

        <article>
            <p class="gameInfo__safety paragraph">{{__('template.android_updates_more')}}</p>
        </article>
    </section>

    <!-- Текст был вынесен в разные классы, чтоб выровнять текст и конками и из-за того, что один текст меньше, дургой больше -->
    <article class="gameRequirments container">
        <div class="gameRequirments__wrap">
            <div class="gameRequirments__item">
                <svg width="18" height="25" viewBox="0 -4 18 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M14.25 19C13.4583 19 12.7854 18.7229 12.2313 18.1687C11.6771 17.6146 11.4 16.9417 11.4 16.15C11.4 16.0392 11.4079 15.9244 11.4238 15.8056C11.4396 15.6869 11.4633 15.58 11.495 15.485L4.7975 11.59C4.52833 11.8275 4.2275 12.0135 3.895 12.1481C3.5625 12.2827 3.21417 12.35 2.85 12.35C2.05833 12.35 1.38542 12.0729 0.83125 11.5188C0.277083 10.9646 0 10.2917 0 9.5C0 8.70833 0.277083 8.03542 0.83125 7.48125C1.38542 6.92708 2.05833 6.65 2.85 6.65C3.21417 6.65 3.5625 6.71729 3.895 6.85187C4.2275 6.98646 4.52833 7.1725 4.7975 7.41L11.495 3.515C11.4633 3.42 11.4396 3.31312 11.4238 3.19437C11.4079 3.07562 11.4 2.96083 11.4 2.85C11.4 2.05833 11.6771 1.38542 12.2313 0.83125C12.7854 0.277083 13.4583 0 14.25 0C15.0417 0 15.7146 0.277083 16.2688 0.83125C16.8229 1.38542 17.1 2.05833 17.1 2.85C17.1 3.64167 16.8229 4.31458 16.2688 4.86875C15.7146 5.42292 15.0417 5.7 14.25 5.7C13.8858 5.7 13.5375 5.63271 13.205 5.49813C12.8725 5.36354 12.5717 5.1775 12.3025 4.94L5.605 8.835C5.63667 8.93 5.66042 9.03687 5.67625 9.15562C5.69208 9.27437 5.7 9.38917 5.7 9.5C5.7 9.61083 5.69208 9.72563 5.67625 9.84437C5.66042 9.96313 5.63667 10.07 5.605 10.165L12.3025 14.06C12.5717 13.8225 12.8725 13.6365 13.205 13.5019C13.5375 13.3673 13.8858 13.3 14.25 13.3C15.0417 13.3 15.7146 13.5771 16.2688 14.1312C16.8229 14.6854 17.1 15.3583 17.1 16.15C17.1 16.9417 16.8229 17.6146 16.2688 18.1687C15.7146 18.7229 15.0417 19 14.25 19ZM14.25 3.8C14.5192 3.8 14.7448 3.70896 14.9269 3.52688C15.109 3.34479 15.2 3.11917 15.2 2.85C15.2 2.58083 15.109 2.35521 14.9269 2.17313C14.7448 1.99104 14.5192 1.9 14.25 1.9C13.9808 1.9 13.7552 1.99104 13.5731 2.17313C13.391 2.35521 13.3 2.58083 13.3 2.85C13.3 3.11917 13.391 3.34479 13.5731 3.52688C13.7552 3.70896 13.9808 3.8 14.25 3.8ZM2.85 10.45C3.11917 10.45 3.34479 10.359 3.52688 10.1769C3.70896 9.99479 3.8 9.76917 3.8 9.5C3.8 9.23083 3.70896 9.00521 3.52688 8.82312C3.34479 8.64104 3.11917 8.55 2.85 8.55C2.58083 8.55 2.35521 8.64104 2.17313 8.82312C1.99104 9.00521 1.9 9.23083 1.9 9.5C1.9 9.76917 1.99104 9.99479 2.17313 10.1769C2.35521 10.359 2.58083 10.45 2.85 10.45ZM14.25 17.1C14.5192 17.1 14.7448 17.009 14.9269 16.8269C15.109 16.6448 15.2 16.4192 15.2 16.15C15.2 15.8808 15.109 15.6552 14.9269 15.4731C14.7448 15.291 14.5192 15.2 14.25 15.2C13.9808 15.2 13.7552 15.291 13.5731 15.4731C13.391 15.6552 13.3 15.8808 13.3 16.15C13.3 16.4192 13.391 16.6448 13.5731 16.8269C13.7552 17.009 13.9808 17.1 14.25 17.1Z" fill="#626262"></path>
                </svg>

                <div>
                    <p class="gameRequirments__state__share">{{__('template.android_secure1')}}</p>
                    <p class="gameRequirments__state__details">{{__('template.android_secure2')}}</p>
                </div>
            </div>

            <div class="gameRequirments__item">
                <svg width="21" height="15" viewBox="0 0 21 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M5.15625 15C3.73438 15 2.51953 14.5078 1.51172 13.5234C0.503906 12.5391 0 11.3359 0 9.91406C0 8.69531 0.367188 7.60938 1.10156 6.65625C1.83594 5.70312 2.79688 5.09375 3.98438 4.82812C4.375 3.39062 5.15625 2.22656 6.32812 1.33594C7.5 0.445313 8.82812 0 10.3125 0C12.1406 0 13.6914 0.636719 14.9648 1.91016C16.2383 3.18359 16.875 4.73438 16.875 6.5625C17.9531 6.6875 18.8477 7.15234 19.5586 7.95703C20.2695 8.76172 20.625 9.70312 20.625 10.7812C20.625 11.9531 20.2148 12.9492 19.3945 13.7695C18.5742 14.5898 17.5781 15 16.4062 15H11.25C10.7344 15 10.293 14.8164 9.92578 14.4492C9.55859 14.082 9.375 13.6406 9.375 13.125V8.29688L7.875 9.75L6.5625 8.4375L10.3125 4.6875L14.0625 8.4375L12.75 9.75L11.25 8.29688V13.125H16.4062C17.0625 13.125 17.6172 12.8984 18.0703 12.4453C18.5234 11.9922 18.75 11.4375 18.75 10.7812C18.75 10.125 18.5234 9.57031 18.0703 9.11719C17.6172 8.66406 17.0625 8.4375 16.4062 8.4375H15V6.5625C15 5.26562 14.543 4.16016 13.6289 3.24609C12.7148 2.33203 11.6094 1.875 10.3125 1.875C9.01562 1.875 7.91016 2.33203 6.99609 3.24609C6.08203 4.16016 5.625 5.26562 5.625 6.5625H5.15625C4.25 6.5625 3.47656 6.88281 2.83594 7.52344C2.19531 8.16406 1.875 8.9375 1.875 9.84375C1.875 10.75 2.19531 11.5234 2.83594 12.1641C3.47656 12.8047 4.25 13.125 5.15625 13.125H7.5V15H5.15625Z" fill="#616161"></path>
                </svg>


                <div>
                    <p class="gameRequirments__state__data">{{__('template.android_secure3')}}</p>
                    <p class="gameRequirments__state__details">{{__('template.android_secure4')}}</p>
                </div>
            </div>

            <div class="gameRequirments__item">
                <svg width="16" height="20" viewBox="0 0 16 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1.90476 20C1.38095 20 0.93254 19.8135 0.559524 19.4405C0.186508 19.0675 0 18.619 0 18.0952V8.57143C0 8.04762 0.186508 7.59921 0.559524 7.22619C0.93254 6.85317 1.38095 6.66667 1.90476 6.66667H2.85714V4.7619C2.85714 3.44444 3.32143 2.32143 4.25 1.39286C5.17857 0.464286 6.30159 0 7.61905 0C8.93651 0 10.0595 0.464286 10.9881 1.39286C11.9167 2.32143 12.381 3.44444 12.381 4.7619V6.66667H13.3333C13.8571 6.66667 14.3056 6.85317 14.6786 7.22619C15.0516 7.59921 15.2381 8.04762 15.2381 8.57143V18.0952C15.2381 18.619 15.0516 19.0675 14.6786 19.4405C14.3056 19.8135 13.8571 20 13.3333 20H1.90476ZM1.90476 18.0952H13.3333V8.57143H1.90476V18.0952ZM7.61905 15.2381C8.14286 15.2381 8.59127 15.0516 8.96429 14.6786C9.3373 14.3056 9.52381 13.8571 9.52381 13.3333C9.52381 12.8095 9.3373 12.3611 8.96429 11.9881C8.59127 11.6151 8.14286 11.4286 7.61905 11.4286C7.09524 11.4286 6.64683 11.6151 6.27381 11.9881C5.90079 12.3611 5.71429 12.8095 5.71429 13.3333C5.71429 13.8571 5.90079 14.3056 6.27381 14.6786C6.64683 15.0516 7.09524 15.2381 7.61905 15.2381ZM4.7619 6.66667H10.4762V4.7619C10.4762 3.96825 10.1984 3.29365 9.64286 2.7381C9.0873 2.18254 8.4127 1.90476 7.61905 1.90476C6.8254 1.90476 6.15079 2.18254 5.59524 2.7381C5.03968 3.29365 4.7619 3.96825 4.7619 4.7619V6.66667Z" fill="#616161"></path>
                </svg>


                <div>
                    <p class="gameRequirments__state__security">{{__('template.android_secure5')}}</p>
                </div>
            </div>

            <div class="gameRequirments__item">
                <svg width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M2.83333 17C2.31389 17 1.86921 16.815 1.49931 16.4451C1.1294 16.0752 0.944444 15.6306 0.944444 15.1111V2.83333H0V0.944444H4.72222V0H10.3889V0.944444H15.1111V2.83333H14.1667V15.1111C14.1667 15.6306 13.9817 16.0752 13.6118 16.4451C13.2419 16.815 12.7972 17 12.2778 17H2.83333ZM12.2778 2.83333H2.83333V15.1111H12.2778V2.83333ZM4.72222 13.2222H6.61111V4.72222H4.72222V13.2222ZM8.5 13.2222H10.3889V4.72222H8.5V13.2222Z" fill="#616161"></path>
                </svg>


                <div>
                    <p class="gameRequirments__state__delete">{{__('template.android_secure6')}}</p>
                    <p class="gameRequirments__state__details">{{__('template.android_secure7')}} <u>{{__('template.android_secure8')}}</u></p>
                </div>
            </div>
            <div class="gameRequirments__state__more">
                    <span class="gameRequirments__state__moreTitle">
                        {{__('template.android_see_details')}}
                    </span>
            </div>
        </div>

    </article>

    @if ($application->display_app_bar)
        <section class="gameReviews container">
            <article class="gameInfo">
                <div class="caption">
                    <h2 class="title">{{__('template.android_top_applications')}}</h2>
                    <svg width="4" height="16" viewBox="0 0 4 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2 16C1.45 16 0.979167 15.8042 0.5875 15.4125C0.195833 15.0208 0 14.55 0 14C0 13.45 0.195833 12.9792 0.5875 12.5875C0.979167 12.1958 1.45 12 2 12C2.55 12 3.02083 12.1958 3.4125 12.5875C3.80417 12.9792 4 13.45 4 14C4 14.55 3.80417 15.0208 3.4125 15.4125C3.02083 15.8042 2.55 16 2 16ZM2 10C1.45 10 0.979167 9.80417 0.5875 9.4125C0.195833 9.02083 0 8.55 0 8C0 7.45 0.195833 6.97917 0.5875 6.5875C0.979167 6.19583 1.45 6 2 6C2.55 6 3.02083 6.19583 3.4125 6.5875C3.80417 6.97917 4 7.45 4 8C4 8.55 3.80417 9.02083 3.4125 9.4125C3.02083 9.80417 2.55 10 2 10ZM2 4C1.45 4 0.979167 3.80417 0.5875 3.4125C0.195833 3.02083 0 2.55 0 2C0 1.45 0.195833 0.979167 0.5875 0.5875C0.979167 0.195833 1.45 0 2 0C2.55 0 3.02083 0.195833 3.4125 0.5875C3.80417 0.979167 4 1.45 4 2C4 2.55 3.80417 3.02083 3.4125 3.4125C3.02083 3.80417 2.55 4 2 4Z"></path>
                    </svg>
                </div>
            </article>
            <div class="topApplicationsSlider">
                @foreach($application->topApplications as $topApplication)
                <a href="https://{{$topApplication->full_domain}}" class="topApplicationsItem">
                    <img src="{{Storage::url($topApplication->icon)}}">
                    <div class="topApplicationsItemDescription">
                        <h5 class="topApplicationsItemName">{{$topApplication->app_name}}</h5>
                        <div class="topApplicationsItemCategory">{{__('template.android_category')}}</div>
                        <div class="topApplicationsItemRating"><span>{{round((float)$topApplication->rating, 1)}}</span> <small>★</small></div>
                    </div>
                </a>
                @endforeach
            </div>
        </section>
    @endif

    <section class="gameReviews container">
        <article class="gameInfo">
            <div class="caption">
                <h2 class="title">{{__('template.android_votes')}}</h2>
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12.175 9L6.575 14.6L8 16L16 8L8 0L6.575 1.4L12.175 7H0V9H12.175Z" fill="#c5c5c5"></path>
                </svg>
            </div>

            <div>
                <p class="gameInfo__reviews paragraph">
                    {{__('template.android_votes')}}
                </p>
            </div>

        </article>

        <div class="gameReviews__grades">
            <div class="gameReviews__grade">
                <p class="gameReviews__grade_value">{{round((float)$application->rating, 1)}}</p>

                <div class="gardeReviews__stars">
                    <svg width="11" height="10" viewBox="0 0 11 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2.01316 10L2.86842 6.30263L0 3.81579L3.78947 3.48684L5.26316 0L6.73684 3.48684L10.5263 3.81579L7.6579 6.30263L8.51316 10L5.26316 8.03947L2.01316 10Z"></path>
                    </svg>

                    <svg width="11" height="10" viewBox="0 0 11 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2.01316 10L2.86842 6.30263L0 3.81579L3.78947 3.48684L5.26316 0L6.73684 3.48684L10.5263 3.81579L7.6579 6.30263L8.51316 10L5.26316 8.03947L2.01316 10Z"></path>
                    </svg>


                    <svg width="11" height="10" viewBox="0 0 11 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2.01316 10L2.86842 6.30263L0 3.81579L3.78947 3.48684L5.26316 0L6.73684 3.48684L10.5263 3.81579L7.6579 6.30263L8.51316 10L5.26316 8.03947L2.01316 10Z"></path>
                    </svg>


                    <svg width="11" height="10" viewBox="0 0 11 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2.01316 10L2.86842 6.30263L0 3.81579L3.78947 3.48684L5.26316 0L6.73684 3.48684L10.5263 3.81579L7.6579 6.30263L8.51316 10L5.26316 8.03947L2.01316 10Z"></path>
                    </svg>

                    <svg width="11" height="10" viewBox="0 0 11 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2.01316 10L2.86842 6.30263L0 3.81579L3.78947 3.48684L5.26316 0L6.73684 3.48684L10.5263 3.81579L7.6579 6.30263L8.51316 10L5.26316 8.03947L2.01316 10Z"></path>
                    </svg>

                </div>

                <p class="gradeReviews__reviewsAmount">{{rand(1111, 1199)}} {{__('template.android_reviews')}}</p>
            </div>

            <ul class="gameReviews__rating">
                <li class="gameReviews__ratingGrade">
                    <p>5</p>
                    <div class="progressBar" id="fiveStar" data-amount="90">
                        <div class="progressThumb" style="width: 90%;"></div>
                    </div>
                </li>

                <li class="gameReviews__ratingGrade">
                    <p>4</p>
                    <div class="progressBar" id="fourStar" data-amount="22">
                        <div class="progressThumb" style="width: 22%;"></div>
                    </div>
                </li>

                <li class="gameReviews__ratingGrade">
                    <p>3</p>
                    <div class="progressBar" id="threeStar" data-amount="18">
                        <div class="progressThumb" style="width: 18%;"></div>
                    </div>
                </li>

                <li class="gameReviews__ratingGrade">
                    <p>2</p>
                    <div class="progressBar" id="twoStar" data-amount="12">
                        <div class="progressThumb" style="width: 12%;"></div>
                    </div>
                </li>

                <li class="gameReviews__ratingGrade">
                    <p>1</p>
                    <div class="progressBar" id="oneStar" data-amount="3">
                        <div class="progressThumb" style="width: 3%;"></div>
                    </div>
                </li>
            </ul>
        </div>

        <ul class="gameReviews__reviewers">
            @php
                $currentDate = new DateTime();
                /**
                 * @var $reviews \App\Models\ApplicationComment[]
                 */
                $reviews = $application->applicationComments()->orderByRaw('date DESC NULLS LAST')->limit(10)->get();
            @endphp
            @foreach($reviews as $comment)

            <li class="gameReviews__reviewer" id="gameReview1">
                <div class="gameReviews__reviewerHeader">
                    <img class="gameReviews__reviewerAvatar" src="{{Storage::url($comment->icon)}}" alt="">
                    <p class="gameReviews__reviewerName">
                        <span class="gameReviews__reviewerFirstName">{{$comment->author_name}}</span>
                    </p>
                    <button type="button" class="gameReviews__moreBtn">
                        <svg width="4" height="16" viewBox="0 0 4 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M2 16C1.45 16 0.979167 15.8042 0.5875 15.4125C0.195833 15.0208 0 14.55 0 14C0 13.45 0.195833 12.9792 0.5875 12.5875C0.979167 12.1958 1.45 12 2 12C2.55 12 3.02083 12.1958 3.4125 12.5875C3.80417 12.9792 4 13.45 4 14C4 14.55 3.80417 15.0208 3.4125 15.4125C3.02083 15.8042 2.55 16 2 16ZM2 10C1.45 10 0.979167 9.80417 0.5875 9.4125C0.195833 9.02083 0 8.55 0 8C0 7.45 0.195833 6.97917 0.5875 6.5875C0.979167 6.19583 1.45 6 2 6C2.55 6 3.02083 6.19583 3.4125 6.5875C3.80417 6.97917 4 7.45 4 8C4 8.55 3.80417 9.02083 3.4125 9.4125C3.02083 9.80417 2.55 10 2 10ZM2 4C1.45 4 0.979167 3.80417 0.5875 3.4125C0.195833 3.02083 0 2.55 0 2C0 1.45 0.195833 0.979167 0.5875 0.5875C0.979167 0.195833 1.45 0 2 0C2.55 0 3.02083 0.195833 3.4125 0.5875C3.80417 0.979167 4 1.45 4 2C4 2.55 3.80417 3.02083 3.4125 3.4125C3.02083 3.80417 2.55 4 2 4Z" fill="#474747"></path>
                        </svg>
                    </button>
                </div>
                <div class="gameReviews__reviewerGrade">
                    <div class="gameReviews__reviewerStars">
                        @for($i = 0; $i < $comment->stars; $i++)
                        <svg class="checked" width="11" height="10" viewBox="0 0 11 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M2.01316 10L2.86842 6.30263L0 3.81579L3.78947 3.48684L5.26316 0L6.73684 3.48684L10.5263 3.81579L7.6579 6.30263L8.51316 10L5.26316 8.03947L2.01316 10Z"></path>
                        </svg>
                        @endfor

                        @for($i = $comment->stars; $i < 5; $i++)
                        <svg width="11" height="10" viewBox="0 0 11 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M2.01316 10L2.86842 6.30263L0 3.81579L3.78947 3.48684L5.26316 0L6.73684 3.48684L10.5263 3.81579L7.6579 6.30263L8.51316 10L5.26316 8.03947L2.01316 10Z"></path>
                        </svg>
                        @endfor

                    </div>
                    @php
                        if ($comment->date) {
                            $currentDate->setDate(...explode('-', $comment->date));
                        } else {
                            $currentDate->sub(new DateInterval('P'.rand(1,15).'D'));
                        }
                    @endphp
                    <span>{{$currentDate->format('d.m.y')}}</span>
                </div>
                <p class="gameReviews__reviewerComment">
                    {{$comment->text}}
                </p>
                <div class="gameReviews__feedback_UsersFoundHelpful">
                    <span>{{rand(55, 1199)}}</span> {{__('template.android_users_found')}}
                </div>
                <div class="gameReviews__feedback">
                    <p>{{__('template.android_answer')}}</p>
                    <div class="gameReviews__feedbackBtns">
                        <button type="button" class="gameReviews__feedbackBtn" data-feedback="positive_gameReview1">
                            <span>{{__('template.android_yes')}}</span>
                        </button>
                        <button type="button" class="gameReviews__feedbackBtn" data-feedback="negative_gameReview1">
                            <span>{{__('template.android_no')}}</span>
                        </button>
                    </div>
                </div>
                @if($comment->answer)
                    <div style="position: relative">
                        <div class="gameReviews__reviewerAnswer">
                            <div class="gameReviews__reviewerHeader">
                                {{$application->developer_name}}
                                <div class="gameReviews__reviewerDate">
                                    {{$currentDate->format('d.m.y')}}
                                </div>
                            </div>
                            <p>{{$comment->answer}}</p>
                        </div>
                    </div>
                @endif
            </li>
            @endforeach
        </ul>
        <!-- Отображение надписи "все коментарии" -->
        <div class="gameReviews__seeAllReviews">
            {{__('template.android_see_all_reviews')}}
        </div>
    </section>

    <!-- Нижняя панелька -->
    <div class="bottomMenu">
        <div class="menuItem active">
            <div class="icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="-28,-47,255,255">
                    <g fill-rule="nonzero" stroke="none" stroke-width="1" stroke-linecap="butt" stroke-linejoin="miter" stroke-miterlimit="10" font-family="none" font-weight="none" font-size="none" text-anchor="none" style="mix-blend-mode: normal">
                        <g transform="scale(10.66667,10.66667)">
                            <path d="M18.9009 6.09902C19.0648 6.90427 19.2169 7.65213 19.3 8.4L19.3946 8.95177C19.911 11.9651 20.0055 12.5165 20.1 12.8C20.1 14.4 18.8 15.6 17.3 15.6C16.5 15.6 15.8 15.3 15.3 14.8L12.4 11.9H7.7L4.8 14.7C4.3 15.2 3.6 15.5 2.8 15.5C1.2 15.5 0 14.2 0 12.7C0 12.5 0.1 11.9 0.7 8.4C0.8 7.95 0.875 7.475 0.95 7C1.025 6.525 1.1 6.05 1.2 5.6C1.3 5.1 1.3 5.1 1.3 4.7C1.36826 4.56347 1.38993 4.52014 1.3968 4.47458C1.4 4.4534 1.4 4.43174 1.4 4.4C1.8 1.8 4.1 0 6.7 0H13.3C16 0 18.2 1.8 18.5 4.4L18.6 4.7C18.7 5.09996 18.7 5.1 18.8 5.59986L18.8 5.6C18.8338 5.76904 18.8676 5.93522 18.9009 6.09902ZM7.6 6.6H9.5V5.4H7.6V3.5H6.4V5.4H4.5V6.6H6.4V8.5H7.6V6.6ZM11.8 6.6C11.5 6.9 10.9 6.9 10.6 6.6C10.3 6.3 10.3 5.7 10.6 5.4C10.9 5.1 11.5 5.1 11.8 5.4C12.1 5.7 12.1 6.3 11.8 6.6ZM12.1 7.8C12.1 8.3 12.5 8.7 13 8.7C13.5 8.7 13.9 8.3 13.9 7.9C13.9 7.4 13.5 7 13 7C12.5 7 12.1 7.3 12.1 7.8ZM13 5C12.5 5 12.1 4.6 12.1 4.1C12.1 3.6 12.5 3.2 13 3.2C13.5 3.2 13.9 3.6 13.9 4.1C13.9 4.6 13.5 5 13 5ZM14 6C14 6.5 14.4 6.9 14.9 6.9C15.4 6.9 15.8 6.5 15.8 6C15.8 5.5 15.4 5.1 14.9 5.1C14.4 5.1 14 5.5 14 6Z"></path>
                        </g>
                    </g>
                </svg>
            </div>
            <span>{{__('template.android_games')}}</span>
        </div>

        <div class="menuItem">
            <div class="icon">
                <svg xmlns="http://www.w3.org/2000/svg" height="25" width="25" viewBox="0 0 24 24">
                    <path class="heroicon-ui" d="M5 3h4a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5c0-1.1.9-2 2-2zm0 2v4h4V5H5zm10-2h4a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2h-4a2 2 0 0 1-2-2V5c0-1.1.9-2 2-2zm0 2v4h4V5h-4zM5 13h4a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4c0-1.1.9-2 2-2zm0 2v4h4v-4H5zm10-2h4a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2h-4a2 2 0 0 1-2-2v-4c0-1.1.9-2 2-2zm0 2v4h4v-4h-4z"/>
                </svg>
            </div>
            <span>{{__('template.android_apps')}}</span>
        </div>

        <div class="menuItem">
            <div class="icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="-15,0,255,255">
                    <g fill-rule="nonzero" stroke="none" stroke-width="1" stroke-linecap="butt" stroke-linejoin="miter" stroke-miterlimit="10" stroke-dasharray="" stroke-dashoffset="0" font-family="none" font-weight="none" font-size="none" text-anchor="none" style="mix-blend-mode: normal"><g transform="scale(10.66667,10.66667)"><path d="M9,2c-3.85415,0 -7,3.14585 -7,7c0,3.85415 3.14585,7 7,7c1.748,0 3.34501,-0.65198 4.57422,-1.71875l0.42578,0.42578v1.29297l6,6l2,-2l-6,-6h-1.29297l-0.42578,-0.42578c1.06677,-1.22921 1.71875,-2.82622 1.71875,-4.57422c0,-3.85415 -3.14585,-7 -7,-7zM9,4c2.77327,0 5,2.22673 5,5c0,2.77327 -2.22673,5 -5,5c-2.77327,0 -5,-2.22673 -5,-5c0,-2.77327 2.22673,-5 5,-5z"></path></g></g>
                </svg>
            </div>
            <span>{{__('template.android_search')}}</span>
        </div>

        <div class="menuItem">
            <div class="icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0,0,255,255">
                    <g fill-rule="nonzero" stroke="none" stroke-width="1" stroke-linecap="butt" stroke-linejoin="miter" stroke-miterlimit="10" font-family="none" font-weight="none" font-size="none" text-anchor="none" style="mix-blend-mode: normal">
                        <g transform="scale(10.66667,10.66667)">
                            <path d="M12.4996 6.36584L14.001 7.65237V4H11.001V7.65075L12.4996 6.36584ZM10 2H11.001H14.001H15H16.998C18.6461 2 20.001 3.35397 20.001 5.002V18.998C20.001 20.646 18.6461 22 16.998 22H4V2H10ZM18.001 5.002C18.001 4.459 17.542 4 16.998 4H16.001V12L12.5 9L9.001 12V4H6V20H16.998C17.542 20 18.001 19.541 18.001 18.998V5.002Z"></path>
                        </g>
                    </g>
                </svg>
            </div>
            <span>{{__('template.android_books')}}</span>
        </div>
    </div>
</div>
<a style="display:none" href="#" id="r">test</a>
<script type="text/javascript" src="/js/plugins/slick.min.js"></script>
<script src="/js/templates/new.js?v={{date('m')}}"></script>
<script src="/js/templates/ua-parser.min.js"></script>
<script src="/js/templates/client.js?v={{date('m')}}"></script>
</body>
</html>
