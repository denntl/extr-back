<?php

namespace App\Services\Common\OnesignalTemplate;

use App\Enums\PushNotification\Type;
use App\Models\Application;
use App\Models\OneSignalNotification;
use App\Services\Client\OnesignalTemplate\AbstractDelivery;
use App\Services\Client\OnesignalTemplate\DeliverySettings\SingleDeliveryService;
use App\Services\Client\OnesignalTemplate\DTOs\TemplateToSendDTO;
use App\Services\Common\OneSignal\Exceptions\ApplicationHasNoSubscriberException;
use App\Services\Common\OneSignal\Exceptions\InvalidPushNotificationArgumentException;
use App\Services\Common\OneSignal\Exceptions\PushNotificationException;
use App\Services\Common\OneSignal\OneSignalApiClient;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OnesignalDeliveryService
{
    private function getSettingService(Type $type): AbstractDelivery
    {
        return match ($type) {
            Type::Single => new SingleDeliveryService(),
            Type::Regular => throw new \Exception('To be implemented'),
        };
    }

    public function getAllTemplatesById(int $templateId, Type $type): Collection
    {
        return $this->getSettingService($type)->getAllTemplatesById($templateId);
    }
    public function getAllTemplatesByIdDTO($template): TemplateToSendDTO
    {
        $type = Type::tryFrom($template->type);
        return $this->getSettingService($type)->convertTemplateToDto($template);
    }

    /**
     * @throws \Throwable
     * @throws ApplicationHasNoSubscriberException
     * @throws PushNotificationException
     * @throws InvalidPushNotificationArgumentException
     */
    public function sendToOnesignal(TemplateToSendDTO $template)
    {
        /** @var Application $application */
        $application = Application::where('id', $template->application_id)->firstOrFail();
        /** @var OneSignalApiClient $oneSignalApiClient */
        $oneSignalApiClient = app(OneSignalApiClient::class, ['application' => $application]);

        $type = Type::tryFrom($template->type);
        $dto = $this->getSettingService($type)->sendRequestDTO($template, $application);
        $oneSignalNotificationId = $oneSignalApiClient->sendPushNotification($dto)->getId();

        if (!$oneSignalNotificationId) {
            Log::driver('onesignal')
                ->error(
                    'Create onesignal push for application ' . $template->application_id . ' and geo ' . $template->geo_code,
                    ['request' => $dto->toArray()]
                );
            throw new PushNotificationException('Error while send push notification');
        }
        Log::driver('onesignal')
            ->info(
                'Create onesignal push for application ' . $template->application_id . ' and geo ' . $template->geo_code,
                ['request' => $dto->toArray()]
            );

        try {
            DB::beginTransaction();

            $this->getSettingService($type)->setHandled($template->onesignal_templates_id);

            $oneSignalNotification = OneSignalNotification::query()->create([
                'onesignal_notification_id' => $oneSignalNotificationId,
                'onesignal_template_id' => $template->onesignal_templates_id,
                'application_id' => $template->application_id,
                'geo_id' => $template->geo_id,
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            Log::error($e);
            DB::rollBack();
            throw $e;
        }

        return $oneSignalNotification;
    }
    public function getSettingsToPushQuery(Type $type): Builder
    {
        return $this->getSettingService($type)->getSettingsToPush();
    }

    /**
     * @throws PushNotificationException
     */
    public function removeFromOnesignal(OneSignalNotification $oneSignalNotification)
    {
        Log::info('try to delete ' . $oneSignalNotification->onesignal_notification_id, $oneSignalNotification->toArray());
        /** @var Application $application */
        $application = Application::where('id', $oneSignalNotification->getAttribute('application_id'))->firstOrFail();

        /** @var OneSignalApiClient $oneSignalApiClient */
        $oneSignalApiClient = app(OneSignalApiClient::class, ['application' => $application]);

        $oneSignalNotificationId = $oneSignalApiClient->cancelPushNotification($oneSignalNotification->onesignal_notification_id);
        if (!$oneSignalNotificationId->isSuccess()) {
            Log::driver('onesignal')
                ->error('Cancel onesignal push. Push notification id=' . $oneSignalNotification->onesignal_notification_id);
            throw new PushNotificationException('Error while cancel push notification');
        }
        Log::driver('onesignal')
            ->info('Cancel onesignal push. Push notification id=' . $oneSignalNotification->onesignal_notification_id);
    }
}
