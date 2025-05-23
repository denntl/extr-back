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
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1">
    <meta name="theme-color" content="#FFFFFF">
    <meta name="google" value="notranslate">
    <meta name="Description" content="{{$application->developer_name}}">
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

    <link rel="stylesheet" type="text/css" href="/css/templates/old.css?v={{date('m')}}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Material+Icons&display=swap">
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
<div id="_js" style="display:none">
    <div id="__layout">
        <div id="content">
            <div id="main-container" class="is-not-pwa">
                <main>
                    <div class="overlay_loading" style="position:fixed;width:100%;height:100%;background:none center center/50% no-repeat transparent;z-index:102;display:none">
                        <div class="progress_line_" style="position:fixed;width:95%;height:3px;top:0;left:0;background:#4285f4">
                            <div class="runner_window" style="width:15px;height:3px;background:rgba(255,255,255,.62);margin-left:20px"></div>
                        </div>
                    </div>
                    <section class="container">
                        <div id="main-frame-container"></div>
                        <div style="">
                            <section>
                                <div md-mode="indeterminate" class="md-progress-bar md-indeterminate md-theme-default" style="display:none" data-v-6057b18c="">
                                    <div class="md-progress-bar-track" data-v-6057b18c=""></div>
                                    <div class="md-progress-bar-fill" data-v-6057b18c=""></div>
                                    <div class="md-progress-bar-buffer" data-v-6057b18c=""></div>
                                </div>
                                <div class="app-comp">
                                    <div class="app-comp__left">
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
                                                    <div class="app-comp__mobile-info-item">
                                                        <span
                                                        class="app-comp__mobile-info-rating"><span> {{$application->rating}}</span>★</span><span
                                                        class="app-comp__mobile-info-subtitle">
                                                        {{__('template.android_install')}}
                                                        </span>
                                                    </div>
                                                    <div class="app-comp__mobile-info-item">
                                                         <span class="app-comp__mobile-info-icon">
                                                                <svg viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg" width="20">
                                                                    <g transform="translate(21.552 22.5) rotate(180)">
                                                                        <path transform="translate(7.552 7.652)" d="M.625,0h8.75A.68.68,0,0,1,10,.723a.68.68,0,0,1-.625.723H.625A.68.68,0,0,1,0,.723.68.68,0,0,1,.625,0Z" data-name="Path 288"></path>
                                                                        <g transform="translate(17.552 20.797) rotate(180)">
                                                                            <path d="M0,0H9.666V9.666H0Z" fill="none" data-name="Path 289"></path>
                                                                            <path transform="translate(-4.408 -3.203)" d="M8.752,4.642V11.81L5.536,8.678a.677.677,0,0,0-.936,0,.627.627,0,0,0,0,.9l4.343,4.229a.669.669,0,0,0,.929,0l4.343-4.229a.627.627,0,0,0,0-.9.669.669,0,0,0-.929,0L10.07,11.81V4.642a.659.659,0,0,0-1.318,0Z" data-name="Path 290"></path>
                                                                        </g>
                                                                        <rect transform="translate(4.552 5.5)" width="16" height="16" rx="2" fill="none" stroke="#000" stroke-width="2" data-name="Rectangle 123"></rect>
                                                                    </g>
                                                                </svg>
                                                            </span>
                                                        <span class="app-comp__mobile-info-subtitle">8.1 MB</span>
                                                    </div>
                                                    <div class="app-comp__mobile-info-item">
                                                            <span class="app-comp__mobile-info-icon">
                                                                <div class="app-comp__mobile-info-rate">18+</div>
                                                            </span>
                                                            <span class="app-comp__mobile-info-subtitle"> {{__('template.android_years')}} </span>
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
                                        <div class="app-comp__info-wrapper">
                                            <div class="app-comp__logo"><img alt="App icon" src="{{Storage::url($application->icon)}}" width="180" height="180"></div>
                                            <div class="app-comp__main-info">
                                                <div class="app-comp__info">
                                                    <div class="app-comp__info-left">
                                                        <div class="app-comp__info-title"> {{$application->app_name}} <img class="vf-icon" alt="" src="/img/old/galka.png" /></div>
                                                        <div class="app-comp__info-subtitle"><span>{{$application->developer_name}}</span></div>
                                                        <div style="display:flex;margin-top:10px">
                                                            <div class="app-comp__age-rate"></div>
{{--                                                            <div class="app-comp__ad-status"> {{__('template.android_ads_free')}} &#8226; <span>{{__('template.android_category')}}</span></div>--}}
                                                            <div class="app-comp__ad-status"> {{__('template.android_ads_free')}} </span></div>
                                                        </div>
                                                    </div>
                                                    <div class="app-comp__right-wrapper">
                                                        <div class="app-comp__choise"><img src="/img/old/choise.png" alt=""><span helpers-decode>{{__('template.android_selected')}}</span></div>
                                                        <div class="app-comp__users-rate">
                                                            <div class="star" data-v-2b76e870=""><span data-v-2b76e870=""><img src="/img/old/star-full.png" alt="" data-v-2b76e870=""></span></div>
                                                            <div class="star" data-v-2b76e870=""><span data-v-2b76e870=""><img src="/img/old/star-full.png" alt="" data-v-2b76e870=""></span></div>
                                                            <div class="star" data-v-2b76e870=""><span data-v-2b76e870=""><img src="/img/old/star-full.png" alt="" data-v-2b76e870=""></span></div>
                                                            <div class="star" data-v-2b76e870=""><span data-v-2b76e870=""><img src="/img/old/star-full.png" alt="" data-v-2b76e870=""></span></div>
                                                            <div class="star" data-v-2b76e870=""><span data-v-2b76e870=""><img src="/img/old/star-full.png" alt="" data-v-2b76e870=""></span></div>
                                                            <div class="app-comp__user-num"> 1224 </div>
                                                            <img src="/img/old/user.png" alt="">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="app-comp__mobile-info">
                                                    <div class="app-comp__mobile-info-item">
                                                            <span class="app-comp__mobile-info-rating">
                                                                <span>{{round((float)$application->rating, 1)}}</span>
                                                                <svg enable-background="new 0 0 24 24" version="1.1" viewBox="0 0 24 24" xml:space="preserve" xmlns="http://www.w3.org/2000/svg">
                                                                    <g fill="none">
                                                                        <path d="M0,0h24v24H0V0z"></path>
                                                                        <path d="M0,0h24v24H0V0z"></path>
                                                                    </g>
                                                                    <path d="M12,17.27L18.18,21l-1.64-7.03L22,9.24l-7.19-0.61L12,2L9.19,8.63L2,9.24l5.46,4.73L5.82,21L12,17.27z"></path>
                                                                </svg>
                                                            </span>
                                                        <span class="app-comp__mobile-info-subtitle">{{__('template.android_stars')}}</span>
                                                    </div>
                                                    <div class="app-comp__mobile-info-item">
                                                            <span class="app-comp__mobile-info-rating">
                                                                <div class="">{{$application->downloads_count ?: '10 000'}}</div>
                                                            </span>
                                                        <span class="app-comp__mobile-info-subtitle">{{__('template.android_downloads')}}</span>
                                                    </div>
                                                    <div class="app-comp__mobile-info-item">
                                                            <span class="app-comp__mobile-info-icon">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                                                                <rect fill="none" width="20" height="20"></rect>
                                                                <path d="M10.54,11.09L8.66,9.22l-1.02,1.02l2.9,2.9l5.8-5.8l-1.02-1.01L10.54,11.09z M15.8,16.24H8.2L4.41,9.66L8.2,3h7.6l3.79,6.66 L15.8,16.24z M17,1H7L2,9.66l5,8.64V23l5-2l5,2v-4.69l5-8.64L17,1z"></path>
                                                            </svg>
                                                            </span>
                                                        <span class="app-comp__mobile-info-subtitle">{{__('template.android_editors_choice')}}</span>
                                                    </div>
                                                    <div class="app-comp__mobile-info-item">
                                                            <span class="app-comp__mobile-info-icon">
                                                                <svg viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg" width="20">
                                                                    <g transform="translate(21.552 22.5) rotate(180)">
                                                                        <path transform="translate(7.552 7.652)" d="M.625,0h8.75A.68.68,0,0,1,10,.723a.68.68,0,0,1-.625.723H.625A.68.68,0,0,1,0,.723.68.68,0,0,1,.625,0Z" data-name="Path 288"></path>
                                                                        <g transform="translate(17.552 20.797) rotate(180)">
                                                                            <path d="M0,0H9.666V9.666H0Z" fill="none" data-name="Path 289"></path>
                                                                            <path transform="translate(-4.408 -3.203)" d="M8.752,4.642V11.81L5.536,8.678a.677.677,0,0,0-.936,0,.627.627,0,0,0,0,.9l4.343,4.229a.669.669,0,0,0,.929,0l4.343-4.229a.627.627,0,0,0,0-.9.669.669,0,0,0-.929,0L10.07,11.81V4.642a.659.659,0,0,0-1.318,0Z" data-name="Path 290"></path>
                                                                        </g>
                                                                        <rect transform="translate(4.552 5.5)" width="16" height="16" rx="2" fill="none" stroke="#000" stroke-width="2" data-name="Rectangle 123"></rect>
                                                                    </g>
                                                                </svg>
                                                            </span>
                                                        <span class="app-comp__mobile-info-subtitle">8.1 MB</span>
                                                    </div>
                                                    <div class="app-comp__mobile-info-item">
                                                            <span class="app-comp__mobile-info-icon">
                                                                <div class="app-comp__mobile-info-rate">18+</div>
                                                            </span>
                                                        <span class="app-comp__mobile-info-subtitle">{{__('template.android_years')}}</span>
                                                    </div>
                                                </div>
                                                <div class="app-comp__install-wrapper">
                                                    <div class="app-comp__wish-list-add">
                                                        <div class="app-comp__wish-list-img"></div>
                                                        <button class="app-comp__wish-list-button"><span>{{__('template.android_favorite')}}</span></button>
                                                    </div>
                                                    <button type="button" id="install-button" data-install="{{__('template.android_install')}}" data-installing="{{__('template.android_installing')}}" data-download="{{__('template.android_download')}}" data-open="{{__('template.android_open')}}" class="app-comp__install-button active greenBtn" data-size="8.1 MB" style="background-color:#00875f">{{__('template.android_install')}}</button>
                                                    <div class="loading">
                                                        <div class="progress_container">
                                                            <div class="progress_word">{{__('template.android_download')}}...</div>
                                                            <div class="progress_graph">
                                                                <div class="runner"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="gallery">
                                            @foreach($application->files as $file)
                                                <div class="gall_img"><img src="{{Storage::url($file->path)}}" alt=""></div>
                                            @endforeach
                                        </div>
                                        <div class="app-description" data-v-4c38fcdc="">
                                            <div class="app-description__title" data-v-4c38fcdc=""> {{__('template.android_about')}} </div>
                                            <div class="app-description__content" data-v-4c38fcdc="">
                                                <div class="app-description__expand expand size collapsed" id="text" data-v-4c38fcdc>
                                                    <p class="app-description__main-content" data-v-4c38fcdc="" style="white-space: pre-wrap;"> {{$application->description}} </p>
                                                </div>
                                                <div class="shadow"></div>
                                            </div>
                                            <button type="button" data-hide="{{__('template.android_hide')}}" data-show="{{__('template.android_show')}}" class="expand-btn app-comp__install-button active greenBtn colapsed" id="expand-button"></button>
                                        </div>
                                        <div class="line"></div>
                                        <div class="app-comp__recent-changes app-comp__main-recent-changes">
                                            <h3 class="app-comp__section-header app-comp__section-header_changes" helpers-decode> {{__('template.android_updates')}} </h3>
                                            <div class="app-comp__recent-changes-wrapper"> {{__('template.android_updates_more')}} </div>
                                        </div>
                                        <div class="line"></div>
                                        @if ($application->display_app_bar)
                                            <div class="app-comp__charts topApplications">
                                                <article class="gameInfo">
                                                    <div class="caption">
                                                        <h2 class="app-comp__section-header">{{__('template.android_top_applications')}}</h2>
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
{{--                                                                <div class="topApplicationsItemCategory">{{__('template.android_category')}}</div>--}}
                                                                <div class="topApplicationsItemRating"><span>{{round((float)$topApplication->rating, 1)}}</span> <small>★</small></div>
                                                            </div>
                                                        </a>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                        <div class="app-comp__charts">
                                            <h3 class="app-comp__section-header">{{__('template.android_votes')}}</h3>
                                            <div class="app-comp__charts-wrapper">
                                                <div class="score" data-v-3bf2ee4c="">
                                                    <div class="score__number" data-v-3bf2ee4c=""> {{round((float)$application->rating, 1)}} </div>
                                                    <div class="score__stars" data-v-3bf2ee4c="">
                                                        <div class="star" data-v-2b76e870="" data-v-3bf2ee4c=""><span data-v-2b76e870=""><img src="/img/old/star-full-big.png" alt="" data-v-2b76e870=""></span></div>
                                                        <div class="star" data-v-2b76e870="" data-v-3bf2ee4c=""><span data-v-2b76e870=""><img src="/img/old/star-full-big.png" alt="" data-v-2b76e870=""></span></div>
                                                        <div class="star" data-v-2b76e870="" data-v-3bf2ee4c=""><span data-v-2b76e870=""><img src="/img/old/star-full-big.png" alt="" data-v-2b76e870=""></span></div>
                                                        <div class="star" data-v-2b76e870="" data-v-3bf2ee4c=""><span data-v-2b76e870=""><img src="/img/old/star-full-big.png" alt="" data-v-2b76e870=""></span></div>
                                                        <div class="star" data-v-2b76e870="" data-v-3bf2ee4c=""><span data-v-2b76e870=""><img src="/img/old/star-full-big.png" alt="" data-v-2b76e870=""></span></div>
                                                    </div>
                                                    <div class="score__all-users" data-v-3bf2ee4c=""> {{__('template.android_reviews')}} <img src="/img/old/user.png" alt="" data-v-3bf2ee4c=""></div>
                                                    <div class="score__ratings" data-v-3bf2ee4c=""> {{rand(1111, 1199)}} </div>
                                                </div>
                                                <div class="chart" data-v-623abd02="">
                                                    <ol class="chart__bars" data-v-623abd02="">
                                                        <li class="chart__bar-container" data-v-623abd02="">
                                                            <span data-v-623abd02="">5</span>
                                                            <div class="chart__bar" style="width:100%" data-v-623abd02=""></div>
                                                            <div class="chart__bg" data-v-623abd02=""></div>
                                                        </li>
                                                        <li class="chart__bar-container" data-v-623abd02="">
                                                            <span data-v-623abd02="">4</span>
                                                            <div class="chart__bar" style="width:22.74%" data-v-623abd02=""></div>
                                                            <div class="chart__bg" data-v-623abd02=""></div>
                                                        </li>
                                                        <li class="chart__bar-container" data-v-623abd02="">
                                                            <span data-v-623abd02="">3</span>
                                                            <div class="chart__bar" style="width:0.51%" data-v-623abd02=""></div>
                                                            <div class="chart__bg" data-v-623abd02=""></div>
                                                        </li>
                                                        <li class="chart__bar-container" data-v-623abd02="">
                                                            <span data-v-623abd02="">2</span>
                                                            <div class="chart__bar" style="width:0.01%" data-v-623abd02=""></div>
                                                            <div class="chart__bg" data-v-623abd02=""></div>
                                                        </li>
                                                        <li class="chart__bar-container" data-v-623abd02="">
                                                            <span data-v-623abd02="">1</span>
                                                            <div class="chart__bar" style="width:0%" data-v-623abd02=""></div>
                                                            <div class="chart__bg" data-v-623abd02=""></div>
                                                        </li>
                                                    </ol>
                                                </div>
                                            </div>
                                        </div>
                                        @php
                                            $currentDate = new DateTime();
                                            /**
                                             * @var $reviews \App\Models\ApplicationComment[]
                                             */
                                            $reviews = $application->applicationComments()->orderByRaw('date DESC NULLS LAST')->limit(10)->get();
                                        @endphp
                                        @foreach($reviews as $comment)
                                            <div data-v-265dd011="" class="testimotials">
                                                <div data-v-265dd011="" class="testimotials__comment-info">
                                                    <div data-v-265dd011="" class="testimotials__user-logo"><img data-v-265dd011="" src="{{Storage::url($comment->icon)}}" class="v-lazy-image" alt="Avatar"></div>
                                                    <div data-v-265dd011="" class="testimotials__user-info-wrapper">
                                                        <div data-v-265dd011="" class="testimotials__user-info-top">
                                                            <div data-v-265dd011="" class="testimotials__user-info-left">
                                                                <div data-v-265dd011="" class="testimotials__user-name"> {{$comment->author_name}} </div>
                                                                <div data-v-265dd011="" class="testimotials__rate-info">
                                                                        @php
                                                                        if ($comment->date) {
                                                                            $currentDate->setDate(...explode('-', $comment->date));
                                                                        } else {
                                                                            $currentDate->sub(new DateInterval('P'.rand(1,15).'D'));
                                                                        }
                                                                        @endphp
                                                                    <div data-v-265dd011="" class="testimotials__date"> {{$currentDate->format('d.m.y')}}</div>
                                                                </div>
                                                            </div>
                                                            <div data-v-265dd011="" class="testimotials__rate-stars">
                                                                @for($i = 0; $i < $comment->stars; $i++)
                                                                    <div data-v-2b76e870="" data-v-265dd011="" class="star"><span data-v-2b76e870=""><img data-v-2b76e870="" src="/img/old/star-full.png" alt=""></span></div>
                                                                @endfor
                                                            </div>
                                                            <div data-v-3442737e="" data-v-265dd011="" class="tooltip">
                                                                <div data-v-265dd011="" data-v-3442737e="" class="testimotials__likes"><i data-v-265dd011="" data-v-3442737e="" class="material-icons">thumb_up</i><span class="likes">1{{rand(11,99)}}</span></div>
                                                            </div>
                                                        </div>
                                                        <div data-v-265dd011="" class="testimotials__text-ph">
                                                            <div data-v-265dd011="" class="testimotials__text-visible"> {{$comment->text}}</div>
                                                        </div>
                                                        @if($comment->answer)
                                                            <div style="position: relative">
                                                                <div class="answer-block">
                                                                    <div data-v-265dd011="" class="testimotials__user-info-left">
                                                                        <div data-v-265dd011="" class="testimotials__user-name"> {{$application->developer_name}}<span class="answer-date">{{$currentDate->format('d.m.y')}}</span></div>
                                                                    </div>
                                                                    <div data-v-265dd011="" class="testimotials__text-ph">
                                                                        <div data-v-265dd011="" class="testimotials__text-visible"> {{$comment->answer}}</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                        <div class="line"></div>
                                        <div class="line"></div>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </section>
                </main>
                <div class="modal_window"><img class="img_close_image" src="/img/old/right-arrow.svg" alt=""> <img class="target_img" src="" alt=""></div>
            </div>
        </div>
    </div>
</div>
<a style="display:none" href="#" id="r">test</a>
<script src="/js/templates/main.js?v={{date('m')}}"></script>
<script src="/js/templates/ua-parser.min.js"></script>
<script src="/js/templates/client.js?v={{date('m')}}"></script>
</body>
</html>
