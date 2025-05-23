<?php

namespace App\Http\Controllers\Admin\Client;

use App\Enums\User\Status;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Client\User\CanDeactivateRequest;
use App\Http\Requests\Admin\Client\User\DeactivateRequest;
use App\Models\User;
use App\Services\Client\Application\ApplicationService;
use App\Services\Client\User\UserService as ClientUserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    public function canChangeStatus(int $userId, CanDeactivateRequest $request, ClientUserService $userService): JsonResponse
    {
        Gate::authorize('deactivate', [User::class, $userService, $userId]);

        $request = $request->validated();

        $resp = $userService->prepareToDeactivate($request['status'], $userId);
        return response()->json($resp);
    }

    public function changeStatus(
        int $userId,
        DeactivateRequest $request,
        ClientUserService $userService,
        ApplicationService $pwaService
    ): JsonResponse {
        Gate::authorize('deactivate', [User::class, $userService, $userId]);

        $request = $request->validated();
        $status = $request['status'] ? Status::Active->value : Status::Deleted->value;

        $deactivate = $userService->deactivate($userId, $status, $request['newApplicationsOwners'], $pwaService);

        return response()->json([
            'isUpdated' => true,
            'message' => $deactivate['message'],
        ]);
    }
}
