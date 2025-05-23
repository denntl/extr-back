<?php

namespace Tests\Feature\Admin\Manage\Company;

use App\Enums\Authorization\PermissionName;
use App\Models\Company;
use App\Models\User;
use Tests\TestCase;

class EditTest extends TestCase
{
    public function testHasNoPermission()
    {
        [$token] = $this->getUserToken();
        $company = Company::factory()->create();

        $response = $this->getRequest(
            route('manage.company.edit', ['id' => $company->id]),
            $token,
            false
        );

        $response->assertStatus(403);
    }

    public function testSuccess()
    {
        [$token] = $this->getUserToken(PermissionName::ManageCompanyUpdate);
        $company = Company::factory()->create();
        $users = User::factory()->count(3)->create([
            'company_id' => $company->id,
        ]);

        $owner = $users->random()->first();

        $company->owner_id = $owner->id;
        $company->save();

        $response = $this->getRequest(
            route('manage.company.edit', ['id' => $company->id]),
            $token
        );

        $response->assertStatus(200);
        $response->assertJson([
            'company' => [
                'name' => $company->name,
                'owner_id' => $owner->id,
            ],
            'users' => $users->map(function ($user) {
                return [
                    'value' => $user->id,
                    'label' => $user->name,
                ];
            })->toArray(),
        ]);
    }
}
