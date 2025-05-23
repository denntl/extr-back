<?php

namespace App\Services\Common\DataListing\Entities;

use App\Enums\Authorization\PermissionName;
use App\Services\Common\DataListing\CoreListing;
use App\Services\Common\DataListing\DTO\OrderDto;
use App\Services\Common\DataListing\Enums\SortingType;
use App\Services\Common\DataListing\FieldCollector;
use App\Services\Common\DataListing\Fields\FieldAction;
use App\Services\Common\DataListing\Fields\FieldBool;
use App\Services\Common\DataListing\Fields\FieldInt;
use App\Services\Common\DataListing\Fields\FieldString;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class ManageRolesListing extends CoreListing
{
    protected function getModel(): Model
    {
        return new Role();
    }

    protected function getFieldCollector(): FieldCollector
    {
        return FieldCollector::init()->setBatch([
            FieldInt::init('id', 'roles.id', 'ID'),
            FieldString::init('name', 'roles.name', 'Название'),
            FieldBool::init('is_predefined', 'roles.is_predefined', 'Статичная роль')
                ->makeInvisible()
                ->makeUnSearchable()
                ->makeUnSortable(),
            FieldAction::init('actions', '', 'Действия'),
        ]);
    }

    protected function defaultOrder(): OrderDto
    {
        return new OrderDto('id', SortingType::Asc);
    }

    protected function getPermission(): array
    {
        return [PermissionName::ManageRoleRead];
    }
}
