<?php

namespace Database\Seeders;

use App\Enums\Authorization\PermissionName;
use App\Services\Common\Auth\PermissionService;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (PermissionName::cases() as $permission) {
            Permission::query()->updateOrCreate([
                'name'       => $permission->value,
            ], [
                'name'       => $permission->value,
                'guard_name' => config('auth.defaults.guard'),
            ]);
        }

        $role = Role::where('name', 'Trash role. Full access')->first();
        if ($role) {
            $role->syncPermissions(Permission::all());
        }

        $predefinedRoles = PermissionService::predefinedRoles();
        foreach ($predefinedRoles as $role) {
            Role::query()->updateOrCreate(
                ['name' => $role],
                [
                    'name' => $role,
                    'guard_name' => config('auth.defaults.guard'),
                    'is_predefined' => true,
                ]
            );
        }
    }
}
