<?php

namespace Tests\Feature\Admin\Manage\Role;

use App\Enums\Authorization\PermissionName;
use App\Models\Permission;
use Database\Seeders\PermissionSeeder;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StoreTest extends TestCase
{
    public function testSuccessCreateRole()
    {
        [$token] = $this->getUserToken(PermissionName::ManageRoleCreate);

        $this->seed(PermissionSeeder::class);

        $permissionIds = Permission::all()->take(5)->pluck('id')->toArray();

        $response = $this->postRequest(
            route('manage.roles.store'),
            [
                'name' => 'test',
                'permissionIds' => $permissionIds,
            ],
            $token,
            false
        );

        $response->assertStatus(200);

        $roleId = Role::query()->where('name', 'test')->firstOrFail()->id;
        foreach ($permissionIds as $permission) {
            $this->assertDatabaseHas('role_has_permissions', [
                'role_id' => $roleId,
                'permission_id' => $permission,
            ]);
        }
    }
    public function testInvalidRoleName()
    {
        [$token] = $this->getUserToken(PermissionName::ManageRoleCreate);

        $this->seed(PermissionSeeder::class);

        $permissions = Permission::all()->take(5)->pluck('id')->toArray();

        $response = $this->postRequest(
            route('manage.roles.store'),
            [
                'name' => 't',
                'permissionIds' => $permissions,
            ],
            $token,
        );

        $response->assertStatus(422);
        $response->assertInvalid(['name']);

        $response = $this->postRequest(
            route('manage.roles.store'),
            [
                'name' => fake()->regexify('[A-Za-z0-9]{51}'),
                'permissionIds' => $permissions,
            ],
            $token,
        );

        $response->assertStatus(422);
        $response->assertInvalid(['name']);

        $response = $this->postRequest(
            route('manage.roles.store'),
            [
                'name' => ['test'],
            ],
            $token,
        );

        $response->assertStatus(422);
        $response->assertInvalid(['name']);
    }
    public function testMissingRoleName()
    {
        [$token] = $this->getUserToken(PermissionName::ManageRoleCreate);

        $this->seed(PermissionSeeder::class);

        $permissions = Permission::all()->take(5)->pluck('id')->toArray();

        $response = $this->postRequest(
            route('manage.roles.store'),
            [
                'permissionIds' => $permissions,
            ],
            $token,
        );

        $response->assertStatus(422);
        $response->assertInvalid(['name']);
    }
    public function testInvalidPermissions()
    {
        [$token] = $this->getUserToken(PermissionName::ManageRoleCreate);

        $this->seed(PermissionSeeder::class);

        $permissions = Permission::all()->pluck('id')
            ->last();

        $response = $this->postRequest(
            route('manage.roles.store'),
            [
                'name' => 'test',
                'permissionIds' => '$permissions',
            ],
            $token,
        );

        $response->assertStatus(422);
        $response->assertInvalid(['permissionIds']);

        $response = $this->postRequest(
            route('manage.roles.store'),
            [
                'name' => 'test',
                'permissionIds' => [$permissions + 1, $permissions + 2],
            ],
            $token,
        );

        $response->assertStatus(422);
        $response->assertInvalid(['permissionIds.0', 'permissionIds.1']);

        $response = $this->postRequest(
            route('manage.roles.store'),
            [
                'name' => 'test',
                'permissionIds' => ['test', 'test2'],
            ],
            $token,
        );

        $response->assertStatus(422);
        $response->assertInvalid(['permissionIds.0', 'permissionIds.1']);
    }
}
