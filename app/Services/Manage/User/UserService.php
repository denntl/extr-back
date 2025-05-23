<?php

namespace App\Services\Manage\User;

use App\Enums\Application\Status as ApplicationStatus;
use App\Enums\User\Status;
use App\Events\Client\UserStatusChanged;
use App\Models\Application;
use App\Models\User;
use App\Services\Client\User\DTO\UserCompanyOwnerTeamLeadDTO;
use App\Services\Common\Role\RoleService;
use App\Services\Manage\Application\ApplicationService;
use App\Services\Manage\User\DTO\NewApplicationOwnersDTO;
use Illuminate\Database\Eloquent\Collection;

readonly class UserService
{
    public function getStatusesForSelections(): array
    {
        return [
            [
                'label' => 'Включен',
                'value' => Status::Active->value,
            ],
            [
                'label' => 'Выключен',
                'value' => Status::Deleted->value,
            ],
            [
                'label' => 'Новая Регистрация',
                'value' => Status::NewReg->value,
            ],
        ];
    }

    /**
     * @return array[]
     */
    public function getEmployeeForSelections(): array
    {
        return [
            [
                'label' => 'Да',
                'value' => true,
            ],
            [
                'label' => 'Нет',
                'value' => false,
            ],
        ];
    }

    /**
     * @param bool $includeSystemUser
     * @return Collection
     */
    public function getUsersForSelections(bool $includeSystemUser = false): Collection
    {
        $users = User::query()
            ->selectRaw('id as value, username as label')
            ->get();
        if ($includeSystemUser) {
            $users->prepend(['value' => 0, 'label' => 'System']);
        }

        return $users;
    }

    public function getUsersInCompanyForSelections(int $companyId, array $excludingIds = [], bool $activeOnly = false): Collection
    {
        $query = User::query()
            ->selectRaw('users.id as value, username as label')
            ->where('users.company_id', $companyId)
            ->whereNotIn('users.id', $excludingIds);

        if ($activeOnly) {
            $query->whereNot('status', Status::Deleted->value);
        }
        return $query->get();
    }

    /**
     * @param int $id
     * @param bool|null $public
     * @return User
     */
    public function getById(int $id, ?bool $public = true): User
    {
        $query = User::query();
        if ($public) {
            $query->select(['public_id', 'name']);
        }

        return $query->where('users.id', $id)->firstOrFail();
    }

    /**
     * @param int $publicId
     * @return User
     */
    public function getObjectForEdit(int $publicId): User
    {
        return User::query()
            ->select(['users.name', 'users.username', 'email', 'users.status', 'is_employee', 'companies.name as companyName', 'companies.id as companyId'])
            ->leftJoin('companies', 'companies.id', '=', 'users.company_id')
            ->where('users.id', $publicId)
            ->firstOrFail();
    }

    /**
     * @param int $id
     * @return \Illuminate\Support\Collection
     */
    public function getRoles(int $id): \Illuminate\Support\Collection
    {
        return $this->getById($id, false)->roles->map(function ($role) {
            return $role->id;
        });
    }

    /**
     * @param int $id
     * @return Collection
     */
    public function getActiveApplications(int $id): Collection
    {
        return Application::query()
            ->select(
                'applications.id as applicationId',
                'applications.name',
                'applications.full_domain as domain',
                'applications.owner_id as applicationOwnerId'
            )
            ->where('applications.owner_id', $id)
            ->where('applications.status', ApplicationStatus::Active->value)
            ->get();
    }

    /**
     * @param int $id
     * @param array $roles
     * @return void
     */
    public function updateRoles(int $id, array $roles): void
    {
        /** @var RoleService $roleService */
        $roleService = app(RoleService::class);
        $this->getById($id, false)->syncRoles(
            $roleService->getRolesById($roles)->pluck('name')
        );
    }

    /**
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $user = $this->getById($id, false);

        $updated = User::query()
            ->where('id', $id)
            ->firstOrFail()
            ->update($data);

        if (
            $updated
            && $data['status'] !== $user->status
        ) {
            event(new UserStatusChanged($user, $data['status']));
        }

        return $updated;
    }

    public function canDeactivateUser(int $userId, array $newPwaOwners = []): bool
    {
        $activeApplications = $this->getActiveApplications($userId);

        if (
            !$activeApplications->isEmpty()
            && count($newPwaOwners) !== count($activeApplications)
        ) {
            return false;
        }

        foreach ($newPwaOwners as $newOwner) {
            if (empty($newOwner['applicationId']) || empty($newOwner['userId'])) {
                return false;
            }

            foreach ($activeApplications as $key => $activeApplication) {
                if (
                    $newOwner['applicationId'] === $activeApplication['applicationId']
                    && $newOwner['userId'] !== $activeApplication['applicationOwnerId']
                ) {
                    unset($activeApplications[$key]);
                }
            }
        }

        if (count($activeApplications) !== 0) {
            return false;
        }

        return true;
    }
    public function changeStatus(int $id, int $status, array $newAppsOwners): array
    {
        /**
         * @var ApplicationService $applicationService
         */
        $applicationService = app(ApplicationService::class);
        $updated = true;
        switch ($status) {
            case Status::Deleted->value:
                $newAppsOwners = array_map(fn($item) => NewApplicationOwnersDTO::fromArray($item), $newAppsOwners);
                $updated = $applicationService->updateApplicationsOwners($newAppsOwners);
                $message = $updated ? __('user-actions.deactivated-success') : __('user-actions.deactivation-error');
                break;
            case Status::Active->value:
                $message = __('user-actions.activated-success');
                break;
            default:
                $message = '';
        }
        if ($updated) {
            $this->update($id, ['status' => $status]);
        }

        return [
            'isUpdated' => $updated,
            'message' => $message
        ];
    }
}
