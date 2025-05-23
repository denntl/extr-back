<?php

namespace Tests\Feature\Admin\Client\MyCompany;

use App\Enums\Authorization\PermissionName;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use WithFaker;

    public function testSuccess()
    {
        [$token, $user] = $this->getUserToken(PermissionName::ManageCompanyUpdate);

        $updateParams = [
            'name' => $this->faker->text(14)
        ];

        $response = $this->postRequest(
            route('client.company.update'),
            $updateParams,
            $token
        );

        $response->assertStatus(200);

        $response->assertJson([
            'isUpdated' => true,
            'message' => 'Компания успешно обновлена',
        ]);

        $this->assertDatabaseHas('companies', [
            'id' => $user->company_id,
            'name' => $updateParams['name']
        ]);
    }

    public function testHasNoPermission()
    {
        [$token] = $this->getUserToken();
        $company = Company::factory()->create();

        $user = User::factory()->create();

        $response = $this->postRequest(
            route('client.company.update', ['id' => $company->id]),
            [
                'name' => 'Qwerty',
                'owner_id' => $user->id,
            ],
            $token,
            false
        );

        $response->assertStatus(403);
    }
}
