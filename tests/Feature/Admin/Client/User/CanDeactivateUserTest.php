<?php

namespace Tests\Feature\Admin\Client\User;

use App\Enums\Authorization\PermissionName;
use App\Models\Application;
use App\Models\User;
use Tests\TestCase;

class CanDeactivateUserTest extends TestCase
{
    public function testSuccess()
    {
        [$token, $user, $company] = $this->getUserToken(PermissionName::ClientUserDeactivate);
        $this->app->setLocale('en');
        Application::factory()->create([
            'full_domain' => 'test.full.domain.com',
            'owner_id' => $user->id,
            'status' => 2
        ]);
        $user2 = User::factory()->create([
            'company_id' => $company->id,
            'public_id' => 2,
        ]);

        $response = $this->getRequest(
            route('client.user.canChangeStatus', [
                'id' => $user2->public_id,
                'companyId' => $company->public_id,
                'status' => false,
            ]),
            $token
        );
        $response->assertStatus(200);
        $response->assertJson([
            'canUpdate' => true,
            'message' => 'Are you sure you want to deactivate the user?',
        ]);
    }
    public function testUserIsCompanyOwnerFail()
    {
        [$token, $user, $company] = $this->getUserToken(PermissionName::ClientUserDeactivate);
        $this->app->setLocale('en');

        Application::factory()->create([
            'full_domain' => 'test.full.domain.com',
            'owner_id' => $user->id + 1,
            'status' => 2
        ]);

        $response = $this->getRequest(
            route('client.user.canChangeStatus', [
                'id' => $user->public_id,
                'companyId' => $company->public_id,
                'status' => false,
            ]),
            $token
        );

        $response->assertStatus(200);
        $response->assertJson([
            'canUpdate' => false,
            'message' => 'You can\'t deactivate company owner',
        ]);
    }
    public function testUserHasActivePwaFail()
    {
        [$token, $user, $company] = $this->getUserToken(PermissionName::ClientUserDeactivate);
        $this->app->setLocale('en');

        $user2 = User::factory()->create([
            'company_id' => $company->id,
            'public_id' => 2,
        ]);
        Application::factory()->create([
            'full_domain' => 'test.full.domain.com',
            'owner_id' => $user2->id,
            'status' => 2
        ]);

        $response = $this->getRequest(
            route('client.user.canChangeStatus', [
                'id' => $user2->public_id,
                'companyId' => $company->public_id,
                'status' => false,
            ]),
            $token
        );

        $response->assertStatus(200);
        $response->assertJson([
            'canUpdate' => false,
            'message' => 'You need to change PWA\'s owner before deactivating this user',
        ]);
    }
    public function testActivateUser()
    {
        [$token, $user, $company] = $this->getUserToken(PermissionName::ClientUserDeactivate);
        $this->app->setLocale('en');

        Application::factory()->create([
            'full_domain' => 'test.full.domain.com',
            'owner_id' => $user->id,
            'status' => 2
        ]);

        $response = $this->getRequest(
            route('client.user.canChangeStatus', [
                'id' => $user->public_id,
                'companyId' => $company->public_id,
                'status' => true,
            ]),
            $token,
            false
        );

        $response->assertStatus(200);
        $response->assertJson([
            'canUpdate' => true,
            'message' => 'Are you sure you want to activate the user?',
        ]);
    }
    public function testInvalidParametersFail()
    {
        [$token, $user, $company] = $this->getUserToken(PermissionName::ClientUserDeactivate);
        $this->app->setLocale('en');

        Application::factory()->create([
            'full_domain' => 'test.full.domain.com',
            'owner_id' => $user->id,
            'status' => 2
        ]);

        $response = $this->getRequest(
            route('client.user.canChangeStatus', [
                'id' => $user->public_id,
                'companyId' => $company->public_id,
                'status' => 'something',
            ]),
            $token
        );

        $response->assertStatus(422);

        $response = $this->getRequest(
            route('client.user.canChangeStatus', [
                'id' => $user->public_id,
                'companyId' => $company->public_id,
                'status' => 2,
            ]),
            $token
        );

        $response->assertStatus(422);
    }
    public function testHasNoPermission()
    {
        [$token, $user, $company] = $this->getUserToken();
        $this->app->setLocale('en');

        Application::factory()->create([
            'full_domain' => 'test.full.domain.com',
            'owner_id' => $user->id,
            'status' => 2
        ]);

        $response = $this->getRequest(
            route('client.user.canChangeStatus', [
                'id' => $user->public_id,
                'companyId' => $company->public_id,
                'status' => true,
            ]),
            $token,
            false
        );

        $response->assertStatus(403);
    }
}
