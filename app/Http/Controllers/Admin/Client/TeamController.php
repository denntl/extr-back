<?php

namespace App\Http\Controllers\Admin\Client;

use App\Enums\Invite\ActionName;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Client\Team\UpdateRequest;
use App\Services\Client\Company\CompanyService;
use App\Services\Client\Invite\DTO\ActionDTO;
use App\Services\Client\Invite\InviteService;
use App\Services\Client\Team\TeamService;
use App\Services\Client\User\UserService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\JsonResponse;
use Throwable;

class TeamController extends Controller
{
    public function getInvite($id, InviteService $inviteService, TeamService $teamService): JsonResponse
    {
        $team = $teamService->getByPublicId($id, false);

        $actionDTO = new ActionDTO(
            auth()->id(),
            ActionName::Registration,
            ['team_id' => $team->id]
        );
        $invite = $inviteService->generateInvite($actionDTO);

        return response()->json(['key' => $invite->key, 'expiredAt' => $invite->expireAt]);
    }

    /**
     * @param int $id
     * @param TeamService $teamService
     * @return JsonResponse
     */
    public function edit(int $id, TeamService $teamService): JsonResponse
    {
        $team = $teamService->getObjectForEdit($id);
        Gate::authorize('update', [$team, $teamService]);

        $teamMembers = $teamService->getMembersSelection($id);
        $team['members'] = $teamService->getMembersByPublicId($id, ['users.public_id'])->pluck('public_id')->all();

        return response()->json([
            'team' => $team,
            'teamLeaders' => $teamService->getTeamLeaderSelection(),
            'members' => $teamMembers,
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function create(): JsonResponse
    {
        /**
         * @var TeamService $teamService
         */
        $teamService = app(TeamService::class);

        return response()->json([
            'teamLeaders' =>  $teamService->getTeamLeaderSelection(),
            'members' => $teamService->getMembersSelection(),
        ]);
    }

    /**
     * @param int $publicId
     * @param UpdateRequest $request
     * @param TeamService $teamService
     * @return JsonResponse
     * @throws Throwable
     */
    public function update(int $publicId, UpdateRequest $request, TeamService $teamService): JsonResponse
    {
        $team = $teamService->getObjectForEdit($publicId);
        Gate::authorize('update', [$team, $teamService]);
        $teamService->update($publicId, $request->validated());

        return response()->json([
            'isUpdated' => true,
            'message' => 'Команда успешно обновлена',
        ]);
    }

    /**
     * @param UpdateRequest $request
     * @param TeamService $teamService
     * @param UserService $userService
     * @return JsonResponse
     * @throws Throwable
     */
    public function store(UpdateRequest $request, TeamService $teamService, UserService $userService): JsonResponse
    {
        $teamService->store($request->validated());

        return response()->json([
            'isUpdated' => true,
            'message' => 'Команда успешно создана',
        ]);
    }

    /**
     * @param int $publicId
     * @param TeamService $teamService
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy(int $publicId, TeamService $teamService): JsonResponse
    {
        try {
            $teamService->destroy($publicId);
        } catch (Throwable $e) {
            return response()->json([
                'message' => __('team.error.delete'),
                'errors' => [
                    'team' => [
                        __('team.error.delete'),
                    ]
                ]
            ], 422);
        }

        return response()->json([
            'isDeleted' => true
        ]);
    }
}
