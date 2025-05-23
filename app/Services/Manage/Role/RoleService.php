<?php

namespace App\Services\Manage\Role;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class RoleService
{
    /**
     * @param int $id
     * @return Role
     */
    public function getRoleById(int $id): Role
    {
        return Role::findOrFail($id);
    }

    /**
     * @param int $id
     * @param array $data
     * @return Role
     */
    public function update(int $id, array $data): Role
    {
        $role = Role::findOrFail($id);
        $role->update(['name' => $data['name']]);
        $role->syncPermissions($data['permissionIds']);

        return $role;
    }

    /**
     * @param array $data
     * @return Role
     */
    public function store(array $data): Role
    {
        $role = Role::create([
            'guard_name' => 'web',
            'name' => $data['name']
        ]);
        if (isset($data['permissionIds'])) {
            $role->syncPermissions($data['permissionIds']);
        }

        return $role;
    }

    public function destroy(int $roleId): void
    {
        Role::destroy($roleId);
    }

    public function syncUserRoles(int $userId, array $roleIds): void
    {
        User::find($userId)->syncRoles($roleIds);
    }

    public function getRolesForSelections(): Collection
    {
        return Role::query()
            ->selectRaw('id as value, name as label')
            ->orderBy('id')
            ->get();
    }

    public function getUsersWithRoles(int $roleId): array
    {
        $users = User::select(
            'users.id',
            'users.company_id',
            'users.username',
            'users.name',
            'companies.name as company_name',
            'companies.id as company_id'
        )
            ->join('companies', 'users.company_id', '=', 'companies.id')
            ->join('model_has_roles', function ($join) use ($roleId) {
                $join->on('users.id', '=', 'model_has_roles.model_id')
                    ->where('model_has_roles.model_type', 'App\Models\User')
                    ->where('model_has_roles.role_id', '=', $roleId);
            })
            ->groupBy('users.id', 'users.company_id', 'users.username', 'users.name', 'company_name', 'companies.id')
            ->get()
            ->keyBy('id')
            ->toArray();

        $userIds = array_keys($users);
        $roles = DB::table('model_has_roles')
            ->select('model_id', 'role_id')
            ->where('model_type', 'App\Models\User')
            ->whereIn('model_id', $userIds)
            ->get();

        foreach ($roles as $role) {
            $users[$role->model_id]['roleIds'][] = $role->role_id;
        }

        return $users;
    }
}
