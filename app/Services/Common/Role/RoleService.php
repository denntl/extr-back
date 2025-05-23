<?php

namespace App\Services\Common\Role;

use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role;

class RoleService
{
    public function getRolesList(): Collection
    {
        return Role::query()
            ->get()
            ->map(function ($role) {
                return [
                    'value' => $role->id,
                    'label' => __("role.$role->name"),
                ];
            });
    }

    /**
     * @param $roleIds
     * @return Collection
     */
    public function getRolesById($roleIds): Collection
    {
        return Role::whereIn('id', $roleIds)->get();
    }
}
