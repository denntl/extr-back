<?php

namespace App\Services\Common\DataListing\Entities;

use App\Enums\Authorization\PermissionName;
use App\Models\Application;
use App\Services\Client\Application\ApplicationService;
use App\Services\Common\DataListing\CoreListing;
use App\Services\Common\DataListing\DTO\FilterDTO;
use App\Services\Common\DataListing\DTO\OrderDto;
use App\Services\Common\DataListing\Enums\FilterOperator;
use App\Services\Common\DataListing\Enums\SortingType;
use App\Services\Common\DataListing\FieldCollector;
use App\Services\Common\DataListing\Fields\FieldAction;
use App\Services\Common\DataListing\Fields\FieldDateTime;
use App\Services\Common\DataListing\Fields\FieldInt;
use App\Services\Common\DataListing\Fields\FieldList;
use App\Services\Common\DataListing\Fields\FieldString;
use App\Services\Manage\Company\CompanyService;
use App\Services\Manage\User\UserService;
use Illuminate\Database\Eloquent\Model;

class ManageApplicationsListing extends CoreListing
{
    protected const WITH_TRASHED = true;

    protected function getModel(): Model
    {
        return new Application();
    }

    /**
     * @return array|PermissionName[]
     */
    protected function getPermission(): array
    {
        return [PermissionName::ManageApplicationRead];
    }

    protected function getFieldCollector(): FieldCollector
    {
        return FieldCollector::init()
            ->setBatch([
                FieldInt::init('id', 'applications.id', 'ID'),
                FieldList::init('company_id', 'applications.company_id', 'Компания')
                    ->setListName('companies'),
                FieldString::init('name', 'applications.name', 'Название'),
                FieldString::init('full_domain', 'applications.full_domain', 'Домен'),
                FieldList::init('owner', 'users.username', 'Пользователь')
                    ->withScope('owner')
                    ->setFilterField('applications.owner_id')
                    ->setListName('users'),
                FieldList::init('geos', 'app_geos.geos', 'Гео')
                    ->setFilterField('application_geo_languages.geo')
                    ->setListName('geos')
                    ->setCustomStyles(['minWidth' => '200px'])->withScope('geo'),
                FieldList::init('status', 'applications.status', 'Статус')
                    ->setListName('statuses'),
                FieldDateTime::init('deleted', 'applications.deleted_at', 'Удален'),

                FieldAction::init('actions', '', 'Действия'),

                FieldString::init('app_uuid', 'applications.uuid')
                    ->makeInvisible()
                    ->makeUnSearchable()
                    ->makeUnSortable()
                    ->hideInFilterSelect(),
            ])->distinct();
    }

    protected function defaultOrder(): OrderDto
    {
        return new OrderDto('id', SortingType::Desc);
    }

    protected function defaultFilter(): array
    {
        return [
            "deleted" => (new FilterDTO('deleted', FilterOperator::Empty, null))->toArrayFront(),
        ];
    }

    /**
     * @throws \Exception
     */
    protected function predefinedFilters(): array
    {

        return [
        ];
    }

    protected function getListItems(): array
    {
        /** @var UserService $userService */
        $userService = app(UserService::class);
        /** @var CompanyService $companyService */
        $companyService = app(CompanyService::class);

        return [
            'users' => $userService->getUsersForSelections(),
            'statuses' => ApplicationService::getStatusList(),
            'geos' => ApplicationService::getGeoList(),
            'companies' => $companyService->getCompanyForSelections(),
        ];
    }
}
