<?php

namespace App\Services\Common\DataListing\Entities;

use App\Enums\Authorization\PermissionName;
use App\Models\OneSignalNotification;
use App\Services\Client\PushNotification\PushNotificationService;
use App\Services\Common\DataListing\CoreListing;
use App\Services\Common\DataListing\DTO\OrderDto;
use App\Services\Common\DataListing\Enums\SortingType;
use App\Services\Common\DataListing\FieldCollector;
use App\Services\Common\DataListing\Fields\FieldInt;
use App\Services\Common\DataListing\Fields\FieldList;
use App\Services\Common\DataListing\Fields\FieldString;
use App\Services\Manage\Application\ApplicationService;
use App\Services\Manage\User\UserService;
use Illuminate\Database\Eloquent\Model;

class ManagePushNotificationStatisticsListing extends CoreListing
{
    protected function getModel(): Model
    {
        return new OneSignalNotification();
    }

    /**
     * @return array|PermissionName[]
     */
    protected function getPermission(): array
    {
        return [PermissionName::ManagePushNotificationStatisticRead];
    }

    protected function getFieldCollector(): FieldCollector
    {
        return FieldCollector::init()
            ->setBatch([
                FieldInt::init('id', 'onesignal_notifications.id', __('push-notifications.id'))
                    ->withScope('pushNotification'),
                FieldString::init('name', 'push_notifications.name', __('push-notifications.name')),
                FieldList::init('username', 'users.username', __('push-notifications.username'))
                    ->withScope('createdBy')
                    ->setFilterField('users.id')
                    ->setListName('users'),
                FieldList::init('full_domain', 'applications.full_domain', __('push-notifications.full_domain'))
                    ->withScope('application')
                    ->setFilterField('applications.id')
                    ->setListName('applications'),
                FieldList::init('type', "(CASE WHEN push_notifications.type = 1 THEN 'Одноразовый' ELSE 'Регулярный' END)", __('push-notifications.type'))
                    ->setFilterField('push_notifications.type')
                    ->setListName('types'),
                FieldString::init('queued_at', 'onesignal_notifications.queued_at', __('push-notifications.queued_at')),
                FieldString::init('completed_at', 'onesignal_notifications.completed_at', __('push-notifications.completed_at')),
                FieldInt::init('sent', 'onesignal_notifications.sent', __('push-notifications.sent')),
                FieldInt::init('delivered', 'onesignal_notifications.delivered', __('push-notifications.delivered')),
                FieldInt::init('clicked', 'onesignal_notifications.clicked', __('push-notifications.clicked')),
                FieldInt::init('dismissed', 'onesignal_notifications.dismissed', __('push-notifications.dismissed')),
                FieldString::init(
                    'cr_to_click',
                    "(CASE
                        WHEN delivered = 0 OR clicked = 0 THEN '0%'
                        ELSE ROUND(((clicked::float / delivered::float) * 100)::NUMERIC, 2)::TEXT || '%'
                    END)",
                    __('push-notifications.cr_to_click')
                )
            ]);
    }

    protected function defaultOrder(): OrderDto
    {
        return new OrderDto('id', SortingType::Desc);
    }

    protected function getListItems(): array
    {
        /** @var UserService $userService */
        $userService = app(UserService::class);
        /** @var ApplicationService $applicationService */
        $applicationService = app(ApplicationService::class);

        return [
            'users' => $userService->getUsersForSelections(),
            'types' => PushNotificationService::getTypeList(),
            'applications' => $applicationService->getListForSelections(),
        ];
    }
}
