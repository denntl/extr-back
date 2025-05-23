<?php

namespace App\Services\Common\OneSignal;

use App\Enums\OneSignal\NotificationEventId;
use App\Models\Application;
use App\Models\OneSignalEvents;
use App\Models\OneSignalNotification;
use App\Models\PushNotification;
use App\Services\Common\OneSignal\DTO\ApiRequest\CreateApiKeyRequestDTO;
use App\Services\Common\OneSignal\DTO\ApiResponse\CreateUpdateApplicationResponseDTO;
use App\Services\Common\OneSignal\Exceptions\InvalidPushNotificationArgumentException;
use App\Services\Common\OneSignal\Exceptions\PushNotificationException;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

readonly class OneSignalNotificationService
{
    /**
     * @param PushNotification $pushNotification
     * @return OneSignalNotification
     * @throws InvalidPushNotificationArgumentException
     * @throws PushNotificationException
     * @throws \Throwable
     */
    public function create(PushNotification $pushNotification): OneSignalNotification
    {
        $oneSignalNotificationId = $this->sendRequest($pushNotification);

        if (!$oneSignalNotificationId) {
            throw new PushNotificationException('Error while send push notification');
        }

        try {
            DB::beginTransaction();

            $pushNotification->update([
                'last_onesignal_created_at' => Carbon::now('UTC'),
            ]);
            $oneSignalNotification = OneSignalNotification::query()->create([
                'onesignal_notification_id' => $oneSignalNotificationId,
                'push_notifications_id' => $pushNotification->id,
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return $oneSignalNotification;
    }

    /**
     * @param PushNotification $pushNotification
     * @return bool
     * @throws PushNotificationException
     */
    public function delete(PushNotification $pushNotification): bool
    {
        if (!$pushNotification->canBeCanceled()) {
            throw new PushNotificationException(
                'Error while delete push notification: already added in queue on OneSignal or less than ' .
                PushNotification::SKIP_SENDING_PUSH_NOTIFICATION_BEFORE_SECONDS .
                ' seconds before sending'
            );
        }

        if (!$this->cancelRequest($pushNotification)) {
            throw new PushNotificationException('Error while cancel push notification');
        }

        return OneSignalNotification::query()
            ->where('onesignal_notification_id', $pushNotification->oneSignalNotification->onesignal_notification_id)
            ->delete();
    }

    /**
     * @param PushNotification $pushNotification
     * @return OneSignalNotification
     * @throws InvalidPushNotificationArgumentException
     * @throws PushNotificationException
     * @throws \DateMalformedStringException
     */
    public function deleteAndCreate(PushNotification $pushNotification): OneSignalNotification
    {
        try {
            DB::beginTransaction();
            if ($pushNotification->oneSignalNotification && !$this->delete($pushNotification)) {
                throw new PushNotificationException('Error while delete push notification');
            }
            $newOneSignalNotification = $this->create($pushNotification);
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return $newOneSignalNotification;
    }

    /**
     * @param OneSignalNotification $oneSignalNotification
     * @return array
     * @throws PushNotificationException
     */
    public function get(OneSignalNotification $oneSignalNotification): array
    {
        if (!$oneSignalNotification = $this->getRequest($oneSignalNotification)) {
            throw new PushNotificationException('Error while get push notification');
        }

        return $oneSignalNotification;
    }

    /**
     * @param Application $application
     * @return bool
     * @throws PushNotificationException
     */
    public function registerApplication(Application $application): bool
    {
        $oneSignalApplication = $this->createApplicationRequest($application);
        if (!$oneSignalApplication) {
            $application->delete();
            throw new PushNotificationException('Error while create OneSignal application');
        }

        return $application->update(
            [
                'onesignal_id' => $oneSignalApplication['id'],
                'onesignal_name' => $oneSignalApplication['name'],
                //'onesignal_auth_key' => $oneSignalApplication['basic_auth_key'],
                // TODO change it after onesignal team create a solution for app auth_token
            ]
        );
    }

    /**
     * @param Application $application
     * @return Application
     * @throws PushNotificationException
     */
    public function registerApplicationAndCreateKey(Application $application): Application
    {
        if (!$application->onesignal_id || !$application->onesignal_name) {
            $oneSignalApplication = $this->createApplicationRequest($application);

            if (!$oneSignalApplication) {
                $application->delete();
                throw new PushNotificationException('Error while create OneSignal application');
            }

            $application->onesignal_id = $oneSignalApplication->id;
            $application->onesignal_name = $oneSignalApplication->name;
        }

        if (!$application->onesignal_auth_key) {
            $oneSignalKey = $this->createApiKeyRequest($application);

            $application->onesignal_auth_key = $oneSignalKey;
        }

        $application->save();


        return $application;
    }

    /**
     * @param Application $application
     * @return bool
     * @throws PushNotificationException
     */
    public function updateApplication(Application $application): bool
    {
        $oneSignalApplication = $this->updateApplicationRequest($application);
        if (!$oneSignalApplication) {
            $application->delete();
            throw new PushNotificationException('Error while update OneSignal application');
        }

        return $application->update(
            [
                'onesignal_name' => $oneSignalApplication['name'],
                //'onesignal_auth_key' => $oneSignalApplication['basic_auth_key'],
                // TODO change it after onesignal team create a solution for app auth_token
            ]
        );
    }

    /**
     * @return Collection
     */
    public function getPushNotificationsForUpdate(): Collection
    {
        return OneSignalNotification::query()
            ->where('last_webhook_accepted_at', '>', Carbon::now()->subHours(
                PushNotification::GET_PUSH_NOTIFICATIONS_FOR_UPDATE_HOURS
            ))
            ->orWhereNull('last_webhook_accepted_at')
            ->get();
    }

    public function getByNotificationId(string $notificationId): ?OneSignalNotification
    {
        return OneSignalNotification::query()
            ->where('onesignal_notification_id', $notificationId)
            ->first();
    }
    public function getByTemplateId(int $templateId): Collection
    {
        return OneSignalNotification::query()
            ->where('onesignal_template_id', $templateId)
            ->get();
    }

    /**
     * @param OneSignalEvents $onesignalEvent
     * @return int
     */
    public function incrementEventCounter(OneSignalEvents $onesignalEvent): int
    {
        $column = match ($onesignalEvent->event_id) {
            NotificationEventId::Display->value   => 'delivered',
            NotificationEventId::Clicked->value   => 'clicked',
            NotificationEventId::Dismissed->value => 'dismissed',
            default => throw new \InvalidArgumentException('Invalid event id'),
        };

        return OneSignalNotification::query()
            ->where('onesignal_notification_id', $onesignalEvent->notification_id)
            ->update([
                $column => OneSignalEvents::query()
                    ->where('notification_id', $onesignalEvent->notification_id)
                    ->where('event_id', $onesignalEvent->event_id)
                    ->count(),
            ]);
    }

    /**
     * @param string $onesignalNotificationId
     * @param array $data
     * @return int
     */
    public function updateByOneSignalNotificationId(string $onesignalNotificationId, array $data): int
    {
        return OneSignalNotification::query()
            ->where(['onesignal_notification_id' => $onesignalNotificationId])
            ->update($data);
    }

    /**
     * @param string $onesignalNotificationId
     * @return int
     */
    public function updateLastWebhookAcceptedAt(string $onesignalNotificationId): int
    {
        return $this->updateByOneSignalNotificationId($onesignalNotificationId, [
            'last_webhook_accepted_at' => Carbon::now(),
        ]);
    }

    /**************************************** API METHODS ****************************************/

    /**
     * @param PushNotification $pushNotification
     * @return mixed
     * @throws InvalidPushNotificationArgumentException
     * @throws PushNotificationException
     */
    public function sendRequest(PushNotification $pushNotification): mixed
    {
        /** @var OneSignalApiClient $oneSignalApiClient */
        $oneSignalApiClient = app(OneSignalApiClient::class, ['application' => $pushNotification->application]);

        return $oneSignalApiClient->sendPushNotification($pushNotification->toDTO())->getId();
    }

    /**
     * @param OneSignalNotification $oneSignalNotification
     * @return array
     * @throws PushNotificationException
     */
    public function getRequest(OneSignalNotification $oneSignalNotification): array
    {
        /** @var OneSignalApiClient $oneSignalApiClient */
        $oneSignalApiClient = app(OneSignalApiClient::class, ['application' => $oneSignalNotification->pushNotification->application]);

        return $oneSignalApiClient->getPushNotification(
            $oneSignalNotification->onesignal_notification_id
        )->toArray();
    }

    /**
     * @param $pushNotification
     * @return bool
     * @throws PushNotificationException
     */
    public function cancelRequest($pushNotification): bool
    {
        /** @var OneSignalApiClient $oneSignalApiClient */
        $oneSignalApiClient = app(OneSignalApiClient::class, ['application' => $pushNotification->application]);

        $result = $oneSignalApiClient->cancelPushNotification(
            $pushNotification->oneSignalNotification->onesignal_notification_id
        );

        return $result->isSuccess();
    }

    /**
     * @param Application $application
     * @return CreateUpdateApplicationResponseDTO
     * @throws PushNotificationException
     */
    public function createApplicationRequest(Application $application): CreateUpdateApplicationResponseDTO
    {
        /** @var OneSignalApiClient $oneSignalApiClient */
        $oneSignalApiClient = app(OneSignalApiClient::class, [
            'application' => $application,
            'isCreateApplicationRequest' => true
        ]);

        return $oneSignalApiClient->createApplication(
            $application->toOneSignalCreateApplicationDTO()
        );
    }

    /**
     * @param Application $application
     * @return string
     * @throws PushNotificationException
     */
    public function createApiKeyRequest(Application $application): string
    {
        /** @var OneSignalApiClient $oneSignalApiClient */
        $oneSignalApiClient = app(OneSignalApiClient::class, [
            'application' => $application,
            'isCreateApplicationRequest' => true
        ]);

        return $oneSignalApiClient->createApiKey(
            new CreateApiKeyRequestDTO($application->name)
        )->formatted_token;
    }

    /**
     * @param Application $application
     * @return bool|array
     * @throws PushNotificationException
     */
    public function updateApplicationRequest(Application $application): bool|array
    {
        /** @var OneSignalApiClient $oneSignalApiClient */
        $oneSignalApiClient = app(OneSignalApiClient::class, ['application' => $application]);

        return $oneSignalApiClient->updateApplication(
            $application->toOneSignalCreateApplicationDTO()
        )->toArray();
    }
}
