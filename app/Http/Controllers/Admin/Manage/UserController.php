<?php

namespace App\Http\Controllers\Admin\Manage;

use App\Enums\User\Status;
use App\Events\Client\UserStatusChanged;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Manage\User\ChangeStatusRequest;
use App\Http\Requests\Admin\Manage\User\UpdateRequest;
use App\Services\Common\Role\RoleService;
use App\Services\Manage\User\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

class UserController extends Controller
{
    /**
     * @param int $id
     * @param UserService $userService
     * @param RoleService $roleService
     * @return JsonResponse
     */
    public function edit(int $id, UserService $userService, RoleService $roleService): JsonResponse
    {
        $userInfo = $userService->getObjectForEdit($id);
        return response()->json([
            'user' => $userInfo,
            'roles' => $userService->getRoles($id),
            'rolesList' => $roleService->getRolesList(),
            'statusList' => $userService->getStatusesForSelections(),
            'activeApplications' => $userService->getActiveApplications($id),
            'companyUsers' => $userService->getUsersInCompanyForSelections($userInfo['companyId'], [$id], true)
        ]);
    }

    /**
     * @param int $id
     * @param UpdateRequest $request
     * @param UserService $userService
     * @return JsonResponse
     * @throws Throwable
     */
    public function update(int $id, UpdateRequest $request, UserService $userService): JsonResponse
    {
        $data = $request->validated();
        if (! empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        DB::beginTransaction();
        try {
            $userService->update($id, $data);
            $userService->updateRoles($id, $request->input('roles', []));
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return response()->json([
            'isUpdated' => true,
            'message' => 'Пользователь успешно обновлен',
        ]);
    }

    public function changeStatus(int $id, ChangeStatusRequest $request, UserService $userService): JsonResponse
    {
        $request = $request->validated();
        $deactivated = $userService->changeStatus($id, $request['status'], $request['newApplicationsOwners']);

        return response()->json($deactivated);
    }
}
