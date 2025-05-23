<?php

namespace App\Services\Client\User;

use App\Enums\Application\Status as ApplicationStatus;
use App\Enums\User\Status;
use App\Events\Client\UserStatusChanged;
use App\Models\User;
use App\Services\Client\Application\ApplicationService;
use App\Services\Client\User\DTO\UserCompanyOwnerTeamLeadDTO;
use App\Services\Common\BalanceTransaction\BalanceTransactionService;
use Illuminate\Database\Eloquent\Collection;

readonly class UserService
{
    /**
     * @param int $companyId
     */
    public function __construct(protected int $companyId)
    {
    }

    /**
     * @return Collection
     */
    public function getUsersForSelections(array $excluding = [], bool $activeOnly = false): Collection
    {
        $query = User::query()
            ->selectRaw('id as value, username as label')
            ->where('company_id', $this->companyId)
            ->whereNotIn('public_id', $excluding);

        if ($activeOnly) {
            $query->whereNot('status', Status::Deleted->value);
        }
        return $query->get();
    }

    /**
     * @return Collection
     */
    public function getTransactionUsersForSelections(): Collection
    {
        return User::query()
            ->selectRaw('DISTINCT users.id as value, users.name as label')
            ->join('balance_transactions', 'balance_transactions.user_id', '=', 'users.id')
            ->where('balance_transactions.company_id', $this->companyId)
            ->groupBy('users.id', 'users.name')
            ->get()
            ->prepend(['value' => 0, 'label' => 'System']);
    }

    /**
     * @param int $publicId
     * @param bool|null $public
     * @return User
     */
    public function getByPublicId(int $publicId, ?bool $public = true): User
    {
        $query = User::query();
        if ($public) {
            $query->select(['public_id', 'name']);
        }

        return $query
            ->where('company_id', $this->companyId)
            ->where('public_id', $publicId)
            ->firstOrFail();
    }

    public function getById(int $id, ?bool $public = true): User
    {
        $query = User::query();
        if ($public) {
            $query->select(['public_id', 'name']);
        }

        return $query
            ->where('id', $id)
            ->where('company_id', $this->companyId)
            ->firstOrFail();
    }

    public function getByTelegramId(int $telegramId): ?User
    {
        return User::query()
            ->where('company_id', $this->companyId)
            ->where('telegram_id', $telegramId)
            ->first();
    }

    public function getUsersCompanyOwnerAndTeamLead(int $id): UserCompanyOwnerTeamLeadDTO
    {
        $user = User::query()
            ->select('users.*', 'teams.team_lead_id', 'companies.owner_id as company_owner_id')
            ->join('companies', 'companies.id', '=', 'users.company_id')
            ->leftJoin('teams', 'teams.id', '=', 'users.team_id')
            ->where('users.id', $id)
            ->where('users.company_id', $this->companyId)
            ->firstOrFail();

        $teamLead = match ($user->toArray()['team_lead_id']) {
            null => null,
            $id => $user,
            default => $this->getById($user->getAttribute('team_lead_id'), false),
        };

        $companyOwner = $this->getById($user->getAttribute('company_owner_id'), false);

        return new UserCompanyOwnerTeamLeadDTO($user, $teamLead, $companyOwner);
    }

    public function getByTeamIds(array $teamId): Collection
    {
        return User::query()
            ->where('company_id', $this->companyId)
            ->whereIn('team_id', $teamId)
            ->get();
    }

    public function update(int $id, array $data): bool
    {
        return User::query()
            ->where('id', $id)
            ->firstOrFail()
            ->update($data);
    }

    public function getUsersActiveApplications(int $publicId): Collection
    {
        return User::query()
            ->select(
                'applications.id as applicationId',
                'applications.name',
                'applications.full_domain as domain',
                'applications.owner_id as applicationOwnerId'
            )
            ->join('applications', 'applications.owner_id', '=', 'users.id')
            ->where('users.company_id', $this->companyId)
            ->where('users.public_id', $publicId)
            ->where('applications.status', ApplicationStatus::Active->value)
            ->get();
    }

    public function isCompanyOwner(int $publicId): bool
    {
        $query = User::query()
            ->select('users.id as user_id', 'companies.owner_id')
            ->leftJoin('companies', 'companies.id', '=', 'users.company_id')
            ->where('company_id', $this->companyId)
            ->where('users.public_id', $publicId)
            ->first();

        return $query['user_id'] === $query['owner_id'];
    }

    public function canDeactivateUser(int $publicId, array $newPwaOwners = []): bool
    {
        $activeApplications = $this->getUsersActiveApplications($publicId);

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

    public function prepareToDeactivate(bool $status, int $publicId): array
    {
        $response = [
            'canUpdate' => true,
            'message' => __('user-actions.deactivate-confirm'),
            'isOwner' => false,
            'applications' => [],
            'users' => [],
        ];
        if ($status === true) {
            $response['canUpdate'] = true;
            $response['message'] = __('user-actions.activate-confirm');
            return $response;
        }

        if ($this->isCompanyOwner($publicId) && $status === false) {
            $response['canUpdate'] = false;
            $response['message'] = __('user-actions.cannot-deactivate-company-owner');
            $response['isOwner'] = true;
            return $response;
        }

        $usersActiveApplications = $this->getUsersActiveApplications($publicId);
        if (!$usersActiveApplications->isEmpty()) {
            $response['canUpdate'] = false;
            $response['message'] = __('user-actions.change-pwa-owner');
            $response['applications'] = $usersActiveApplications;
            $response['users'] = $this->getUsersForSelections(excluding: [$publicId], activeOnly: true);
            $response['isOwner'] = false;
            return $response;
        }

        return  $response;
    }

    public function deactivate(int $publicId, int $status, array $newPwaOwners, ApplicationService $pwaService): array
    {
        $user = $this->getByPublicId($publicId, false);

        switch ($status) {
            case Status::Deleted->value:
                $pwaService->updateApplicationsOwners($newPwaOwners);
                $message = __('user-actions.deactivated-success');
                break;
            case Status::Active->value:
                $message = __('user-actions.activated-success');
                break;
            default:
                $message = '';
        }
        $this->update($user->id, ['status' => $status]);
        event(new UserStatusChanged($user, $status));

        return [
            'message' => $message,
            'user' => $user,
        ];
    }
}
