<?php

namespace App\Services\Site\Pwa;

use App\Enums\Application\LandingType;
use App\Enums\Application\PlatformType;
use App\Enums\Application\Status;
use App\Enums\PwaEvents\Event;
use App\Enums\Site\Landings;
use App\Events\PwaClientClickCreated;
use App\Events\PwaClientEventCreated;
use App\Models\Application;
use App\Models\PwaClient;
use App\Models\PwaClientClick;
use App\Models\PwaClientEvent;
use App\Services\Client\PWAEvent\PWAEventService;
use App\Services\Site\MobileDetect;
use App\Services\Site\Pwa\DTO\AddEventDTO;
use App\Services\Site\SiteRequestService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PWAService
{
    protected SiteRequestService $siteService;
    protected MobileDetect $mobileDetect;

    public function __construct(SiteRequestService $siteService, MobileDetect $mobileDetect)
    {
        $this->siteService = $siteService;
        $this->mobileDetect = $mobileDetect;
    }

    public function resolveClick(Application $application, PwaClient $pwaClient): ?PwaClientClick
    {
        if ($application->status !== Status::Active->value) {
            return null;
        }

        $params = $this->siteService->request->all();

        $externalId = Str::uuid()->toString();

        $pwaClientClick = PwaClientClick::query()->create([
            'external_id' => $externalId,
            'pwa_client_id' => $pwaClient->id,
            'ip' => $this->siteService->ipReal,
            'useragent' => $this->siteService->userAgent,
            'sub_1' => $params['sub_id_1'] ?? null,
            'sub_2' => $params['sub_id_2'] ?? null,
            'sub_3' => $params['sub_id_3'] ?? null,
            'sub_4' => $params['sub_id_4'] ?? null,
            'sub_5' => $params['sub_id_5'] ?? null,
            'sub_6' => $params['sub_id_6'] ?? null,
            'sub_7' => $params['sub_id_7'] ?? null,
            'sub_8' => $params['sub_id_8'] ?? null,
            'fb_p' => $this->siteService->request->cookie('_fbp') ?? $this->siteService->request->get('_fbp'),
            'fb_c' => $this->siteService->request->cookie('_fbc') ?? $this->siteService->request->get('_fbc'),
            'fb_click_id' => $this->siteService->request->get('fbclid'),
            'pixel_id' => $application->pixel_id,
            'pixel_key' => $application->pixel_key,
            'link' => $this->resolveLink($application->link, $externalId, $this->siteService->request),
            'request_url' => $this->siteService->request->fullUrl(),
            'country' => $this->siteService->getCountry(),
        ]);

        event(new PwaClientClickCreated($pwaClientClick));

        $this->addEvent(AddEventDTO::fromArray([
            'external_id' => $externalId,
            'status' => Event::Click->value,
            'full_domain' => $application->full_domain,
            'geo' => array_column($application->applicationGeoLanguages, 'geo'),
        ]));
        $this->setClientClickExternalIdCookie($pwaClientClick);

        return $pwaClientClick;
    }

    protected function resolveLink(string $link, string $clickExternalId, Request $request): array|string
    {
        $params = $request->query();
        $link = str_replace("{sub_id_1}", $params['sub_id_1'] ?? '', $link);
        $link = str_replace("{sub_id_2}", $params['sub_id_2'] ?? '', $link);
        $link = str_replace("{sub_id_3}", $params['sub_id_3'] ?? '', $link);
        $link = str_replace("{sub_id_4}", $params['sub_id_4'] ?? '', $link);
        $link = str_replace("{sub_id_5}", $params['sub_id_5'] ?? '', $link);
        $link = str_replace("{sub_id_6}", $params['sub_id_6'] ?? '', $link);
        $link = str_replace("{sub_id_7}", $params['sub_id_7'] ?? '', $link);
        $link = str_replace("{sub_id_8}", $params['sub_id_8'] ?? '', $link);

        return str_replace("{external_id}", $clickExternalId, $link);
    }

    public function resolveLanding(Application $application): string
    {
        $landing = $application->landing_type === LandingType::Old->value ? Landings::AndroidOld->value : Landings::AndroidNew->value;
        if (!$this->siteService->isPreview) {
            if ($application->status !== Status::Active->value) {
                return Landings::WhiteDefault->value;
            }
            switch ($application->platform_type) {
                case PlatformType::Android->value:
                    if (!$this->mobileDetect->isAndroidOS()) {
                        return Landings::WhiteDefault->value;
                    }
                    break;
                case PlatformType::IOS->value:
                    if ($this->mobileDetect->isiOS()) {
                        $landing = Landings::Ios->value;
                    } else {
                        return Landings::WhiteDefault->value;
                    }
                    break;
                case PlatformType::Multi->value:
                    if ($this->mobileDetect->isiOS()) {
                        $landing = Landings::Ios->value;
                    }
                    break;
                default:
                    break;
            }
        } else {
            if ($application->platform_type === PlatformType::IOS->value) {
                $landing = Landings::Ios->value;
            }
        }
        return $landing;
    }

    public function resolveClient(Request $request, Application $application): ?PwaClient
    {
        if ($application->status !== Status::Active->value) {
            return null;
        }
        $externalId = $this->getClientExternalId($request, $application);
        $client = $externalId ? $this->getClient($externalId) : null;

        if (!$client || $client->application_id != $application->id) {
            $client = $this->createClient($application);
            $this->setClientExternalIdCookie($client);
        }

        return $client;
    }

    public function getClientExternalId(Request $request, Application $application): ?string
    {
        return $request->cookie("application_{$application->id}_client_id");
    }

    protected function setClientExternalIdCookie(PwaClient $pwaClient): void
    {
        Cookie::queue(Cookie::make("application_{$pwaClient->application_id}_client_id", $pwaClient->external_id, 365 * 24 * 60));
    }

    protected function setClientClickExternalIdCookie(PwaClientClick $pwaClientClick): void
    {
        Cookie::queue(Cookie::make("application_{$pwaClientClick->pwaClient->application_id}_click_id", $pwaClientClick->external_id, 365 * 24 * 60));
    }


    public function getClientClickExternalId(Request $request, Application $application): ?string
    {
        return $request->cookie("application_{$application->id}_click_id");
    }

    public function setFbCredentials(Request $request, ?PwaClientClick $pwaClientClick, string $place): void
    {
        if (!$pwaClientClick) {
            return;
        }

        $fbp = $request->cookie('_fbp') ?? $request->get('_fbp');
        $fbc = $request->cookie('_fbc') ?? $request->get('_fbc');

        Log::debug('setFbCredentials', [
            'place' => $place,
            'pwaClientClick' => $pwaClientClick,
            'after' => [
                'fb_p' => $fbp,
                'fb_c' => $fbc,
            ],
            'before' => [
                'fb_p' => $pwaClientClick->fb_p,
                'fb_c' => $pwaClientClick->fb_c,
            ]
        ]);

        if (!$pwaClientClick->fb_c || !$pwaClientClick->fb_p) {
            $pwaClientClick->update([
                'fb_p' => $fbp,
                'fb_c' => $fbc,
            ]);
        }
    }

    public function getClick(string $externalId): ?PwaClient
    {
        return PwaClientClick::query()->where(['external_id' => $externalId])->first();
    }

    public function getClient(string $externalId): ?PwaClient
    {
        return PwaClient::query()->where(['external_id' => $externalId])->first();
    }

    protected function createClient(Application $application): PwaClient
    {
        return PwaClient::query()->create([
            'application_id' => $application->id,
        ]);
    }

    public function createManifest(Application $application): array
    {
        $resolutions = [
            '48x48',
            '180x180',
            '96x96',
            '128x128',
            '192x192',
            '384x384',
            '512x512',
            '1024x1024',
        ];
        $icons = [];

        foreach ($resolutions as $resolution) {
            $icons[] = [
                'src' => $this->siteService->getHostUrl(Storage::url($application->icon)),
                'sizes' => $resolution,
                'type' => 'image/png',
                'purpose' => 'maskable any',
            ];
        }

        return [
            'id' => $application->uuid,
            'dir' => 'ltr',
            'name' => $application->app_name,
            'scope' => $this->mobileDetect->isMobile() ? '/go' : '/',
            'display' => 'standalone',
            'start_url' => '/go?com=' . $application->uuid,
            'short_name' => $application->app_name,
            'theme_color' => '#ffffff',
            'description' => $application->description ?? '',
            'orientation' => 'any',
            'background_color' => '#ffffff',
            'prefer_related_applications' => true,
            'icons' => $icons,
            'url' => $this->siteService->getHostUrl('/go?com=' . $application->uuid),
            'lang' => 'ru',
            'related_applications' => [
                [
                    'platform' => 'webapp',
                    'url' => $this->siteService->getHostUrl('/manifest?app_uuid=' . $application->uuid),
                    'id' => $application->uuid,
                ],
            ],
            'screenshots' => [],
            'generated' => '',
            'manifest_package' => $application->uuid,
            'scope_url' => $this->mobileDetect->isMobile() ? $this->siteService->getHostUrl('/go') : $this->siteService->getHostUrl('/'),
            'intent_filters' => [
                'scope_url_scheme' => 'https',
                'scope_url_host' => $this->siteService->host,
                'scope_url_path' =>  $this->mobileDetect->isMobile() ? '/go' : '/',
            ],
            'display_mode' => 'standalone',
            'web_manifest_url' => $this->siteService->getHostUrl('/manifest?app_uuid=' . $application->uuid),
            'version_code' => '1',
            'version_name' => '1.0',
            'bound_webapk' => [
                'runtime_host' => 'org.chromium.chrome',
                'runtime_host_application_name' => 'Chromium'
            ],
        ];
    }

    public function addEvent(AddEventDTO $addEventDto): null|PwaClientEvent
    {
        $pwaClick = PwaClientClick::query()->where('external_id', $addEventDto->clickExternalId)->first();

        if ($pwaClick && $addEventDto->event) {
            return $this->createEvent($pwaClick, $addEventDto);
        }

        return null;
    }

    private function createEvent(PwaClientClick $pwaClick, AddEventDTO $addEventDto): PwaClientEvent
    {
        $exists = PwaClientEvent::query()->scopes(['clientClick'])
            ->where('event', $addEventDto->event->value)
            ->where('pwa_client_clicks.pwa_client_id', $pwaClick->pwa_client_id)
            ->exists();
        $pwaClickEvent = $pwaClick->event()->create([
            'event' => $addEventDto->event->value,
            'details' => $addEventDto->details,
            'is_first' => !$exists,
            'full_domain' => $addEventDto->fullDomain,
            'geo' => $addEventDto->geo,
            'platform' => $addEventDto->platform->value,
        ]);
        event(new PwaClientEventCreated($pwaClickEvent));

        return $pwaClickEvent;
    }
}
