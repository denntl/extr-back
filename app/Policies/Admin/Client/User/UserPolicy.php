<?php

namespace App\Policies\Admin\Client\User;

use App\Models\Team;
use App\Models\User;
use App\Services\Client\Application\ApplicationService;
use App\Services\Client\Company\CompanyService;
use App\Services\Client\Team\TeamService;
use App\Services\Client\User\UserService;

class UserPolicy
{
    public function deactivate(User $user, UserService $userService, int $userToUpdatePublicId)
    {

        if ($userService->isCompanyOwner($user->public_id)) {
            return true;
        }

        /** @var TeamService $teamService */
        $teamService = app(TeamService::class);
        $managedTeams = $teamService->getByTeamLeadId($user->id)->pluck('id')->toArray();
        $userToDeactivate = $userService->getByPublicId($userToUpdatePublicId, false);
        if (in_array($userToDeactivate->team_id, $managedTeams)) {
            return true;
        }

        return false;
    }
}
