<?php

namespace Tests\Feature\Admin\Manage\Company;

use App\Enums\Authorization\PermissionName;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use WithFaker;

    public function testHasNoPermission()
    {
        [$token] = $this->getUserToken();
        $company = Company::factory()->create();

        $user = User::factory()->create();

        $response = $this->postRequest(
            route('manage.company.update', ['id' => $company->id]),
            [
                'name' => 'Qwerty',
                'owner_id' => $user->id,
            ],
            $token,
            false
        );

        $response->assertStatus(403);
    }

    public function testInvalid()
    {
        [$token] = $this->getUserToken(PermissionName::ManageCompanyUpdate);
        $company = Company::factory()->create();

        $response = $this->postRequest(
            route('manage.company.update', ['id' => $company->id]),
            [],
            $token
        );

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'name',
            'owner_id',
        ]);
    }

    public function testSuccess()
    {
        [$token] = $this->getUserToken(PermissionName::ManageCompanyUpdate);
        $company = Company::factory()->create();
        $users = User::factory()->count(3)->create([
            'company_id' => $company->id,
        ]);

        $role = Role::create([
            'name' => 'Руководитель компании',
            'guard_name' => 'web'
        ]);

        $owner = $users->random()->first();

        $company->owner_id = $owner->id;
        $company->save();

        $updateParams = [
            'name' => $this->faker->text(14),
            'owner_id' => $users->first()->id,
        ];
        $response = $this->postRequest(
            route('manage.company.update', ['id' => $company->id]),
            $updateParams,
            $token
        );

        $response->assertStatus(200);

        $response->assertJson([
            'isUpdated' => true,
            'message' => 'Компания успешно обновлена',
        ]);

        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
            'name' => $updateParams['name'],
            'owner_id' => $updateParams['owner_id'],
        ]);

        $this->assertDatabaseHas('model_has_roles', [
            'role_id' => $role->id,
            'model_id' => $users->first()->id,
        ]);

        $this->assertDatabaseMissing('model_has_roles', [
            'role_id' => $role->id,
            'model_id' => $owner->id,
        ]);
    }
}
