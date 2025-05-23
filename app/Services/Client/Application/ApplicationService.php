<?php

namespace App\Services\Client\Application;

use App\Enums\Application\Category;
use App\Enums\Application\Geo;
use App\Enums\Application\LandingType;
use App\Enums\Application\Language;
use App\Enums\Application\PlatformType;
use App\Enums\Application\Status;
use App\Enums\Application\WhiteType;
use App\Enums\PwaEvents\Event;
use App\Enums\PwaEvents\Platform;
use App\Events\Client\Application\ApplicationDeactivated;
use App\Models\Application;
use App\Models\ApplicationGeoLanguage;
use App\Models\TopApplication;
use App\Models\User;
use App\Services\Client\Application\DTO\PostbackDTO;
use App\Services\Client\Company\CompanyService;
use App\Services\Common\Application\ApplicationGeoLanguageService;
use App\Services\Common\ApplicationFiles\ApplicationFileService;
use App\Services\Common\OneSignal\Exceptions\PushNotificationException;
use App\Services\Common\OneSignal\OneSignalNotificationService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

readonly class ApplicationService
{
    public static function getStatusList(): array
    {
        return [
            ['value' => Status::NotActive->value, 'label' => 'Не активный'],
            ['value' => Status::Active->value, 'label' => 'Активный'],
        ];
    }

    public static function getGeoList(): array
    {
        return array_map(fn (Geo $geo) => ['value' => $geo->value, 'label' => $geo->value], Geo::cases());
    }

    public static function getPlatformList(): array
    {
        return [
            [
                'value' => PlatformType::Android,
                'label' => 'Android'
            ],
            [
                'value' => PlatformType::IOS,
                'label' => 'iOS'
            ],
            [
                'value' => PlatformType::Multi,
                'label' => 'Multi'
            ],
        ];
    }

    public static function getPWAStatisticsPlatformList(): array
    {
        return [
            [
                'value' => Platform::Android,
                'label' => 'Android'
            ],
            [
                'value' => Platform::IOS,
                'label' => 'iOS'
            ],
            [
                'value' => Platform::Unknown,
                'label' => 'Unknown'
            ],
        ];
    }

    public static function getCategoryList(): array
    {
        return [
            [
                'value' => Category::Gambling->value,
                'label' => 'Гемблинг'
            ],
            [
                'value' => Category::Dating->value,
                'label' => 'Дейтинг'
            ],
            [
                'value' => Category::Finances->value,
                'label' => 'Финансы'
            ],
            [
                'value' => Category::Nature->value,
                'label' => 'Нутра'
            ],
            [
                'value' => Category::Adult->value,
                'label' => 'Адалт'
            ],
            [
                'value' => Category::Betting->value,
                'label' => 'Беттинг'
            ],
            [
                'value' => Category::SweepStake->value,
                'label' => 'Свипстейки'
            ],
            [
                'value' => Category::Tovarka->value,
                'label' => 'Товарка'
            ],
            [
                'value' => Category::Crypto->value,
                'label' => 'Крипта'
            ],
        ];
    }

    public static function getWhiteTypeList(): array
    {
        return [
            [
                'value' => WhiteType::White->value,
                'label' => 'Белый экран',
            ],
            [
                'value' => WhiteType::System->value,
                'label' => 'Системный',
            ],
        ];
    }

    public static function getStatisticEventList(): array
    {
        return [
            [
                'value' => Event::Click->value,
                'label' => 'Клик'
            ],
            [
                'value' => Event::UniqueClick->value,
                'label' => 'Уникальный клик'
            ],
            [
                'value' => Event::Install->value,
                'label' => 'Первая установка'
            ],
            [
                'value' => Event::RepeatedInstall->value,
                'label' => 'Повторная установка'
            ],
            [
                'value' => Event::Open->value,
                'label' => 'Первое открытие'
            ],
            [
                'value' => Event::RepeatedOpen->value,
                'label' => 'Повторное открытие'
            ],
            [
                'value' => Event::Subscribe->value,
                'label' => 'PUSH подписка'
            ],
            [
                'value' => Event::Registration->value,
                'label' => 'Регистрация'
            ],
            [
                'value' => Event::Deposit->value,
                'label' => 'Депозит'
            ],
        ];
    }

    public static function getLanguages(): array
    {
        return array_map(fn (Language $lng) => ['value' => $lng->value, 'label' => $lng->value], Language::cases());
    }

    public function __construct(private int $companyId)
    {
    }

    public function getPostbacks(): PostbackDTO
    {
        $registration = route('postback', ['status' => 'reg']);
        $deposit = route('postback', ['status' => 'dep']);

        return new PostbackDTO(
            "$registration&external_id={external_id}",
            "$deposit&external_id={external_id}",
        );
    }

    public function getByPublicId(int $publicId, ?bool $public = true): Application
    {
        $query = Application::query();
        if ($public) {
            $query->select(['id', 'status', 'public_id', 'name', 'domain_id', 'subdomain', 'pixel_id', 'pixel_key', 'link',
                'platform_type', 'landing_type', 'white_type', 'category', 'app_name', 'developer_name', 'uuid',
                'icon', 'description', 'downloads_count', 'rating', 'onesignal_id', 'onesignal_name', 'onesignal_auth_key',
                'owner_id', 'display_top_bar', 'full_domain', 'display_app_bar',
            ]);
        }

        $application = $query
            ->where('public_id', $publicId)
            ->where('company_id', $this->companyId)
            ->firstOrFail()
            ->load('files');
        if ($public) {
            $application->makeHidden('id');
        }
        $application->append('topApplicationIds');
        $application->append('applicationGeoLanguages');
        $application->makeHidden('topApplications');

        return $application;
    }

    public function getAllByPublicId(array $publicId, ?bool $public = true): Collection
    {
        $query = Application::query();
        if ($public) {
            $query->select(['id', 'status', 'public_id', 'name', 'domain_id', 'subdomain', 'pixel_id', 'pixel_key', 'link', 'geo',
                'platform_type', 'landing_type', 'white_type', 'language', 'category', 'app_name', 'developer_name', 'uuid',
                'icon', 'description', 'downloads_count', 'rating', 'onesignal_id', 'onesignal_name', 'onesignal_auth_key',
                'owner_id', 'display_top_bar', 'full_domain', 'display_app_bar',
            ]);
        }

        $application = $query
            ->whereIn('public_id', $publicId)
            ->where('company_id', $this->companyId)
            ->get()
            ->load('files');
        if ($public) {
            $application->makeHidden('id');
        }
        $application->append('topApplicationIds');
        $application->makeHidden('topApplications');

        return $application;
    }

    public function getTopApplicationsByPublicId(int $publicId): array
    {
        $application = Application::query()
            ->with(['topApplications'])
            ->where('company_id', $this->companyId)
            ->where('public_id', $publicId)
            ->firstOrFail();
        return $application->topApplications->map(function (Application $topApplication) {
            return $topApplication->public_id;
        })->toArray();
    }

    public function getApplicationsForSelection(int $userId)
    {
        $query = Application::query()
            ->selectRaw("applications.public_id as value, full_domain as label")
            ->where('company_id', $this->companyId)
            ->where('status', Status::Active->value);
        /** @var CompanyService $companyService */
        $companyService = app(CompanyService::class);
        $companyOwnerId = $companyService->get(false)->owner_id;
        if ($companyOwnerId !== $userId) {
            $applications = $this->getTeamsOrOwnApplications($userId)->pluck('public_id')->toArray();
            $query->whereIn('public_id', $applications);
        }
        return $query->get();
    }

    public function getListForSelections(int $userId)
    {
        $query = Application::query()
            ->selectRaw("applications.id as value, full_domain as label")
            ->where('company_id', $this->companyId)
            ->where('status', Status::Active->value);
        /** @var CompanyService $companyService */
        $companyService = app(CompanyService::class);
        $companyOwnerId = $companyService->get(false)->owner_id;
        if ($companyOwnerId !== $userId) {
            $applications = $this->getTeamsOrOwnApplications($userId)->pluck('public_id')->toArray();
            $query->whereIn('public_id', $applications);
        }
        return $query->get();
    }

    /**
     * @throws PushNotificationException
     */
    public function save(array $params, int $authUserId): Application
    {
        if (!isset($params['public_id']) || !$params['public_id']) {
            $application = new Application();
            $application->status = Status::Active->value;
            $application->company_id = $this->companyId;
            $application->owner_id = $authUserId;
            $application->created_by_id = $authUserId;
            $application->uuid = Str::uuid();
            $application->platform_type = PlatformType::Android->value;
            $application->landing_type = LandingType::Old->value;
            $application->category = Category::Gambling->value;
            $application->white_type = WhiteType::White->value;
        } else {
            $application = Application::query()
                ->where('public_id', $params['public_id'])
                ->where('company_id', $this->companyId)
                ->firstOrFail();
        }
        $presetStatus = $application->status;

        $application->fill($params);
        $application->save();

        if (isset($params['topApplicationIds']) && is_array($params['topApplicationIds'])) {
            $applications = Application::query()
                ->whereIn('public_id', $params['topApplicationIds'])
                ->where('company_id', $this->companyId)
                ->pluck('id');
            $application->topApplications()->sync($applications);
        }

        if (isset($params['applicationGeoLanguages']) && is_array($params['applicationGeoLanguages'])) {
            /**
             * @var ApplicationGeoLanguageService $applicationGeoLanguageService
             */
            $applicationGeoLanguageService = app(ApplicationGeoLanguageService::class);
            $applicationGeoLanguageService->saveGeoLanguages($application, $params['applicationGeoLanguages']);
        }

        /** @var OneSignalNotificationService $oneSignalNotificationService */
        $oneSignalNotificationService = app(OneSignalNotificationService::class);
        if (empty($params['public_id'])) {
            $oneSignalNotificationService->registerApplicationAndCreateKey($application);
        } else {
            $oneSignalNotificationService->updateApplication($application);
        }

        if (isset($params['files']) && count($params['files'])) {
            /** @var ApplicationFileService $applicationFileService */
            $applicationFileService = app(ApplicationFileService::class);
            $applicationFileService->saveFiles($application, $params['files']);
        }

        if (
            !empty($params['public_id'])
            && $params['status'] === Status::NotActive->value
            && $presetStatus == Status::Active->value
        ) {
            event(new ApplicationDeactivated($application));
        }

        $application->load('files');

        return $application;
    }

    public function clone(int $publicId, int $userId): Application
    {
        $application = Application::query()
            ->where('public_id', $publicId)
            ->where('company_id', $this->companyId)
            ->firstOrFail();
        $appParams = $application->toArray();
        unset($appParams['id']);
        unset($appParams['public_id']);
        unset($appParams['created_at']);
        unset($appParams['updated_at']);
        unset($appParams['onesignal_id']);
        unset($appParams['onesignal_name']);
        unset($appParams['onesignal_auth_key']);
        $appParams['uuid'] = Str::uuid();
        $appParams['owner_id'] = $userId;
        $appParams['subdomain'] = 'copy-' . $application->subdomain;
        $appParams['status'] = Status::NotActive;
        $imageIds = $application->files()->get()->pluck('id')->toArray();

        DB::beginTransaction();
        try {
            $newApplication = new Application();
            $newApplication->fill($appParams);
            $newApplication->save();
            $newApplication->files()->sync($imageIds);

            $languages = $application->applicationGeoLanguages;
            foreach ($languages as $language) {
                ApplicationGeoLanguage::query()->create([
                    'application_id' => $newApplication->id,
                    'geo' => $language->geo,
                    'language' => $language->language,
                ]);
            }

            /** @var OneSignalNotificationService $oneSignalNotificationService */
            $oneSignalNotificationService = app(OneSignalNotificationService::class);
            $oneSignalNotificationService->registerApplicationAndCreateKey($newApplication);
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
        return $this->getByPublicId($newApplication->public_id);
    }

    public function deactivate($id): Application
    {
        $application = Application::query()
            ->where('public_id', $id)
            ->where('company_id', $this->companyId)
            ->firstOrFail();
        $application->status = Status::NotActive->value;
        $application->save();
        event(new ApplicationDeactivated($application));
        return $application;
    }

    public function delete(int $id): Application
    {
        $application = Application::query()
            ->where('public_id', $id)
            ->where('company_id', $this->companyId)
            ->firstOrFail();
        $application->delete();
        return $application;
    }

    public function update(int $id, array $data): bool
    {
        return Application::query()
            ->where('id', $id)
            ->firstOrFail()
            ->update($data);
    }

    public function updateApplicationsOwners(array $appsOwners): bool
    {
        foreach ($appsOwners as $appOwner) {
            if (empty($appOwner['applicationId'] || empty($appOwner['userId']))) {
                return false;
            }
            $this->update($appOwner['applicationId'], ['owner_id' => $appOwner['userId']]);
        }

        return true;
    }

    public function getTeamsOrOwnApplications(int $userId): Collection
    {
        return User::query()
            ->select('applications.id', 'applications.owner_id', 'applications.public_id')
            ->leftJoin('teams', 'teams.id', '=', 'users.team_id')
            ->join('applications', 'applications.owner_id', '=', 'users.id')
            ->where('users.company_id', $this->companyId)
            ->where('teams.team_lead_id', $userId)
            ->orWhere('users.id', $userId)
            ->get();
    }
}
