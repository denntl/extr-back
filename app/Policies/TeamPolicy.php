<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;
use App\Services\Client\Company\CompanyService;
use App\Services\Client\Team\TeamService;

class TeamPolicy
{
    public function update(User $user, Team $team, TeamService $teamService)
    {
        /**
         * @var CompanyService $companyService
         */
        $companyService = app(CompanyService::class);
        $companyOwnerId = $companyService->get(false)->owner_id;
        if ($companyOwnerId === $user->id) {
            return true;
        }

        $team = $teamService->getByPublicId($team->id, false);
        if ($team->team_lead_id === $user->id) {
            return true;
        }

        return false;
    }
}
