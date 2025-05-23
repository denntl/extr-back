<?php

namespace Tests\Feature\Admin\Client\Team;

use App\Enums\Authorization\PermissionName;
use App\Models\Team;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use WithFaker;

    public function testSuccess()
    {
        $role = Role::create(['name' => 'Руководитель команды']);
        [$token, $user] = $this->getUserToken(PermissionName::ClientTeamCreate);

        $name = 'Team store';

        $response = $this->postRequest(
            route('client.team.store'),
            [
                'name' => $name,
                'members' => [$user->id],
                'teamLeadId' => $user->public_id,
            ],
            $token,
        );

        $response->assertStatus(200);

        $this->assertDatabaseHas('teams', [
            'team_lead_id' => $user->id,
            'name' => $name
        ]);

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'Руководитель команды',
        ]);
        $this->assertDatabaseHas('model_has_roles', [
            'role_id' => $role->id,
            'model_id' => $user->id,
        ]);
    }
    public function testCreateAfterDeletingTeam()
    {
        Role::create(['name' => 'Руководитель команды']);
        [$token, $user] = $this->getUserToken(PermissionName::ClientTeamCreate);

        $name = 'Team store';

        $response = $this->postRequest(
            route('client.team.store'),
            [
                'name' => $name,
                'members' => [$user->id],
                'teamLeadId' => $user->public_id,
            ],
            $token,
        );

        $response->assertStatus(200);
        $this->assertDatabaseHas('teams', [
            'team_lead_id' => $user->id,
            'name' => $name
        ]);

        $team = Team::where('name', 'Team store')->first();

        $this->deleteRequest(
            route('client.team.destroy', ['id' => $team->public_id]),
            [],
            $token
        );
        $this->assertDatabaseMissing('teams', [
            'team_lead_id' => $user->id,
            'name' => $name,
            'deleted_at' => null
        ]);
        $this->assertCount(1, Team::withTrashed()->get());

        $response = $this->postRequest(
            route('client.team.store'),
            [
                'name' => $name,
                'members' => [$user->id],
                'teamLeadId' => $user->public_id,
            ],
            $token,
        );
        $response->assertStatus(200);
        $this->assertCount(2, Team::withTrashed()->get());
        $this->assertCount(1, Team::onlyTrashed()->get());
    }
}
