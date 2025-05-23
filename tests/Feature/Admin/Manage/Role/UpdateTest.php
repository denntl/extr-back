<?php

namespace Tests\Feature\Admin\Manage\Role;

use App\Enums\Authorization\PermissionName;
use App\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    public function testSuccessUpdateRole()
    {
        [$token] = $this->getUserToken(PermissionName::ManageRoleUpdate);

        Permission::create(['name' => 'test1', 'guard_name' => 'web']);
        Permission::create(['name' => 'test2', 'guard_name' => 'web']);
        Permission::create(['name' => 'test3', 'guard_name' => 'web']);
        Permission::create(['name' => 'test4', 'guard_name' => 'web']);
        Permission::create(['name' => 'test5', 'guard_name' => 'web']);
        $roleId = Role::create(['name' => 'test'])->id;
        $permissions = Permission::all()->take(5)->pluck('id')->toArray();

        $response = $this->putRequest(
            route('manage.roles.update', ['id' => $roleId]),
            [
                'name' => 'test',
                'permissionIds' => $permissions,
            ],
            $token,
        );

        $response->assertStatus(200);
        foreach ($permissions as $permission) {
            $this->assertDatabaseHas('role_has_permissions', [
                'role_id' => $roleId,
                'permission_id' => $permission,
            ]);
        }
    }
    public function testInvalidRoleName()
    {
        [$token] = $this->getUserToken(PermissionName::ManageRoleUpdate);

        Permission::create(['name' => 'test1', 'guard_name' => 'web']);
        Permission::create(['name' => 'test2', 'guard_name' => 'web']);
        $roleId = Role::create(['name' => 'test'])->id;
        $permissions = Permission::all()->take(5)->pluck('id')->toArray();

        $response = $this->putRequest(
            route('manage.roles.update', ['id' => $roleId]),
            [
                'name' => 't',
                'permissionIds' => $permissions,
            ],
            $token,
        );

        $response->assertStatus(422);
        $response->assertInvalid(['name']);

        $response = $this->putRequest(
            route('manage.roles.update', ['id' => $roleId]),
            [
                'name' => fake()->regexify('[A-Za-z0-9]{51}'),
                'permissionIds' => $permissions,
            ],
            $token,
        );

        $response->assertStatus(422);
        $response->assertInvalid(['name']);

        $response = $this->putRequest(
            route('manage.roles.update', ['id' => $roleId]),
            [
                'name' => ['test'],
                'permissionIds' => $permissions,
            ],
            $token,
        );

        $response->assertStatus(422);
        $response->assertInvalid(['name']);
    }
    public function testMissingRoleName()
    {
        [$token] = $this->getUserToken(PermissionName::ManageRoleUpdate);

        Permission::create(['name' => 'test1', 'guard_name' => 'web']);
        Permission::create(['name' => 'test2', 'guard_name' => 'web']);
        $roleId = Role::create(['name' => 'test'])->id;
        $permissions = Permission::all()->take(5)->pluck('id')->toArray();

        $response = $this->putRequest(
            route('manage.roles.update', ['id' => $roleId]),
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
        [$token] = $this->getUserToken(PermissionName::ManageRoleUpdate);

        Permission::create(['name' => 'test1', 'guard_name' => 'web']);
        Permission::create(['name' => 'test2', 'guard_name' => 'web']);
        $roleId = Role::create(['name' => 'test'])->id;
        $permissions = Permission::all()->pluck('id')
            ->last();

        $response = $this->putRequest(
            route('manage.roles.update', ['id' => $roleId]),
            [
                'name' => 'test',
                'permissionIds' => '$permissions',
            ],
            $token,
        );

        $response->assertStatus(422);
        $response->assertInvalid(['permissionIds']);

        $response = $this->putRequest(
            route('manage.roles.update', ['id' => $roleId]),
            [
                'name' => 'test',
                'permissionIds' => [$permissions + 1, $permissions + 2],
            ],
            $token,
        );

        $response->assertStatus(422);
        $response->assertInvalid(['permissionIds.0', 'permissionIds.1']);

        $response = $this->putRequest(
            route('manage.roles.update', ['id' => $roleId]),
            [
                'name' => 'test',
                'permissionIds' => ['test', 'test2'],
            ],
            $token,
        );

        $response->assertStatus(422);
        $response->assertInvalid(['permissionIds.0', 'permissionIds.1']);
    }

    public function testUpdatePredefinedRolesNameFail()
    {
        [$token] = $this->getUserToken(PermissionName::ManageRoleUpdate);

        Permission::create(['name' => 'test1', 'guard_name' => 'web']);
        Permission::create(['name' => 'test2', 'guard_name' => 'web']);
        Permission::create(['name' => 'test3', 'guard_name' => 'web']);
        Permission::create(['name' => 'test4', 'guard_name' => 'web']);
        Permission::create(['name' => 'test5', 'guard_name' => 'web']);

        $roleId = Role::create(['name' => 'test', 'guard_name' => 'web', 'is_predefined' => true])->id;
        $permissions = Permission::all()->take(3)->pluck('id')->toArray();

        $response = $this->putRequest(
            route('manage.roles.update', ['id' => $roleId]),
            [
                'name' => 'test',
                'permissionIds' => $permissions,
            ],
            $token,
        );
        $response->assertStatus(200);

        $response = $this->putRequest(
            route('manage.roles.update', ['id' => $roleId]),
            [
                'name' => 'test2',
                'permissionIds' => [],
            ],
            $token,
        );
        $response->assertStatus(422);
        $response->assertInvalid(['name']);
        $response->assertJson([
            'message' => 'Вы не можете обновить имя предопределенной роли'
        ]);
    }
}
