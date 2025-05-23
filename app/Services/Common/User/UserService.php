<?php

namespace App\Services\Common\User;

use App\Models\User;
use App\Services\Client\User\DTO\UserCompanyOwnerTeamLeadDTO;
use Illuminate\Database\Eloquent\Collection;

readonly class UserService
{
    public function getById(int $id, ?bool $public = true): User
    {
        $query = User::query();
        if ($public) {
            $query->select(['public_id', 'name']);
        }

        return $query
            ->where('id', $id)
            ->firstOrFail();
    }

    public function getUsersCompanyOwnerAndTeamLead(int $id): UserCompanyOwnerTeamLeadDTO
    {
        $user = User::query()
            ->select('users.*', 'teams.team_lead_id', 'companies.owner_id as company_owner_id')
            ->join('companies', 'companies.id', '=', 'users.company_id')
            ->leftJoin('teams', 'teams.id', '=', 'users.team_id')
            ->where('users.id', $id)
            ->firstOrFail();

        $teamLead = match ($user->toArray()['team_lead_id']) {
            null => null,
            $id => $user,
            default => $this->getById($user->getAttribute('team_lead_id'), false),
        };

        $companyOwner = $this->getById($user->getAttribute('company_owner_id'), false);

        return new UserCompanyOwnerTeamLeadDTO($user, $teamLead, $companyOwner);
    }
}
