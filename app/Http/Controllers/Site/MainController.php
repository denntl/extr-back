<?php

namespace App\Http\Controllers\Site;

use App\Enums\Application\Language;
use App\Enums\PwaEvents\Event;
use App\Enums\Site\Landings;
use App\Http\Controllers\Controller;
use App\Http\Requests\Site\AnalyticRequest;
use App\Http\Requests\Site\GoRequest;
use App\Http\Requests\Site\ManifestRequest;
use App\Http\Requests\Site\PostbackRequest;
use App\Http\Requests\Site\PreviewRequest;
use App\Models\Application;
use App\Services\Site\ApplicationService;
use App\Services\Site\Pwa\DTO\AddEventDTO;
use App\Services\Site\Pwa\PWAService;
use App\Services\Site\SiteRequestService;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;

class MainController extends Controller
{
    public function index(
        SiteRequestService $siteService,
        ApplicationService $applicationService,
        PWAService $PWAService,
    ): Application|Factory|View|RedirectResponse {
        $application = $applicationService->getApplication($siteService->host, $siteService->ipCountry);

        if (!$application) {
            $whitePage = $applicationService->getWhitePage($siteService->host);
            $locale = strtolower($whitePage?->language);
            Lang::hasForLocale('whitepage', $locale) ? App::setLocale($locale) : App::setLocale('en');
            return view('site.landings.white-default');
        }
        App::setLocale(strtolower($application->getLanguage($siteService->ipCountry) ?: 'en'));

        $tag = microtime(true);
        try {
            $client = $PWAService->resolveClient($siteService->request, $application);
            logger()->debug("MainController@index - client - $tag", ['client' => $client ?? null]);
            $landing = $PWAService->resolveLanding($application);
            logger()->debug("MainController@index - landing - $tag", ['landing' => $landing]);
            $pwaClientClick = $PWAService->resolveClick($application, $client);
            logger()->debug("MainController@index - click - $tag", ['pwaClientClick' => $pwaClientClick ?? null]);
        } catch (\Throwable $throwable) {
            logger()->error("MainController@index - error - $tag", ['error' => $throwable->getMessage(), 'trace' => $throwable->getTrace()]);
            return view('site.landings.' . Landings::WhiteDefault->value, [
                'application' => $application,
                'isPreview' => $siteService->isPreview,
                'externalId' => 'exception',
                'link' => app('url')->current(),
            ]);
        }

        return view("site.landings.$landing", [
            'application' => $application,
            'isPreview' => $siteService->isPreview,
            'externalId' => $pwaClientClick->external_id,
            'link' => $pwaClientClick->link,
        ]);
    }

    public function preview(
        $appUuid,
        SiteRequestService $siteService,
        ApplicationService $applicationService,
        PWAService $PWAService
    ): View {
        $application = $applicationService->getApplicationByUuid($appUuid);

        if (!$application) {
            $whitePage = $applicationService->getWhitePage($siteService->host);
            $locale = strtolower($whitePage?->language);
            Lang::hasForLocale('whitepage', $locale) ? App::setLocale($locale) : App::setLocale('en');
            return view('site.landings.white-default');
        }
        $language = $application->applicationGeoLanguages()->first();
        App::setLocale(strtolower($language ? $language->language : 'en'));

        $landing = $PWAService->resolveLanding($application);

        return view("site.landings.$landing", [
            'application' => $application,
            'isPreview' => $siteService->isPreview,
            'externalId' => Str::uuid(),
            'link' => route('preview', ['appUuid' => $appUuid]),
        ]);
    }

    public function previewPost(
        PreviewRequest $previewRequest,
        SiteRequestService $siteService,
        ApplicationService $applicationService,
        PWAService $PWAService
    ): View {
        $application = $applicationService->fillApplicationForPreview($previewRequest->validated());
        App::setLocale(strtolower($application->language));

        $landing = $PWAService->resolveLanding($application);

        return view("site.landings.$landing", [
            'application' => $application,
            'isPreview' => $siteService->isPreview,
            'externalId' => Str::uuid(),
            'link' => route('preview', ['appUuid' => $application->uuid]),
        ]);
    }

    public function postback(PostbackRequest $request, PWAService $PWAService): JsonResponse
    {
        $PWAService->addEvent(AddEventDTO::fromArray($request->validated()));

        return response()->json(['status' => 'ok']);
    }

    public function acc()
    {
        //TODO onesignal integration
    }

    public function manifest(
        ManifestRequest $request,
        ApplicationService $applicationService,
        PWAService $PWAService
    ): Application|Response|JsonResponse|ResponseFactory {
        $application = $applicationService->getApplicationByUuid($request->validated('app_uuid'));
        return response()->json($PWAService->createManifest($application));
    }

    public function analytic(
        AnalyticRequest $request,
        PWAService $PWAService,
        SiteRequestService $requestService,
        ApplicationService $applicationService
    ): JsonResponse {
        $application = $applicationService->getApplicationByUuid($request->validated('com'));
        $externalId = $request->validated('externalId');
        if (!$externalId) {
            $externalId = $PWAService->getClientClickExternalId($request, $application);
        }
        $pwaClientEvent = $PWAService->addEvent(AddEventDTO::fromArray([
            'external_id' => $externalId,
            'status' => $request->validated('t'),
        ]));

        $PWAService->setFbCredentials($request, $pwaClientEvent?->pwaClientClick, __FUNCTION__);

        return response()->json([
            'redirect' => $requestService->getHostUrl('/go?com=' . $request->validated('com') . '&externalId=' . $externalId),
            'setting' => [
                'installing' => [
                    'ranges' => [
                        'step' => [
                            'min' => 10,
                            'max' => 15,
                        ],
                        'interval' => [
                            'min' => 1000,
                            'max' => 1500,
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function go(
        GoRequest $request,
        PWAService $PWAService,
        ApplicationService $applicationService,
        SiteRequestService $requestService
    ): Factory|View|Application|RedirectResponse {
        $application = $applicationService->getApplicationByUuid($request->validated('com'));
        $externalId = $request->validated('externalId');
        if (!$externalId) {
            $externalId = $PWAService->getClientClickExternalId($request, $application);
            if (!$externalId) {
                return view('site.landings.white-default');
            }
        }
        if (!$request->validated('onesignal')) {
            return view('site.onesignal', [
                'application' => $application,
                'link' => $requestService->getHostUrl('/go?com=' . $request->validated('com') . '&externalId=' . $externalId . '&onesignal=ok'),
                'externalId' => $externalId,
                'clientId' => $PWAService->getClientExternalId($request, $application),
                'webhookUrl' => route('onesignal.webhooks'),
            ]);
        }
        $event = $PWAService->addEvent(AddEventDTO::fromArray([
            'external_id' => $externalId,
            'status' => Event::Open->value,
        ]));

        $click = $event?->pwaClientClick;

        $PWAService->setFbCredentials($request, $click, __FUNCTION__);

        return $click ? response()->redirectTo($click->link) : view('site.landings.white-default');
    }
}
