<?php

namespace Tests\Feature\Admin\Manage\Domain;

use App\Enums\Authorization\PermissionName;
use Tests\TestCase;

class StoreTest extends TestCase
{
    public function testSuccessCreateDomain()
    {
        [$token] = $this->getUserToken(PermissionName::ManageDomainCreate);

        $response = $this->postRequest(
            route('manage.domain.store'),
            [
                'domain' => 'example.com',
                'status' => true,
            ],
            $token,
        );

        $response->assertStatus(200);
        $this->assertDatabaseHas('domains', [
            'domain' => 'example.com',
            'status' => 1,
        ]);
    }
    public function testInvalidDomain()
    {
        [$token] = $this->getUserToken(PermissionName::ManageDomainCreate);

        $response = $this->postRequest(
            route('manage.domain.store'),
            [
                'domain' => 'example',
                'status' => true,
            ],
            $token,
        );

        $response->assertStatus(422);
    }
}
