<?php

namespace App\Http\Controllers\Admin\Common;

use App\Enums\User\Status;
use App\Events\Client\UserWasAuthenticated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Common\Auth\LoginRequest;
use App\Http\Requests\Admin\Common\Auth\RegisterRequest;
use App\Models\User;
use App\Services\Client\Application\ApplicationService;
use App\Services\Common\Auth\AuthService;
use App\Services\Common\Auth\Exceptions\UserIsDeactivatedException;
use App\Services\Common\Auth\PermissionService;
use App\Services\Common\Company\Exceptions\UserNotBelongsToCompanyException;
use App\Services\Common\Invite\Exceptions\InviteIsNotFoundException;
use App\Services\Common\Invite\InviteService;
use App\Services\Manage\User\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * @param RegisterRequest $request
     * @param AuthService $authService
     * @return JsonResponse
     */
    public function register(RegisterRequest $request, AuthService $authService): JsonResponse
    {
        try {
            $registeredUser = $authService->registerWithCompany($request->validated());
        } catch (UserNotBelongsToCompanyException $e) {
            return response()->json(['message' => __('user_not_belongs_to_company')], 403);
        }

        return response()->json($authService->getAuthentication($registeredUser));
    }

    /**
     * @param LoginRequest $request
     * @return JsonResponse
     * @throws UserIsDeactivatedException
     */
    public function login(LoginRequest $request): JsonResponse
    {
        if (Auth::attempt($request->validated())) {
            /** @var User $user */
            $user = Auth::user();
            if ($user->status === Status::Deleted->value) {
                throw new UserIsDeactivatedException();
            }
            $token = $user->createToken('auth_token')->plainTextToken;

            $permissionService = app(PermissionService::class);

            event(new UserWasAuthenticated($user));
            return response()->json([
                'token' => $token,
                'access' => $permissionService->getAccess()->toArray(),
            ]);
        }

        return response()->json([
            'message' => __('common.auth.invalid_credentials'),
            'errors' => [
                'common' => [
                    __('common.auth.invalid_credentials'),
                ]
            ]
        ], 422);
    }

    /**
     * @param $id
     * @param UserService $userService
     * @return JsonResponse
     */
    public function loginAsUser($id, UserService $userService): JsonResponse
    {
        $user = $userService->getById($id, false);
        if (Auth::guard()->setUser($user)) {
            $token = Auth::user()->createToken('auth_token')->plainTextToken;
            $permissionService = app(PermissionService::class);
            return response()->json([
                'token' => $token,
                'access' => $permissionService->getAccess()->toArray(),
                'user' => Auth::user()->only(['username'])
            ]);
        }

        return response()->json([
            'message' => __('common.auth.unauthenticated'),
            'errors' => [
                'common' => [
                    __('common.auth.unauthenticated'),
                ]
            ]
        ], 422);
    }

    /**
     * @param PermissionService $permissionService
     * @param ApplicationService $applicationService
     * @return JsonResponse
     */
    public function user(PermissionService $permissionService, ApplicationService $applicationService): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => __('common.auth.unauthenticated')], 401);
        }

        return response()->json([
            'user' => $user->only(['public_id', 'username', 'email']),
            'params' => [
                'balance' => $user->company->balances->balance,
                'balance_bonus' => $user->company->balances->balance_bonus,
                'tariff_type_id' => $user->company->tariff->type_id,
                'companyName' => $user->company->name,
                'hasApplications' => $applicationService->getTeamsOrOwnApplications($user->getAttribute('id'))->count() > 0,
                'companyId' => $user->company->public_id
            ],
            'access' => $permissionService->getAccess()->toArray(),
        ]);
    }

    /**
     * @param string $key
     * @param InviteService $inviteService
     * @return JsonResponse
     */
    public function invite(string $key, InviteService $inviteService): JsonResponse
    {
        try {
            $invite = $inviteService->getByKey($key);
        } catch (InviteIsNotFoundException $e) {
            return response()->json(['message' => 'Invite not found'], 404);
        }

        return response()->json($invite->toArray());
    }
}
