<?php

namespace App\Services\Common\DataListing\Entities;

use App\Enums\Authorization\PermissionName;
use App\Models\Team;
use App\Services\Client\Company\CompanyService;
use App\Services\Client\Team\TeamService;
use App\Services\Common\DataListing\CoreListing;
use App\Services\Common\DataListing\DTO\FilterDTO;
use App\Services\Common\DataListing\DTO\OrderDto;
use App\Services\Common\DataListing\Enums\FilterOperator;
use App\Services\Common\DataListing\Enums\SortingType;
use App\Services\Common\DataListing\FieldCollector;
use App\Services\Common\DataListing\Fields\FieldAction;
use App\Services\Common\DataListing\Fields\FieldDate;
use App\Services\Common\DataListing\Fields\FieldInt;
use App\Services\Common\DataListing\Fields\FieldList;
use App\Services\Common\DataListing\Fields\FieldString;
use App\Services\Client\User\UserService;
use Illuminate\Database\Eloquent\Model;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class ClientTeamsListing extends CoreListing
{
    /**
     * @return Model
     */
    protected function getModel(): Model
    {
        return new Team();
    }

    /**
     * @return array|PermissionName[]
     */
    protected function getPermission(): array
    {
        return [PermissionName::ClientTeamRead];
    }

    /**
     * @return FieldCollector
     */
    protected function getFieldCollector(): FieldCollector
    {
        return FieldCollector::init()
            ->setBatch([
                FieldInt::init('company_id', 'teams.company_id', 'Company Id')->hideInSelect(),
                FieldInt::init('team_id', 'teams.id', 'id')->hideInSelect(),
                FieldInt::init('id', 'teams.public_id', 'ID'),
                FieldDate::init('created_at', 'teams.created_at', 'Создано'),
                FieldDate::init('updated_at', 'teams.updated_at', 'Обновлено'),
                FieldString::init('name', 'teams.name', 'Команда'),
                FieldList::init('username', 'users.username', 'Руководитель')
                    ->withScope('teamLead')
                    ->setFilterField('teams.team_lead_id')
                    ->setListName('users'),
                FieldInt::init('user_count', 'uc.count', 'Участники')->withScope('userCount'),
                FieldAction::init('actions', '', 'Действия'),
            ]);
    }

    /**
     * @return OrderDto
     */
    protected function defaultOrder(): OrderDto
    {
        return new OrderDto('id', SortingType::Desc);
    }

    /**
     * @return array
     */
    protected function getListItems(): array
    {
        /** @var UserService $userService */
        $userService = app(UserService::class);
        return [
            'users' => $userService->getUsersForSelections(),
        ];
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \Exception
     */
    protected function predefinedFilters(): array
    {
        /** @var TeamService $teamService */
        $teamService = app(TeamService::class);
        /** @var CompanyService $companyService */
        $companyService = app(CompanyService::class);

        $userId = $this->listingModel->user->id;
        $companyOwnerId = $companyService->get(false)->owner_id;
        if ($companyOwnerId === $userId) {
            return [
                new FilterDTO('company_id', FilterOperator::Equal, $this->listingModel->user->company_id),
            ];
        }

        $managedTeams = $teamService->getByTeamLeadId($userId)->pluck('id')->toArray();
        if (count($managedTeams) > 0) {
            $managedTeams[] = $this->listingModel->user->team_id;
            return [
                new FilterDTO('company_id', FilterOperator::Equal, $this->listingModel->user->company_id),
                new FilterDTO('team_id', FilterOperator::In, $managedTeams),
            ];
        }

        return [
            new FilterDTO('company_id', FilterOperator::Equal, $this->listingModel->user->company_id),
            new FilterDTO('team_id', FilterOperator::Equal, $this->listingModel->user->team_id),
        ];
    }
}
