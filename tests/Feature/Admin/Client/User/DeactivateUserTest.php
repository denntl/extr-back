<?php

namespace Tests\Feature\Admin\Client\User;

use App\Enums\Authorization\PermissionName;
use App\Models\Application;
use App\Models\User;
use Tests\TestCase;

class DeactivateUserTest extends TestCase
{
    public function testUserActivatedSuccessfully()
    {
        [$token, $user, $company] = $this->getUserToken(PermissionName::ClientUserDeactivate);
        $this->app->setLocale('en');

        Application::factory()->create([
            'full_domain' => 'test.full.domain.com',
            'owner_id' => $user->id,
            'status' => 0 //2 - active, 0 - deleted
        ]);

        $response = $this->putRequest(
            route('client.user.changeStatus', ['id' => $user->public_id]),
            [
                'companyId' => $company->public_id,
                'status' => true,
                'newApplicationsOwners' => []
            ],
            $token
        );

        $response->assertStatus(200);
        $response->assertJson([
            'isUpdated' => true,
            'message' => 'User activated successfully',
        ]);
    }
    public function testUserDeactivatedSuccessfully()
    {
        [$token, $user, $company] = $this->getUserToken(PermissionName::ClientUserDeactivate);
        $this->app->setLocale('en');

        Application::factory()->create([
            'full_domain' => 'test.full.domain.com',
            'owner_id' => $user->id,
            'status' => 2 //2 - active, 0 - deleted
        ]);
        $user2 = User::factory()->create([
            'company_id' => $company->id,
            'public_id' => 2,
        ]);

        $response = $this->putRequest(
            route('client.user.changeStatus', ['id' => $user2->public_id]),
            [
                'companyId' => $company->public_id,
                'status' => false,
                'newApplicationsOwners' => []
            ],
            $token
        );

        $response->assertStatus(200);
        $response->assertJson([
            'isUpdated' => true,
            'message' => 'User deactivated successfully',
        ]);
    }
    public function testUserHavingActivePwaDeactivatedSuccessfully()
    {
        [$token, $user, $company] = $this->getUserToken(PermissionName::ClientUserDeactivate);
        $this->app->setLocale('en');

        $user2 = User::factory()->create([
            'company_id' => $company->id,
            'public_id' => 2,
        ]);
        $pwa = Application::factory()->create([
            'full_domain' => 'test.full.domain.com',
            'owner_id' => $user2->id,
            'status' => 2 //2 - active, 0 - deleted
        ]);

        $response = $this->putRequest(
            route('client.user.changeStatus', ['id' => $user2->public_id]),
            [
                'companyId' => $company->public_id,
                'status' => false,
                'newApplicationsOwners' => [
                    [
                        'applicationId' => $pwa->id,
                        'userId' => $user->id
                    ]
                ]
            ],
            $token
        );

        $response->assertStatus(200);
        $response->assertJson([
            'isUpdated' => true,
            'message' => 'User deactivated successfully',
        ]);
    }
    public function testCompanyOwnerNotDeactivated()
    {
        [$token, $user, $company] = $this->getUserToken(PermissionName::ClientUserDeactivate);
        $this->app->setLocale('en');

        $user2 = User::factory()->create([
            'company_id' => $company->id,
            'public_id' => 2,
        ]);
        $pwa = Application::factory()->create([
            'full_domain' => 'test.full.domain.com',
            'owner_id' => $user2->id,
            'status' => 2 //2 - active, 0 - deleted
        ]);

        $response = $this->putRequest(
            route('client.user.changeStatus', ['id' => $user->public_id]),
            [
                'companyId' => $company->public_id,
                'status' => false,
                'newApplicationsOwners' => [
                    [
                        'applicationId' => $pwa->id,
                        'userId' => $user2->id
                    ]
                ]
            ],
            $token
        );

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'You can\'t deactivate company owner',
        ]);
    }
}
