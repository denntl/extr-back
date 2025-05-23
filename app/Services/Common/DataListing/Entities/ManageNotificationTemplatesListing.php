<?php

namespace App\Services\Common\DataListing\Entities;

use App\Enums\Authorization\PermissionName;
use App\Enums\NotificationTemplate\Entity;
use App\Models\NotificationTemplate;
use App\Services\Common\DataListing\CoreListing;
use App\Services\Common\DataListing\DTO\OrderDto;
use App\Services\Common\DataListing\Enums\SortingType;
use App\Services\Common\DataListing\FieldCollector;
use App\Services\Common\DataListing\Fields\FieldAction;
use App\Services\Common\DataListing\Fields\FieldList;
use App\Services\Common\DataListing\Fields\FieldString;
use App\Services\Manage\NotificationTemplate\NotificationTemplateService;
use Illuminate\Database\Eloquent\Model;

class ManageNotificationTemplatesListing extends CoreListing
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
        return [PermissionName::ManageNotificationTemplateRead];
    }

    protected function getFieldCollector(): FieldCollector
    {
        return FieldCollector::init()
            ->setBatch([
                FieldString::init('id', 'notification_templates.id', 'ID'),
                FieldString::init('name', 'notification_templates.name', 'Название'),
                FieldList::init('entity', 'notification_templates.entity', 'Раздел')
                    ->setTransform(function ($entity) {
                        return __("notifications." . Entity::tryFrom($entity)?->name);
                    })
                    ->setListName('entities'),
                FieldList::init('event', 'notification_templates.event', 'Событие')
                    ->setTransform(function ($event) {
                        return __("notifications.{$event}");
                    })
                    ->setListName('events'),
                FieldList::init('is_active', 'notification_templates.is_active', 'Статус')->setListName('activeOptions'),
                FieldList::init('enable_for_client', 'notification_templates.enable_for_client', 'Доступно для клиента')->setListName('enableForClientOptions'),
//                FieldList::init('all_roles', 'notification_templates.all_roles', 'Для всех ролей')->setListName('allRolesOptions'),
                FieldAction::init('actions', '', 'Действия'),
            ]);
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
            'enableForClientOptions' => $notificationTemplateService->enableForClientOptions(),
//            'allRolesOptions' => $notificationTemplateService->allRolesOptions(),
        ];
    }
}
