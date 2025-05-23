<?php

namespace App\Services\Common\DataListing\Entities;

use App\Enums\Authorization\PermissionName;
use App\Models\NotificationTemplate;
use App\Services\Common\DataListing\CoreListing;
use App\Services\Common\DataListing\DTO\FilterDTO;
use App\Services\Common\DataListing\DTO\OrderDto;
use App\Services\Common\DataListing\Enums\FilterOperator;
use App\Services\Common\DataListing\Enums\SortingType;
use App\Services\Common\DataListing\FieldCollector;
use App\Services\Common\DataListing\Fields\FieldAction;
use App\Services\Common\DataListing\Fields\FieldBool;
use App\Services\Common\DataListing\Fields\FieldList;
use App\Services\Common\DataListing\Fields\FieldString;
use App\Services\Manage\NotificationTemplate\NotificationTemplateService;
use Illuminate\Database\Eloquent\Model;

class ClientNotificationListing extends CoreListing
{
    protected function getModel(): Model
    {
        return new NotificationTemplate();
    }

    /**
     * @return array|PermissionName[]
     */
    protected function getPermission(): array
    {
        return [PermissionName::ClientNotificationRead];
    }

    protected function getFieldCollector(): FieldCollector
    {
        return FieldCollector::init()
            ->setBatch([
                FieldString::init('id', 'notification_templates.id', 'ID')->makeInvisible()->makeUnSearchable()->hideInFilterSelect(),
                FieldString::init('name', 'notification_templates.name', 'Название'),
                FieldList::init('entity', 'notification_templates.entity', 'Раздел')->setListName('entities'),
                FieldList::init('event', 'notification_templates.event', 'Событие')->setListName('events'),
                FieldList::init('is_enabled', '(CASE WHEN company_notifications.is_enabled IS FALSE THEN FALSE ELSE TRUE END)', 'Статус')
                    ->setListName('activeOptions')
                    ->withScope('companyNotifications', $this->listingModel->user->company_id),
                FieldBool::init('is_active', 'notification_templates.is_active')->hideInSelect(),
                FieldBool::init('enable_for_client', 'notification_templates.enable_for_client')->hideInSelect(),
                FieldAction::init('actions', '', 'Действия'),
            ]);
    }

    protected function predefinedFilters(): array
    {
        return [
            new FilterDTO('is_active', FilterOperator::Equal, true),
            new FilterDTO('enable_for_client', FilterOperator::Equal, true),
        ];
    }

    protected function defaultOrder(): OrderDto
    {
        return new OrderDto('id', SortingType::Desc);
    }

    protected function getListItems(): array
    {
        /** @var NotificationTemplateService $notificationTemplateService */
        $notificationTemplateService = app(NotificationTemplateService::class);
        return [
            'entities' => $notificationTemplateService->getEntitiesList(),
            'events' => $notificationTemplateService->getEventsList(),
            'activeOptions' => $notificationTemplateService->activeOptions(),
        ];
    }
}
