<?php

namespace App\Services\Common\DataListing\Entities;

use App\Enums\Authorization\PermissionName;
use App\Models\Company;
use App\Services\Common\DataListing\CoreListing;
use App\Services\Common\DataListing\DTO\OrderDto;
use App\Services\Common\DataListing\Enums\SortingType;
use App\Services\Common\DataListing\FieldCollector;
use App\Services\Common\DataListing\Fields\FieldAction;
use App\Services\Common\DataListing\Fields\FieldDate;
use App\Services\Common\DataListing\Fields\FieldInt;
use App\Services\Common\DataListing\Fields\FieldList;
use App\Services\Common\DataListing\Fields\FieldString;
use App\Services\Manage\User\UserService;
use Illuminate\Database\Eloquent\Model;

class ManageCompaniesListing extends CoreListing
{
    protected function getModel(): Model
    {
        return new Company();
    }

    /**
     * @return array|PermissionName[]
     */
    protected function getPermission(): array
    {
        return [PermissionName::ManageCompanyRead];
    }

    protected function getFieldCollector(): FieldCollector
    {
        return FieldCollector::init()
            ->setBatch([
                FieldInt::init('id', 'companies.id', 'ID'),
                FieldDate::init('created_at', 'companies.created_at', 'Создано'),
                FieldDate::init('updated_at', 'companies.updated_at', 'Обновлено'),
                FieldString::init('name', 'companies.name', 'Компания'),
                FieldList::init('owner', 'users.username', 'Руководитель')
                    ->withScope('owner')
                    ->setFilterField('companies.owner_id')
                    ->setListName('users'),
                FieldInt::init('user_count', 'uc.count', 'Участники')->withScope('userCount'),
                FieldInt::init('team_count', 'tc.count', 'Команды')->withScope('teamCount'),
                FieldAction::init('actions', '', 'Действия'),
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
        return [
            'users' => $userService->getUsersForSelections(),
        ];
    }
}
