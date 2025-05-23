<?php

namespace Tests\Feature\Admin\Common\Auth;

use App\Enums\User\Status;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use DatabaseTransactions;

    public function testSuccess()
    {
        $company = Company::factory()->create();

        User::factory()->create([
            'email' => 'test@test.com',
            'password' => bcrypt('Password1!'),
            'company_id' => $company->id,
            'status' => Status::Active->value,
        ]);

        $response = $this->postJson(route('common.auth.login'), [
            'email' => 'test@test.com',
            'password' => 'Password1!',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'token',
            'access'
        ]);
    }

    public function testStatusInactive()
    {
        $company = Company::factory()->create();

        User::factory()->create([
            'email' => 'test@test.com',
            'password' => bcrypt('Password1!'),
            'company_id' => $company->id,
            'status' => Status::Deleted->value,
        ]);

        $response = $this->postJson(route('common.auth.login'), [
            'email' => 'test@test.com',
            'password' => 'Password1!',
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'common' => []
            ]
        ]);
    }

    public function testUserNotExists()
    {
        $response = $this->postJson(route('common.auth.login'), [
            'email' => 'test@test.com',
            'password' => 'Password1!',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => __('common.auth.invalid_credentials'),
            'errors' => [
                'email' => [
                    __('common.auth.invalid_credentials'),
                ]
            ]
        ]);
    }

    public function testWrongCredentials()
    {
        $company = Company::factory()->create();

        User::factory()->create([
            'email' => 'test@test.com',
            'password' => bcrypt('Password1!'),
            'company_id' => $company->id,
        ]);

        $response = $this->postJson(route('common.auth.login'), [
            'email' => 'test@test.com',
            'password' => 'different password',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => __('common.auth.invalid_credentials'),
            'errors' => [
                'common' => [
                    __('common.auth.invalid_credentials'),
                ]
            ]
        ]);
    }
}
