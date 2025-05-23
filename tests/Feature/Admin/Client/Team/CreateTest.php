<?php

namespace Feature\Admin\Client\Team;

use App\Enums\Authorization\PermissionName;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use WithFaker;

    public function testSuccess()
    {
        [$token] = $this->getUserToken(PermissionName::ClientTeamCreate);

        $response = $this->postRequest(
            route('client.team.create'),
            [],
            $token,
            false
        );

        $response->assertStatus(200);

        $this->assertIsArray($response->json('teamLeaders'));
        $this->assertIsArray($response->json('members'));
    }
}
