<?php

namespace Tests\Feature\Admin\Client\Company;

use App\Enums\Authorization\PermissionName;
use Tests\TestCase;

class GetInviteTest extends TestCase
{
    public function testGetInvite(): void
    {
        [$token] = $this->getUserToken(PermissionName::ClientCompanyInvite);

        $response = $this->getRequest(route('client.company.invite'), $token);

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
