<?php

namespace Feature\Admin\Client\Team;

use App\Enums\Authorization\PermissionName;
use App\Models\Company;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use WithFaker;

    public function testSuccess()
    {
        [$token, $user] = $this->getUserToken(PermissionName::ClientTeamUpdate);

        /** @var Team $team */
        $team = Team::factory()->create([
            'company_id' => $user->company_id,
            'name' => 'Team name',
            'team_lead_id' => $user->id,
        ]);

        $newName = 'Team updated';

        $response = $this->postRequest(
            route('client.team.update', ['id' => $team->public_id]),
            [
                'name' => $newName,
                'members' => [$user->public_id],
                'teamLeadId' => $user->public_id,
            ],
            $token
        );

        $response->assertStatus(200);

        $response->assertJson([
            'isUpdated' => true,
            'message' => 'Команда успешно обновлена',
        ]);

        $this->assertDatabaseHas('teams', [
            'id' => $team->id,
            'name' => $newName
        ]);
    }

    public function testUpdateTeamLeader()
    {
        $role = Role::create(['name' => 'Руководитель команды']);
        [$token, $user] = $this->getUserToken(PermissionName::ClientTeamUpdate);

        /** @var Team $team */
        $team = Team::factory()->create([
            'company_id' => $user->company_id,
            'name' => 'Team name',
            'team_lead_id' => $user->id,
        ]);

        $user->assignRole([$role->id]);

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'Руководитель команды',
        ]);
        $this->assertDatabaseHas('model_has_roles', [
            'role_id' => $role->id,
            'model_id' => $user->id,
        ]);

        $newTeamLead = User::factory()->create([
            'company_id' => $user->company_id,
            'name' => 'Team lead name',
        ]);

        $newName = 'Team updated';

        $response = $this->postRequest(
            route('client.team.update', ['id' => $team->public_id]),
            [
                'name' => $newName,
                'members' => [$user->public_id],
                'teamLeadId' => $newTeamLead->public_id,
            ],
            $token
        );

        $response->assertStatus(200);

        $response->assertJson([
            'isUpdated' => true,
            'message' => 'Команда успешно обновлена',
        ]);
        $this->assertDatabaseHas('teams', [
            'id' => $team->id,
            'name' => $newName
        ]);

        $this->assertDatabaseHas('model_has_roles', [
            'role_id' => $role->id,
            'model_id' => $newTeamLead->id,
        ]);
        $this->assertDatabaseMissing('model_has_roles', [
            'role_id' => $role->id,
            'model_id' => $user->id,
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
