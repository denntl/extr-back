<?php

namespace App\Services\Common\DataListing\Entities;

use App\Enums\Authorization\PermissionName;
use App\Models\PushTemplate;
use App\Services\Client\Application\ApplicationService;
use App\Services\Client\User\UserService;
use App\Services\Common\DataListing\CoreListing;
use App\Services\Common\DataListing\DTO\JoinDTO;
use App\Services\Common\DataListing\DTO\OrderDto;
use App\Services\Common\DataListing\Enums\JoinEnum;
use App\Services\Common\DataListing\Enums\SortingType;
use App\Services\Common\DataListing\FieldCollector;
use App\Services\Common\DataListing\Fields\FieldAction;
use App\Services\Common\DataListing\Fields\FieldInt;
use App\Services\Common\DataListing\Fields\FieldList;
use App\Services\Common\DataListing\Fields\FieldListAsString;
use App\Services\Common\DataListing\Fields\FieldString;
use App\Services\Manage\PushTemplate\PushTemplateService;
use Illuminate\Database\Eloquent\Model;

class ManagePushTemplatesListing extends CoreListing
{
    protected function getModel(): Model
    {
        return new PushTemplate();
    }

    protected function getFieldCollector(): FieldCollector
    {
        return FieldCollector::init()->setBatch([
            FieldInt::init('id', 'push_templates.id', 'ID')->makeInvisible(),
            FieldString::init('name', 'push_templates.name', 'Название'),
            FieldListAsString::init('geo', 'geo', 'Гео') ->setListName('geos'),
            FieldListAsString::init('events', 'events', 'События') ->setListName('events'),
            FieldList::init('owner', 'created_by', 'Пользователь')
                ->withScope('createdBy')
                ->setListName('users'),
            FieldList::init('is_active', 'push_templates.is_active', 'Статус')->setListName('statuses'),
            FieldAction::init('actions', '', 'Действия'),
        ]);
    }

    protected function defaultOrder(): OrderDto
    {
        return new OrderDto('id', SortingType::Desc);
    }

    protected function getPermission(): array
    {
        return [PermissionName::ManagePushTemplateRead];
    }

    protected function getListItems(): array
    {
        /** @var UserService $userService */
        $userService = app(UserService::class);
        return [
            'geos' => ApplicationService::getGeoList(),
            'users' => $userService->getUsersForSelections(),
            'events' => PushTemplateService::getEventsList(),
            'statuses' => PushTemplateService::getStatusesList(),
        ];
    }
}
