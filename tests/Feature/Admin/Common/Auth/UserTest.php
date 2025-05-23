<?php

namespace Tests\Feature\Admin\Common\Auth;

use App\Enums\User\Status;
use App\Models\Company;
use App\Models\CompanyBalance;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserTest extends TestCase
{
    use DatabaseTransactions;

    public function testSuccess()
    {
        $company = Company::factory()->create([
            'name' => 'Test',
            'tariff_id' => 1,
        ]);

        $this->assertDatabaseHas('company_balances', [
            'company_id' => $company->id,
        ]);

        CompanyBalance::where('company_id', $company->id)->update([
            'balance' => 11.23,
            'balance_bonus' => 22.23,
        ]);

        $user = User::factory()->create([
            'email' => 'test@test.com',
            'password' => bcrypt('Password1!'),
            'company_id' => $company->id,
            'status' => Status::Active->value,
        ]);

        $response = $this->getJson(route('common.auth.user'), [
            'authorization' => 'Bearer ' . $user->createToken('auth_token')->plainTextToken,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'user' => [
                'public_id' => $user->public_id,
                'username' => $user->username,
                'email' => $user->email,
            ],
            'params' => [
                'balance' => 11.23,
                'balance_bonus' => 22.23,
                'tariff_type_id' => 1,
                'companyName' => 'Test',
                'hasApplications' => false,
                'companyId' => $company->public_id,
            ],
            'access' => [
                'isAdmin' => false,
                'permissions' => [],
            ],
        ]);
    }

    public function testSuccessWithAccess()
    {
        $company = Company::factory()->create();

        $user = User::factory()->create([
            'email' => 'test@test.com',
            'password' => bcrypt('Password1!'),
            'company_id' => $company->id,
            'status' => Status::Active->value,
        ]);

        $role = Role::create(['name' => 'test']);
        $permissions = ['test-permission-1', 'test-permission-2', 'test-permission-3'];
        foreach ($permissions as $permission) {
            $perm = Permission::query()->create(['name' => $permission]);
            $role->givePermissionTo($perm);
        }
        $user->assignRole($role);

        $role2 = Role::create(['name' => 'test1']);
        $permissions = ['test1-permission-1', 'test1-permission-2'];
        foreach ($permissions as $permission) {
            $perm = Permission::query()->create(['name' => $permission]);
            $role2->givePermissionTo($perm);
        }

        $response = $this->getJson(route('common.auth.user'), [
            'authorization' => 'Bearer ' . $user->createToken('auth_token')->plainTextToken,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'user' => [
                'public_id',
                'username',
                'email',
            ],
            'params' => [
                'balance',
                'balance_bonus',
                'tariff_type_id',
                'companyName',
                'hasApplications',
            ],
            'access'
        ]);
        $this->assertEquals($response['access'], [
            'isAdmin' => false,
            'permissions' => ['test-permission-1', 'test-permission-2', 'test-permission-3'],
        ]);
    }

    public function testFailWithoutToken()
    {
        $response = $this->getJson(route('common.auth.user'));

        $response->assertStatus(401);
        $response->assertJson(['message' => 'Unauthenticated.']);
    }

    public function testFailWithToken()
    {
        $response = $this->getJson(route('common.auth.user'), [
            'authorization' => 'wrong auth token',
        ]);

        $response->assertStatus(401);
        $response->assertJson(['message' => 'Unauthenticated.']);
    }
}
