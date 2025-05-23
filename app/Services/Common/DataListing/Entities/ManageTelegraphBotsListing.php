<?php

namespace App\Services\Common\DataListing\Entities;

use App\Enums\Authorization\PermissionName;
use App\Models\TelegraphBot;
use App\Services\Common\DataListing\CoreListing;
use App\Services\Common\DataListing\DTO\OrderDto;
use App\Services\Common\DataListing\Enums\SortingType;
use App\Services\Common\DataListing\FieldCollector;
use App\Services\Common\DataListing\Fields\FieldAction;
use App\Services\Common\DataListing\Fields\FieldInt;
use App\Services\Common\DataListing\Fields\FieldList;
use App\Services\Common\DataListing\Fields\FieldString;
use App\Services\Manage\Company\CompanyService;
use Illuminate\Database\Eloquent\Model;

class ManageTelegraphBotsListing extends CoreListing
{
    protected function getModel(): Model
    {
        return new TelegraphBot();
    }

    /**
     * @return array|PermissionName[]
     */
    protected function getPermission(): array
    {
        return [PermissionName::ManageTelegraphBotRead];
    }

    protected function getFieldCollector(): FieldCollector
    {
        return FieldCollector::init()
            ->setBatch([
                FieldInt::init('id', 'telegraph_bots.id', 'ID')->makeInvisible(),
                FieldString::init('name', 'telegraph_bots.name', 'Название'),
                FieldString::init('is_active', 'telegraph_bots.is_active')->makeInvisible(),
                FieldList::init('company', 'companies.name', 'Компания')
                    ->withScope('company')
                    ->setFilterField('telegraph_bots.company_id')
                    ->setListName('companies'),
                FieldAction::init('actions', '', 'Статус'),
            ]);
    }

    protected function defaultOrder(): OrderDto
    {
        return new OrderDto('id', SortingType::Asc);
    }

    protected function getListItems(): array
    {
        /** @var CompanyService $companyService */
        $companyService = app(CompanyService::class);
        return [
            'companies' => $companyService->getCompanyForSelections(),
        ];
    }
}
