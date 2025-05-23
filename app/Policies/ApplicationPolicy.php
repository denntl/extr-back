<?php

namespace App\Policies;

use App\Enums\Authorization\PermissionName;
use App\Models\Application;
use App\Models\User;
use App\Services\Client\Application\ApplicationService;
use App\Services\Client\Team\TeamService;
use App\Services\Client\User\UserService;

class ApplicationPolicy
{
    public function manage(User $user, Application $application): true
    {
        if ($user->company_id === $application->company_id) {
            return true;
        }

        abort(403);
    }

    public function updateOwner(User $user, Application $application): bool
    {
        return $user->hasAnyPermission([PermissionName::ClientApplicationOwnerUpdate->value, PermissionName::ManageApplicationSave]);
    }

    public function update(User $user, Application $application): bool
    {
        if ($user->company_id !== $application->company_id) {
            return false;
        }

        /** @var UserService $userService */
        $userService = app(UserService::class);
        if ($userService->isCompanyOwner($user->public_id)) {
            return true;
        }

        if ($application->owner_id === $user->id) {
            return true;
        }

        /** @var ApplicationService $applicationService */
        $applicationService = app(ApplicationService::class);
        $appOwnerIds = $applicationService->getTeamsOrOwnApplications($user->id)
            ->keyBy('owner_id')
            ->toArray();
        if (isset($appOwnerIds[$application->owner_id])) {
            return true;
        }

        return false;
    }

    public function readDetailedStatistics(User $user, Application $application): bool
    {
        if ($user->company_id !== $application->company_id) {
            return false;
        }

        /** @var UserService $userService */
        $userService = app(UserService::class);
        if ($userService->isCompanyOwner($user->public_id)) {
            return true;
        }

        if ($application->owner_id === $user->id) {
            return true;
        }

        /** @var ApplicationService $applicationService */
        $applicationService = app(ApplicationService::class);
        $appOwnerIds = $applicationService->getTeamsOrOwnApplications($user->id)
            ->keyBy('owner_id')
            ->toArray();
        if (isset($appOwnerIds[$application->owner_id])) {
            return true;
        }

        return false;
    }
}
