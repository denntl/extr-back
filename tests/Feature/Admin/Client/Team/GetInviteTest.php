<?php

namespace Tests\Feature\Admin\Client\Team;

use App\Enums\Authorization\PermissionName;
use App\Models\Team;
use Tests\TestCase;

class GetInviteTest extends TestCase
{
    public function testGetInvite(): void
    {
        [$token, $user, $company] = $this->getUserToken(PermissionName::ClientCompanyInvite);
        /** @var Team $team */
        $team = Team::factory()->create([
            'company_id' => $company->id,
        ]);

        $response = $this->getRequest(route('client.team.invite', ['id' => $team->public_id]), $token);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'key',
            'expiredAt',
        ]);
    }

    public function testGetInviteWithoutPermission(): void
    {
        [$token] = $this->getUserToken();

        $response = $this->getRequest(route('client.company.invite'), $token, false);

        $response->assertStatus(403);
    }
}
