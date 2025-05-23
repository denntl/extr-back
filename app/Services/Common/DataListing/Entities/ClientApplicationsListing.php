<?php

namespace App\Services\Common\DataListing\Entities;

use App\Enums\Authorization\PermissionName;
use App\Models\Application;
use App\Services\Client\Application\ApplicationService;
use App\Services\Client\Company\CompanyService;
use App\Services\Client\User\UserService;
use App\Services\Common\DataListing\CoreListing;
use App\Services\Common\DataListing\DTO\FilterDTO;
use App\Services\Common\DataListing\DTO\OrderDto;
use App\Services\Common\DataListing\Enums\FilterOperator;
use App\Services\Common\DataListing\Enums\SortingType;
use App\Services\Common\DataListing\FieldCollector;
use App\Services\Common\DataListing\Fields\FieldAction;
use App\Services\Common\DataListing\Fields\FieldInt;
use App\Services\Common\DataListing\Fields\FieldList;
use App\Services\Common\DataListing\Fields\FieldString;
use Illuminate\Database\Eloquent\Model;

class ClientApplicationsListing extends CoreListing
{
    protected function getModel(): Model
    {
        return new Application();
    }

    /**
     * @return array|PermissionName[]
     */
    protected function getPermission(): array
    {
        return [PermissionName::ClientApplicationRead];
    }

    protected function getFieldCollector(): FieldCollector
    {
        return FieldCollector::init()
            ->setBatch([
                FieldInt::init('id', 'applications.public_id', 'ID'),
                FieldInt::init('company_id', 'applications.company_id')->hideInSelect(),
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

                // Scope statistic is used to get statistic of application from application_statistics table.
                FieldInt::init('clicks', 'app_stats.click_sum', 'Клики')->withScope('statistic'),
                FieldInt::init('unique_clicks', 'app_stats.unique_click_sum', 'Уникальные'),
                FieldInt::init('installs', 'app_stats.install_sum', 'Установки'),
                FieldInt::init('registrations', 'app_stats.registration_sum', 'Реги'),
                FieldInt::init('deposits', 'app_stats.deposit_sum', 'Депозиты'),

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

    protected function getListItems(): array
    {
        /** @var UserService $userService */
        $userService = app(UserService::class);

        return [
            'users' => $userService->getUsersForSelections(),
            'statuses' => ApplicationService::getStatusList(),
            'geos' => ApplicationService::getGeoList(),
        ];
    }

    /**
     * @throws \Exception
     */
    protected function predefinedFilters(): array
    {
        /** @var CompanyService $companyService */
        $companyService = app(CompanyService::class);
        /** @var ApplicationService $applicationService */
        $applicationService = app(ApplicationService::class);

        $userId = $this->listingModel->user->id;
        $companyOwnerId = $companyService->get(false)->owner_id;
        if ($companyOwnerId === $userId) {
            return [
                new FilterDTO('company_id', FilterOperator::Equal, $this->listingModel->user->company_id),
            ];
        }

        $applications = $applicationService->getTeamsOrOwnApplications($this->listingModel->user->id)->pluck('public_id')->toArray();
        return [
            new FilterDTO('company_id', FilterOperator::Equal, $this->listingModel->user->company_id),
            new FilterDTO('id', FilterOperator::In, $applications),
        ];
    }
}
