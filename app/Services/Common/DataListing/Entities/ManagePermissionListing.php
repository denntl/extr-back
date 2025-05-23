<?php

namespace App\Services\Common\DataListing\Entities;

use App\Enums\Authorization\PermissionName;
use App\Services\Common\DataListing\CoreListing;
use App\Services\Common\DataListing\DTO\OrderDto;
use App\Services\Common\DataListing\Enums\SortingType;
use App\Services\Common\DataListing\FieldCollector;
use App\Services\Common\DataListing\Fields\FieldInt;
use App\Services\Common\DataListing\Fields\FieldString;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use App\Models\Permission;

class ManagePermissionListing extends CoreListing
{
    protected function getModel(): Model
    {
        return new Permission();
    }

    protected function getFieldCollector(): FieldCollector
    {
        return FieldCollector::init()->setBatch([
            FieldInt::init('id', 'permissions.id', 'ID')
                ->makeUnSearchable()
                ->hideInFilterSelect(),
            FieldString::init('name', 'permissions.name', 'Название')
                ->makeUnSearchable()
                ->makeUnSortable()
                ->hideInFilterSelect(),
            FieldString::init('description', 'permissions.name', 'Описание')
                ->makeUnSearchable()
                ->makeUnSortable()
                ->hideInFilterSelect(),
        ]);
    }

    protected function defaultOrder(): OrderDto
    {
        return new OrderDto('id', SortingType::Asc);
    }

    protected function getPermission(): array
    {
        return [PermissionName::ManagePermissionRead];
    }

    protected function transformData(Collection $rows): Collection
    {
        return $rows->map(function (Permission $permission) {
            $permission->description = __("permissions.$permission->name.description");
            $permission->name = __("permissions.$permission->name.name");
            return $permission;
        });
    }
}
