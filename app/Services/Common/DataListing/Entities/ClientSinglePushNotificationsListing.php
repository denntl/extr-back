<?php

namespace App\Services\Common\DataListing\Entities;

use App\Enums\Authorization\PermissionName;
use App\Enums\PushNotification\Type;
use App\Models\PushNotification;
use App\Services\Client\Application\ApplicationService;
use App\Services\Client\Company\CompanyService;
use App\Services\Client\PushNotification\PushNotificationService;
use App\Services\Common\DataListing\CoreListing;
use App\Services\Common\DataListing\DTO\FilterDTO;
use App\Services\Common\DataListing\DTO\OrderDto;
use App\Services\Common\DataListing\Enums\FilterOperator;
use App\Services\Common\DataListing\Enums\SortingType;
use App\Services\Common\DataListing\FieldCollector;
use App\Services\Common\DataListing\Fields\FieldAction;
use App\Services\Common\DataListing\Fields\FieldInt;
use App\Services\Common\DataListing\Fields\FieldList;
use App\Services\Common\DataListing\Fields\FieldListAsString;
use App\Services\Common\DataListing\Fields\FieldString;
use App\Services\Manage\PushTemplate\PushTemplateService;
use App\Services\Manage\User\UserService;
use Illuminate\Database\Eloquent\Model;

class ClientSinglePushNotificationsListing extends CoreListing
{
    protected function getModel(): Model
    {
        return new PushNotification();
    }

    protected function getFieldCollector(): FieldCollector
    {
        return FieldCollector::init()->setBatch([
            FieldInt::init('id', 'push_notifications.id', 'ID')->makeInvisible(),
            FieldInt::init('company_id', 'applications.company_id')->hideInSelect(),
            FieldInt::init('application_id', 'applications.id')->hideInSelect(),
            FieldInt::init('type', 'push_notifications.type', 'Type')->makeInvisible(),
            FieldString::init('name', 'push_notifications.name', 'Название'),
            FieldList::init('creator', 'users.username', 'Пользователь')
                ->withScope('createdBy')
                ->setFilterField('push_notifications.created_by')
                ->setListName('users'),
            FieldString::init('application', 'applications.full_domain', 'Приложение')
                ->withScope('application'),
            FieldListAsString::init('events', 'push_notifications.events', 'События')
                ->setListName('events'),
            FieldList::init('template', 'push_templates.name', 'Шаблон')
                ->withScope('pushTemplate')
                ->setFilterField('push_templates.id')
                ->setListName('templates'),
            FieldListAsString::init('geo', 'push_notifications.geo', 'Гео')
                ->setListName('geos'),
            FieldString::init('send_time', "concat(push_notifications.date, ' ', push_notifications.time)", 'Время отправки')
                ->setFilterField('push_notifications.date'),
            FieldList::init('status', 'push_notifications.status', 'Статус')
                ->setListName('statuses'),
            FieldAction::init('actions', '', 'Действия'),
        ]);
    }

    protected function defaultOrder(): OrderDto
    {
        return new OrderDto('id', SortingType::Desc);
    }

    protected function getPermission(): array
    {
        return [PermissionName::ClientSinglePushNotificationRead];
    }

    protected function predefinedFilters(): array
    {
        $commonFilter = [
            new FilterDTO('type', FilterOperator::Equal, Type::Single->value),
            new FilterDTO('company_id', FilterOperator::Equal, $this->listingModel->user->company_id),
        ];

        /** @var CompanyService $companyService */
        $companyService = app(CompanyService::class);
        /** @var ApplicationService $applicationService */
        $applicationService = app(ApplicationService::class);

        $userId = $this->listingModel->user->id;
        $companyOwnerId = $companyService->get(false)->owner_id;
        if ($companyOwnerId === $userId) {
            return $commonFilter;
        }

        $applications = $applicationService->getTeamsOrOwnApplications($this->listingModel->user->id)->pluck('id')->toArray();
        return array_merge($commonFilter, [
            new FilterDTO('application_id', FilterOperator::In, $applications),
        ]);
    }

    protected function getListItems(): array
    {
        /** @var UserService $userService */
        $userService = app(UserService::class);
        /** @var PushTemplateService $templateService */
        $templateService = app(PushTemplateService::class);
        return [
            'templates' => $templateService->getListForSelections(),
            'geos' => ApplicationService::getGeoList(),
            'users' => $userService->getUsersForSelections(),
            'events' => PushTemplateService::getEventsList(),
            'statuses' => PushNotificationService::getStatusList(),
        ];
    }
}
