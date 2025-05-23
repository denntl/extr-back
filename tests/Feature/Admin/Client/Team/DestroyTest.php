<?php

namespace Tests\Feature\Admin\Client\Team;

use App\Enums\Authorization\PermissionName;
use App\Models\Team;
use App\Models\User;
use App\Services\Client\Team\TeamService;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    use WithFaker;

    public function testSuccess()
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

        $user2 = User::factory()->create([
            'company_id' => $user->company_id,
            'team_id' => $team->id,
        ]);
        $user3 = User::factory()->create([
            'company_id' => $user->company_id,
            'team_id' => $team->id,
        ]);

        $response = $this->deleteRequest(
            route('client.team.destroy', ['id' => $team->public_id]),
            [],
            $token
        );

        $response->assertStatus(200);

        $response->assertJson([
            'isDeleted' => true,
        ]);


        $this->assertCount(1, Team::withTrashed()->get());
        $this->assertCount(0, Team::all());

        $this->assertDatabaseHas('users', [
            'id' => $user2->id,
            'team_id' => null
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $user3->id,
            'team_id' => null
        ]);

        $this->assertDatabaseMissing('model_has_roles', [
            'role_id' => $role->id,
            'model_id' => $user->id,
        ]);
    }

    public function testDontRemoveRoleIfHasMoreTeamsInCharge()
    {
        $role = Role::create(['name' => 'Руководитель команды']);
        [$token, $user] = $this->getUserToken(PermissionName::ClientTeamUpdate);

        /** @var Team $team */
        $team = Team::factory()->create([
            'company_id' => $user->company_id,
            'name' => 'Team name',
            'team_lead_id' => $user->id,
        ]);
        /** @var Team $team */
        $team2 = Team::factory()->create([
            'company_id' => $user->company_id,
            'name' => 'Team name2',
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

        $response = $this->deleteRequest(
            route('client.team.destroy', ['id' => $team->public_id]),
            [],
            $token
        );

        $response->assertStatus(200);
        $response->assertJson([
            'isDeleted' => true,
        ]);
        $this->assertCount(2, Team::withTrashed()->get());
        $this->assertCount(1, Team::onlyTrashed()->get());
        $this->assertCount(1, Team::all());
        $this->assertDatabaseHas('model_has_roles', [
            'role_id' => $role->id,
            'model_id' => $user->id,
        ]);

        $response2 = $this->deleteRequest(
            route('client.team.destroy', ['id' => $team2->public_id]),
            [],
            $token
        );
        $response2->assertStatus(200);
        $response2->assertJson([
            'isDeleted' => true,
        ]);
        $this->assertCount(2, Team::withTrashed()->get());
        $this->assertCount(2, Team::onlyTrashed()->get());
        $this->assertCount(0, Team::all());
        $this->assertDatabaseMissing('model_has_roles', [
            'role_id' => $role->id,
            'model_id' => $user->id,
        ]);
    }

    public function testDestroyTeamWithoutTeamLead()
    {
        $role = Role::create(['name' => 'Руководитель команды']);
        [$token, $user] = $this->getUserToken(PermissionName::ClientTeamUpdate);

        /** @var Team $team */
        $team = Team::factory()->create([
            'company_id' => $user->company_id,
            'name' => 'Team name',
            'team_lead_id' => null,
        ]);

        $response = $this->deleteRequest(
            route('client.team.destroy', ['id' => $team->public_id]),
            [],
            $token
        );

        $response->assertStatus(200);

        $response->assertJson([
            'isDeleted' => true,
        ]);

        $this->assertCount(1, Team::withTrashed()->get());
        $this->assertCount(0, Team::all());

        $this->assertDatabaseMissing('model_has_roles', [
            'role_id' => $role->id,
            'model_id' => $user->id,
        ]);
    }

    public function testTeamNotFoundFail()
    {
        [$token, $user] = $this->getUserToken(PermissionName::ClientTeamUpdate);

        /** @var Team $team */
        $team = Team::factory()->create([
            'company_id' => $user->company_id,
            'name' => 'Team name',
            'team_lead_id' => $user->id,
        ]);

        $user2 = User::factory()->create([
            'company_id' => $user->company_id,
            'team_id' => $team->id,
        ]);
        $user3 = User::factory()->create([
            'company_id' => $user->company_id,
            'team_id' => $team->id,
        ]);

        $response = $this->deleteRequest(
            route('client.team.destroy', ['id' => -1]),
            [],
            $token
        );
        $response->assertStatus(422);

        $response->assertJsonStructure([
            'errors' => [],
            'message'
        ]);
        $response->assertJson([
            'message' => __('team.error.delete'),
        ]);

        $this->assertCount(0, Team::onlyTrashed()->get());
        $this->assertCount(1, Team::all());

        $this->assertDatabaseHas('users', [
            'id' => $user2->id,
            'team_id' => $team->id
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $user3->id,
            'team_id' => $team->id
        ]);
    }

    public function testDestroyHandlesErrorOnTeamDelete()
    {
        [$token, $user] = $this->getUserToken(PermissionName::ClientTeamUpdate);

        /** @var Team $team */
        $team = Team::factory()->create([
            'company_id' => $user->company_id,
            'name' => 'Team name',
            'team_lead_id' => $user->id,
        ]);

        $user2 = User::factory()->create([
            'company_id' => $user->company_id,
            'team_id' => $team->id,
        ]);
        $user3 = User::factory()->create([
            'company_id' => $user->company_id,
            'team_id' => $team->id,
        ]);

        $mockedTeam = $this->partialMock(Team::class, function ($mock) use ($team) {
            $mock->shouldReceive('where')
                ->with('public_id', $team->public_id)
                ->with('company_id', $team->company_id)
                ->andReturnSelf();
            $mock->shouldReceive('firstOrFail')->andReturn($team);
            $mock->shouldReceive('delete')->andThrow(new \Exception('Failed deleting team'));
        });

        $this->app->bind(TeamService::class, function () use ($mockedTeam, $user) {
            return new readonly class ($user->company_id, $mockedTeam) extends TeamService {
                private Team $mockedTeam;

                public function __construct(int $companyId, Team $mockedTeam)
                {
                    parent::__construct($companyId);
                    $this->mockedTeam = $mockedTeam;
                }

                public function getByPublicId(int $id, ?bool $public = true): Team
                {
                    return $this->mockedTeam;
                }
            };
        });

        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('rollBack')->once();
        DB::shouldReceive('commit')->never();

        $response = $this->deleteRequest(
            route('client.team.destroy', ['id' => $team->public_id]),
            [],
            $token
        );

        $this->expectException(\Exception::class);

        $response->assertStatus(422);

        $response->assertJsonStructure([
            'errors' => [],
            'message'
        ]);
        $response->assertJson([
            'message' => __('team.error.delete'),
        ]);

        $this->assertCount(0, Team::onlyTrashed()->get());
        $this->assertCount(1, Team::all());

        $this->assertDatabaseHas('users', [
            'id' => $user2->id,
            'team_id' => $team->id
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $user3->id,
            'team_id' => $team->id
        ]);
    }
}
