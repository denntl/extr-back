<?php

namespace Tests\Feature\Admin\Common\DataListing\Permissions;

use App\Enums\Authorization\PermissionName;
use App\Models\Permission;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DataListingDataTest extends TestCase
{
    use DatabaseTransactions;

    public function testSuccess()
    {
        [$token] = $this->getUserToken(PermissionName::ManagePermissionRead);

        $response = $this->postRequest(
            route('common.listing.data', ['entity' => 'permissions']),
            [],
            $token
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'prev',
            'next',
            'page',
            'data',
            'total',
            'perPage',
            'pages',
        ]);
    }

    public function testSuccessPagination()
    {
        [$token] = $this->getUserToken(PermissionName::ManagePermissionRead);

        $userPermission = Permission::first();
        Permission::factory()->count(12)->create();

        $response = $this->postRequest(
            route(
                'common.listing.data',
                ['entity' => 'permissions'],
            ),
            ['page' => 1],
            $token
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'prev',
            'next',
            'page',
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'description',
                ]
            ],
            'total',
            'perPage',
            'pages',
        ]);

        $this->assertCount(10, $response->json('data'));

        $response->assertJsonPath('data.0', [
            'id' => $userPermission->id,
            'name' => __("permissions.{$userPermission->name}.name"),
            'description' => __("permissions.{$userPermission->name}.description"),
        ]);

        $response = $this->postRequest(
            route(
                'common.listing.data',
                ['entity' => 'permissions'],
            ),
            ['page' => 2],
            $token
        );

        $this->assertCount(2 + 1, $response->json('data'));
    }
}
