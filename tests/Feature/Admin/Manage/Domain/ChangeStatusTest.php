<?php

namespace Tests\Feature\Admin\Manage\Domain;

use App\Enums\Authorization\PermissionName;
use App\Models\Domain;
use Tests\TestCase;

class ChangeStatusTest extends TestCase
{
    public function testSuccessChangeDomainStatus()
    {
        [$token] = $this->getUserToken(PermissionName::ManageDomainUpdate);

        $domain = Domain::factory()->create([
            'domain' => 'example.com',
            'status' => 1
        ]);
        $response = $this->putRequest(
            route('manage.domain.changeStatus', ['id' => $domain->id]),
            [
                'status' => false,
            ],
            $token,
        );

        $response->assertStatus(200);
        $this->assertDatabaseHas('domains', [
            'domain' => 'example.com',
            'status' => 0,
        ]);
        $response->assertJsonStructure([
            'isChanged',
            'message'
        ]);
    }
}
