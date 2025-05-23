<?php

namespace App\Services\Common\DataListing\Entities;

use App\Enums\Authorization\PermissionName;
use App\Models\Domain;
use App\Services\Client\Domain\DomainService;
use App\Services\Common\DataListing\CoreListing;
use App\Services\Common\DataListing\DTO\OrderDto;
use App\Services\Common\DataListing\Enums\SortingType;
use App\Services\Common\DataListing\FieldCollector;
use App\Services\Common\DataListing\Fields\FieldAction;
use App\Services\Common\DataListing\Fields\FieldInt;
use App\Services\Common\DataListing\Fields\FieldList;
use App\Services\Common\DataListing\Fields\FieldString;
use Illuminate\Database\Eloquent\Model;

class ManageDomainsListing extends CoreListing
{
    /**
     * @return Model
     */
    protected function getModel(): Model
    {
        return new Domain();
    }

    /**
     * @return array|PermissionName[]
     */
    protected function getPermission(): array
    {
        return [PermissionName::ManageDomainRead];
    }

    /**
     * @return FieldCollector
     */
    protected function getFieldCollector(): FieldCollector
    {
        return FieldCollector::init()
            ->setBatch([
                FieldInt::init('id', 'domains.id', 'ID')
                    ->makeInvisible(),
                FieldString::init('domain', 'domains.domain', 'Домен'),
                FieldList::init('status', 'domains.status', 'Статус')
                    ->setListName('statuses')
                    ->makeInvisible(),
                FieldAction::init('actions', '', 'Статус'),
            ]);
    }

    /**
     * @return OrderDto
     */
    protected function defaultOrder(): OrderDto
    {
        return new OrderDto('domain', SortingType::Asc);
    }

    protected function getListItems(): array
    {
        return [
            'statuses' => DomainService::getStatusList(),
        ];
    }
}
