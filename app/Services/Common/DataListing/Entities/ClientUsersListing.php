<?php

namespace App\Services\Common\DataListing\Entities;

use App\Enums\Authorization\PermissionName;
use App\Enums\User\Status;
use App\Models\User;
use App\Services\Client\Company\CompanyService;
use App\Services\Client\Team\TeamService;
use App\Services\Client\User\UserService;
use App\Services\Common\DataListing\CoreListing;
use App\Services\Common\DataListing\DTO\FilterDTO;
use App\Services\Common\DataListing\DTO\OrderDto;
use App\Services\Common\DataListing\Enums\FilterOperator;
use App\Services\Common\DataListing\Enums\SortingType;
use App\Services\Common\DataListing\FieldCollector;
use App\Services\Common\DataListing\Fields\FieldDate;
use App\Services\Common\DataListing\Fields\FieldInt;
use App\Services\Common\DataListing\Fields\FieldList;
use App\Services\Common\DataListing\Fields\FieldString;
use Illuminate\Database\Eloquent\Model;

class ClientUsersListing extends CoreListing
{
    /**
     * @return Model
     */
    protected function getModel(): Model
    {
        return new User();
    }

    /**
     * @return array|PermissionName[]
     */
    protected function getPermission(): array
    {
        return [PermissionName::ClientUserRead];
    }

    /**
     * @return FieldCollector
     */
    protected function getFieldCollector(): FieldCollector
    {
        return FieldCollector::init()
            ->setBatch([
                FieldInt::init('company_id', 'users.company_id', 'ID')->hideInSelect(),
                FieldInt::init('user_private_id', 'users.id', 'id')->hideInSelect(),
                FieldString::init('company_pid', 'companies.public_id', 'CID')
                    ->makeInvisible()
                    ->makeUnSearchable()
                    ->withScope('company'),
                FieldInt::init('id', 'teams.public_id', 'ID')->withScope('team'),
                FieldList::init('name', 'users.name', 'Имя'),
                FieldDate::init('created_at', 'teams.created_at', 'Создано'),
                FieldList::init('team_name', 'teams.name', 'Команда')
                    ->setFilterField('teams.public_id')
                    ->setListName('teams'),
                FieldString::init('username', 'users.username', 'Телеграмм'),
                FieldInt::init('user_id', 'users.public_id', 'UserId')
                    ->makeInvisible()
                    ->makeUnSearchable(),
                FieldInt::init('status', 'users.status', 'Статус')
                    ->setTransform(function ($e) {
                        return match ($e) {
                            Status::Active->value => true,
                            Status::Deleted->value => false,
                            default => false,
                        };
                    }),
            ]);
    }

    /**
     * @return OrderDto
     */
    protected function defaultOrder(): OrderDto
    {
        return new OrderDto('id, user_id', SortingType::Desc);
    }

    /**
     * @return array
     */
    protected function getListItems(): array
    {
        /** @var TeamService $teamService */
        $teamService = app(TeamService::class);
        return [
            'teams' => $teamService->getTeamsForSelections(),
        ];
    }

    protected function predefinedFilters(): array
    {
        /** @var TeamService $teamService */
        $teamService = app(TeamService::class);
        /** @var CompanyService $companyService */
        $companyService = app(CompanyService::class);
        /** @var UserService $userService */
        $userService = app(UserService::class);

        $userId = $this->listingModel->user->id;
        $companyOwnerId = $companyService->get(false)->owner_id;
        if ($companyOwnerId === $userId) {
            return [
                new FilterDTO('company_id', FilterOperator::Equal, $this->listingModel->user->company_id),
            ];
        }

        $managedTeams = $teamService->getByTeamLeadId($userId)->pluck('id')->toArray();
        $managedTeams[] = $this->listingModel->user->team_id;
        $usersInTeams = $userService->getByTeamIds($managedTeams)->pluck('id')->toArray();
        $usersInTeams[] = $companyOwnerId;
        return [
            new FilterDTO('company_id', FilterOperator::Equal, $this->listingModel->user->company_id),
            new FilterDTO('user_private_id', FilterOperator::In, $usersInTeams),
        ];
    }
}
