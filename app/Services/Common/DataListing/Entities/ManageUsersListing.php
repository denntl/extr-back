<?php

namespace App\Services\Common\DataListing\Entities;

use App\Enums\Authorization\PermissionName;
use App\Models\TelegraphBot;
use App\Models\User;
use App\Services\Common\DataListing\CoreListing;
use App\Services\Common\DataListing\DTO\OrderDto;
use App\Services\Common\DataListing\Enums\SortingType;
use App\Services\Common\DataListing\FieldCollector;
use App\Services\Common\DataListing\Fields\FieldAction;
use App\Services\Common\DataListing\Fields\FieldDate;
use App\Services\Common\DataListing\Fields\FieldInt;
use App\Services\Common\DataListing\Fields\FieldList;
use App\Services\Common\DataListing\Fields\FieldString;
use App\Services\Manage\Company\CompanyService;
use App\Services\Manage\User\UserService;
use Illuminate\Database\Eloquent\Model;

class ManageUsersListing extends CoreListing
{
    protected function getModel(): Model
    {
        return new User();
    }

    /**
     * @return array|PermissionName[]
     */
    protected function getPermission(): array
    {
        return [PermissionName::ManageUserRead];
    }

    protected function getFieldCollector(): FieldCollector
    {
        return FieldCollector::init()
            ->setBatch([
                FieldInt::init('id', 'users.id', 'ID'),
                FieldDate::init('created_at', 'users.created_at', 'Создано'),
                FieldDate::init('updated_at', 'users.updated_at', 'Обновлено'),
                FieldString::init('name', 'users.username', 'Имя'),
                FieldString::init('email', 'users.email', 'Email'),
                FieldList::init('status', 'users.status', 'Статус')->setListName('statuses'),
                FieldList::init('is_employee', 'users.is_employee', 'Сотрудник')
                    ->setListName('is_employee'),
                FieldList::init('company_name', 'companies.name', 'Компания')
                    ->setFilterField('companies.id')
                    ->setListName('companies')
                    ->withScope('company'),
                FieldString::init('telegram_name', 'users.username', 'TG username'),
                FieldString::init('telegram_id', 'users.telegram_id', 'TG ID'),
                FieldAction::init('actions', '', 'Действия'),
            ]);
    }

    protected function defaultOrder(): OrderDto
    {
        return new OrderDto('id', SortingType::Desc);
    }

    protected function getListItems(): array
    {
        /** @var CompanyService $companyService */
        $companyService = app(CompanyService::class);
        /** @var UserService $userService */
        $userService = app(UserService::class);
        return [
            'companies' => $companyService->getCompanyForSelections(),
            'statuses' => $userService->getStatusesForSelections(),
            'is_employee' => $userService->getEmployeeForSelections(),
        ];
    }
}
