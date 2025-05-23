<?php

namespace App\Services\Manage\Application;

use App\Enums\Application\Category;
use App\Enums\Application\LandingType;
use App\Enums\Application\PlatformType;
use App\Enums\Application\Status;
use App\Enums\Application\WhiteType;
use App\Events\Client\Application\ApplicationDeactivated;
use App\Models\Application;
use App\Models\ApplicationGeoLanguage;
use App\Services\Client\Application\DTO\PostbackDTO;
use App\Services\Common\Application\ApplicationGeoLanguageService;
use App\Services\Common\ApplicationFiles\ApplicationFileService;
use App\Services\Common\OneSignal\OneSignalNotificationService;
use App\Services\Manage\User\DTO\NewApplicationOwnersDTO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

readonly class ApplicationService
{
    public function getListForSelections(): Collection
    {
        return Application::query()
            ->selectRaw("applications.id as value, full_domain as label")
            ->get();
    }

    public function update(int $id, array $data): bool
    {
        return Application::query()
            ->where('id', $id)
            ->firstOrFail()
            ->update($data);
    }

    public function getById(int $id): Application
    {
        return Application::query()->where('id', $id)
            ->firstOrFail()->load('files')->append('applicationGeoLanguages');
    }

    /**
     * @param array<NewApplicationOwnersDTO> $appsOwners
     * @return bool
     */
    public function updateApplicationsOwners(array $appsOwners): bool
    {
        foreach ($appsOwners as $appOwner) {
            if (!$appOwner instanceof NewApplicationOwnersDTO) {
                return false;
            }
            if (empty($appOwner->applicationId || empty($appOwner->userId))) {
                return false;
            }
            $this->update($appOwner->applicationId, ['owner_id' => $appOwner->userId]);
        }

        return true;
    }

    public function save(array $params, int $authUserId): Application
    {
        if (!isset($params['id']) || !$params['id']) {
            $application = new Application();
            $application->status = Status::Active->value;
            $application->company_id = $params['company_id'];
            $application->owner_id = $params['owner_id'] ?? $authUserId;
            $application->created_by_id = $authUserId;
            $application->uuid = Str::uuid();
            $application->platform_type = PlatformType::Android->value;
            $application->landing_type = LandingType::Old->value;
            $application->category = Category::Gambling->value;
            $application->white_type = WhiteType::White->value;
        } else {
            $application = Application::query()->where('id', $params['id'])->firstOrFail();
        }
        $presetStatus = $application->status;

        $application->fill($params);
        $application->save();

        if (isset($params['topApplicationIds']) && is_array($params['topApplicationIds'])) {
            $applications = Application::query()
                ->whereIn('public_id', $params['topApplicationIds'])
                ->where('company_id', $application->company_id)
                ->pluck('id');
            $application->topApplications()->sync($applications);
        }

        /** @var OneSignalNotificationService $oneSignalNotificationService */
        $oneSignalNotificationService = app(OneSignalNotificationService::class);
        if (empty($params['id'])) {
            $oneSignalNotificationService->registerApplicationAndCreateKey($application);
        } else {
            $oneSignalNotificationService->updateApplication($application);
        }

        if (isset($params['files']) && count($params['files'])) {
            /** @var ApplicationFileService $applicationFileService */
            $applicationFileService = app(ApplicationFileService::class);
            $applicationFileService->saveFiles($application, $params['files']);
        }

        if (isset($params['applicationGeoLanguages']) && is_array($params['applicationGeoLanguages'])) {
            /**
             * @var ApplicationGeoLanguageService $applicationGeoLanguageService
             */
            $applicationGeoLanguageService = app(ApplicationGeoLanguageService::class);
            $applicationGeoLanguageService->saveGeoLanguages($application, $params['applicationGeoLanguages']);
        }

        if (
            !empty($params['id'])
            && $params['status'] === Status::NotActive->value
            && $presetStatus == Status::Active->value
        ) {
            event(new ApplicationDeactivated($application));
        }

        $application->load('files');
        return $application;
    }

    public function clone(int $id, int $userId): Application
    {
        $application = Application::query()
            ->where('id', $id)
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
        return $this->getById($newApplication->id);
    }

    public function deactivate($id): void
    {
        $application = Application::query()
            ->where('id', $id)
            ->firstOrFail();
        $application->status = Status::NotActive->value;
        $application->save();

        event(new ApplicationDeactivated($application));
    }

    public function delete(int $id): void
    {
        $application = Application::query()
            ->where('id', $id)
            ->firstOrFail();
        $application->delete();
    }

    public function restore(int $id): void
    {
        Application::query()
            ->where('id', $id)
            ->restore();
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


    // FIXME: move common methods in common service
}
