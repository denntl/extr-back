<?php

namespace Tests\Feature\Admin\Manage\User;

use App\Enums\Authorization\PermissionName;
use App\Models\Application;
use App\Models\User;
use Tests\TestCase;

class ChangeStatusTest extends TestCase
{
    public function testUserHavingActiveAppDeactivatedSuccessfully()
    {
        [$token, $user, $company] = $this->getUserToken(PermissionName::ManageUserUpdate);
        $this->app->setLocale('en');

        $app = Application::factory()->create([
            'full_domain' => 'test.full.domain.com',
            'owner_id' => $user->id,
            'status' => 2 //2 - active, 1 - not active
        ]);

        $user2 = User::factory()->create([
            'company_id' => $company->id,
            'public_id' => 2,
            'status' => 2 //2 - active, 0 - deleted, 1 - newReg
        ]);

        $response = $this->putRequest(
            route('manage.user.changeStatus', ['id' => $user->id]),
            [
                'status' => 0,
                'newApplicationsOwners' => [
                    [
                        'applicationId' => $app->id,
                        'userId' => $user2->id
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

        $this->assertDatabaseHas('applications', [
            'id' => $app->id,
            'status' => 2,
            'owner_id' => $user2->id,
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'status' => 0,
        ]);
    }
    public function testNotReassignActiveAppFail()
    {
        [$token, $user, $company] = $this->getUserToken(PermissionName::ManageUserUpdate);
        $this->app->setLocale('en');

        $app = Application::factory()->create([
            'full_domain' => 'test.full.domain.com',
            'owner_id' => $user->id,
            'status' => 2 //2 - active, 1 - not active
        ]);

        $response = $this->putRequest(
            route('manage.user.changeStatus', ['id' => $user->id]),
            [
                'status' => 0,
                'newApplicationsOwners' => []
            ],
            $token
        );

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('newApplicationsOwners');
        $response->assertJson([
            'message' => 'Before deactivating this user, change owner for all active applications'
        ]);

        $this->assertDatabaseHas('applications', [
            'id' => $app->id,
            'status' => 2,
            'owner_id' => $user->id,
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'status' => 2,
        ]);
    }
    public function testPayloadMissingNewOwnersFail()
    {
        [$token, $user, $company] = $this->getUserToken(PermissionName::ManageUserUpdate);
        $this->app->setLocale('en');

        $app = Application::factory()->create([
            'full_domain' => 'test.full.domain.com',
            'owner_id' => $user->id,
            'status' => 2 //2 - active, 1 - not active
        ]);

        $response = $this->putRequest(
            route('manage.user.changeStatus', ['id' => $user->id]),
            [
                'status' => 0,
            ],
            $token
        );

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('newApplicationsOwners');
        $response->assertJson([
            'message' => 'The new applications owners field must be present.'
        ]);

        $this->assertDatabaseHas('applications', [
            'id' => $app->id,
            'status' => 2,
            'owner_id' => $user->id,
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'status' => 2,
        ]);
    }
    public function testNoActiveAppsSuccess()
    {
        [$token, $user, $company] = $this->getUserToken(PermissionName::ManageUserUpdate);
        $this->app->setLocale('en');

        $app = Application::factory()->create([
            'full_domain' => 'test.full.domain.com',
            'owner_id' => $user->id,
            'status' => 1 //2 - active, 1 - not active
        ]);

        $response = $this->putRequest(
            route('manage.user.changeStatus', ['id' => $user->id]),
            [
                'status' => 0,
                'newApplicationsOwners' => []
            ],
            $token
        );

        $response->assertStatus(200);
        $response->assertJson([
            'isUpdated' => true,
            'message' => 'User deactivated successfully'
        ]);

        $this->assertDatabaseHas('applications', [
            'id' => $app->id,
            'status' => 1,
            'owner_id' => $user->id,
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'status' => 0,
        ]);
    }
}
