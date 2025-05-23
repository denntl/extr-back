<?php

namespace App\Http\Controllers\Admin\Manage;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Manage\Role\CreateRequest;
use App\Http\Requests\Admin\Manage\Role\DestroyRequest;
use App\Http\Requests\Admin\Manage\Role\UpdateRequest;
use App\Services\Common\Auth\PermissionService;
use App\Services\Manage\Role\RoleService;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    public function edit(int $id, RoleService $roleService, PermissionService $permissionService): JsonResponse
    {
        $role = $roleService->getRoleById($id);
        return response()->json([
            'role' => $role->only(['id', 'name', 'is_predefined']),
            'permissions' => $permissionService->getPermissionsForSelections(),
            'permissionIds' => $role->permissions->pluck('id')->toArray(),
        ]);
    }

    public function update(int $id, UpdateRequest $request, RoleService $roleService): JsonResponse
    {
        $roleService->update($id, $request->validated());

        return response()->json([
            'isUpdated' => true,
            'message' => 'Роль успешно обновлена',
        ]);
    }

    public function create(PermissionService $permissionService): JsonResponse
    {
        return response()->json([
            'permissions' => $permissionService->getPermissionsForSelections(),
        ]);
    }

    public function store(CreateRequest $request, RoleService $roleService): JsonResponse
    {
        $roleService->store($request->validated());

        return response()->json([
            'isCreated' => true,
            'message' => 'Роль успешно создана',
        ]);
    }

    public function destroy(int $id, DestroyRequest $request, RoleService $roleService): JsonResponse
    {
        $validatedUsers = $request->validated()['users'];

        foreach ($validatedUsers as $user) {
            $roleService->syncUserRoles($user['userId'], $user['roleIds']);
        }
        $roleService->destroy($id);

        return response()->json([
            'isDeleted' => true,
        ]);
    }

    public function delete(int $id, RoleService $roleService): JsonResponse
    {
        $usersInfo = $roleService->getUsersWithRoles($id);
        $role = $roleService->getRoleById($id);

        if (count($usersInfo) > 0) {
            return response()->json([
                'canBeDeleted' => false,
                'message' => __('roles.suggest.remove_with_users', ['roleName' => $role->name, 'countUsers' => count($usersInfo)]),
                'users' => array_values($usersInfo),
                'roles' => $roleService->getRolesForSelections(),
            ]);
        }

        return response()->json([
            'canBeDeleted' => true,
            'message' => 'Роль доступна к удалению. Желаете продолжить?',
        ]);
    }
}
