<?php

namespace Tests\Feature\Admin\Client\Team;

use App\Enums\Authorization\PermissionName;
use App\Models\Team;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EditTest extends TestCase
{
    use WithFaker;

    public function testSuccess()
    {
        [$token, $user] = $this->getUserToken(PermissionName::ClientTeamUpdate);

        /** @var Team $team */
        $team = Team::factory()->create([
            'company_id' => $user->company_id,
            'name' => 'Test Team',
            'team_lead_id' => $user->id,
        ]);

        $response = $this->postRequest(
            route('client.team.edit', ['id' => $team->public_id]),
            [],
            $token
        );

        $response->assertStatus(200);

        $this->assertEquals($team->name, $response->json('team.name'));
        $this->assertEquals($team->teamLead->public_id, $response->json('team.teamLeadId'));
        $this->assertEquals($team->public_id, $response->json('team.id'));
    }
}
