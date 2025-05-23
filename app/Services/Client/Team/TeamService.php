<?php

namespace App\Services\Client\Team;

use App\Enums\Authorization\RoleName;
use App\Services\Client\User\UserService;
use Illuminate\Support\Facades\Log;
use Throwable;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

readonly class TeamService
{
    /**
     * @param int $companyId
     */
    public function __construct(private int $companyId)
    {
    }

    /**
     * @param int $id
     * @param bool|null $public
     * @return Team
     */
    public function getByPublicId(int $id, ?bool $public = true): Team
    {
        $query = Team::query();

        if ($public) {
            $query->select(['public_id', 'name']);
        }

        return $query->where('company_id', $this->companyId)
            ->where('public_id', $id)
            ->firstOrFail();
    }

    public function getByTeamLeadId(int $userId): Collection
    {
        return Team::query()
            ->where('company_id', $this->companyId)
            ->where('team_lead_id', $userId)
            ->get();
    }

    /**
     * @param int $id
     * @return Team
     */
    public function getObjectForEdit(int $id): Team
    {
        return Team::query()
            ->select(['teams.public_id as id', 'teams.name', 'users.public_id as teamLeadId'])
            ->leftJoin('users', 'users.id', '=', 'teams.team_lead_id')
            ->where('teams.company_id', $this->companyId)
            ->where('teams.public_id', $id)
            ->firstOrFail();
    }

    /**
     * @param array $data
     * @return Team
     */
    public function create(array $data): Team
    {
        $data['company_id'] = $this->companyId;

        return Team::create($data);
    }

    /**
     * @param int $publicId
     * @param array $data
     * @return Team
     * @throws Throwable
     */
    public function update(int $publicId, array $data): Team
    {
        /** @var UserService $userService */
        $userService = app(UserService::class);

        if (!empty($data['team_lead_id'])) {
            $newTeamLead = $userService
                ->getByPublicId($data['team_lead_id'], false);
            $data['team_lead_id'] = $newTeamLead
                ->only('id')['id'];
        } else {
            $data['team_lead_id'] = null;
        }

        DB::beginTransaction();
        try {
            $this->updateMembersByPublicId($publicId, $data['members']);
            $team = Team::where('public_id', $publicId)
                ->where('company_id', $this->companyId)
                ->firstOrFail();

            if (
                !empty($newTeamLead) &&
                $newTeamLead->getAttribute('id') !== $team->getAttribute('team_lead_id')
            ) {
                $this->removeRoleOnUpdateOrDelete($team);
                $newTeamLead->assignRole(RoleName::TeamLead);
            }
            $team->update($data);

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return $team;
    }

    private function removeRoleOnUpdateOrDelete(Team $team): void
    {
        /** @var UserService $userService */
        $userService = app(UserService::class);
        $currentTeamLeadId = $team->getAttribute('team_lead_id');
        $teamId = $team->getAttribute('id');
        if (empty($currentTeamLeadId)) {
            return;
        }

        $allTeams = $this
            ->getByTeamLeadId($currentTeamLeadId)
            ->filter(function ($item) use ($teamId) {
                return $item['id'] !== $teamId;
            })
            ->count();
        if ($allTeams > 0) {
            return;
        }

        $currentTeamLead = $userService->getById($currentTeamLeadId, false);
        $currentTeamLead->removeRole(RoleName::TeamLead);
    }

    /**
     * @param array $data
     * @return Team
     * @throws Throwable
     */
    public function store(array $data): Team
    {
        /** @var UserService $userService */
        $userService = app(UserService::class);

        if (!empty($data['team_lead_id'])) {
            $teamLead = $userService
                ->getByPublicId($data['team_lead_id'], false);
            $data['team_lead_id'] = $teamLead
                ->only('id')['id'];
        } else {
            $data['team_lead_id'] = null;
        }

        DB::beginTransaction();
        try {
            $newTeam = $this->create($data);
            if (!empty($data['members'])) {
                $this->updateMembersByPublicId($newTeam->public_id, $data['members']);
            }
            if (!empty($teamLead)) {
                $teamLead->assignRole(RoleName::TeamLead);
            }

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return $newTeam;
    }

    /**
     * @param int $publicId
     * @return void
     * @throws Throwable
     */
    public function destroy(int $publicId): void
    {
        DB::beginTransaction();
        try {
            $team = $this->getByPublicId($publicId, false);
            User::where('team_id', $team->id)
                ->update(['team_id' => null]);
            $this->removeRoleOnUpdateOrDelete($team);
            $team->delete();

            DB::commit();
        } catch (Throwable $e) {
            Log::error('Failed to destroy team by client', [
                'message' => $e->getMessage(),
                'trace' => $e->getTrace(),
            ]);
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @param int $publicId
     * @param array $members
     * @return void
     * @throws Throwable
     */
    public function updateMembersByPublicId(int $publicId, array $members): void
    {
        $teamId = $this->getByPublicId($publicId, false)->id;

        DB::beginTransaction();
        try {
            User::query()
                ->whereNotIn('public_id', $members)
                ->where('company_id', $this->companyId)
                ->where('team_id', $teamId)
                ->update(['team_id' => DB::raw('NULL')]);
            User::query()
                ->whereIn('public_id', $members)
                ->where('company_id', $this->companyId)
                ->where(function ($query) use ($teamId) {
                    $query->where('team_id', '!=', $teamId)
                        ->orWhereNull('team_id');
                })
            ->update(['team_id' => $teamId]);
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @return Collection
     */
    public function getTeamsForSelections(): Collection
    {
        return Team::query()
            ->selectRaw('public_id as value, name as label')
            ->where('company_id', $this->companyId)
            ->get();
    }

    /**
     * @return Collection
     */
    public function getTeamLeaderSelection(): Collection
    {
        return User::query()
            ->selectRaw('public_id as value, username as label')
            ->where('company_id', $this->companyId)
            ->get();
    }

    /**
     * @param int|null $publicId
     * @return Collection
     */
    public function getMembersSelection(int $publicId = null): Collection
    {
        $query = User::query()
            ->selectRaw('users.public_id as value, username as label')
            ->where('users.company_id', $this->companyId);

        if ($publicId) {
            $query->leftJoin('teams', 'teams.id', '=', 'users.team_id')
                ->where(function ($query) use ($publicId) {
                    $query->where('teams.public_id', $publicId)->orWhereNull('team_id');
                });
        } else {
            $query->whereNull('team_id');
        }

        return $query->get();
    }

    /**
     * @param int $publicId
     * @param array $columns
     * @return Collection
     */
    public function getMembersByPublicId(int $publicId, array $columns = ['*']): Collection
    {
        return User::query()
            ->leftJoin('teams', 'teams.id', '=', 'users.team_id')
            ->where('teams.public_id', $publicId)
            ->where('users.company_id', $this->companyId)
            ->get($columns);
    }
}
