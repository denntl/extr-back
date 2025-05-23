<?php

namespace Tests\Feature\Admin\Common\DataListing\Role;

use App\Enums\Authorization\PermissionName;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DataListingDataTest extends TestCase
{
    use DatabaseTransactions;

    public function testSuccess()
    {
        [$token] = $this->getUserToken(PermissionName::ManageRoleRead);

        $response = $this->postRequest(
            route('common.listing.data', ['entity' => 'roles']),
            [],
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
                ]
            ],
            'total',
            'perPage',
            'pages',
        ]);
    }

    public function testSuccessQuery()
    {
        [$token] = $this->getUserToken(PermissionName::ManageRoleRead);

        Role::create(['name' => 'Test role']);
        Role::create(['name' => 'qwerty']);
        Role::create(['name' => 'qwerty1']);
        Role::create(['name' => 'qwerty3']);

        $response = $this->postRequest(
            route(
                'common.listing.data',
                ['entity' => 'roles'],
            ),
            [
                'page' => 1,
                'sorting' => [
                    'column' => 'id',
                    'state' => 'asc'
                ],
                'filters' => [],
                'query' => 'Test role'
            ],
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

        $this->assertEquals(1, count($response->json('data')));

        $this->assertEquals('Test role', $response->json('data')[0]['name']);
    }

    public function testSuccessFilter()
    {
        [$token] = $this->getUserToken(PermissionName::ManageRoleRead);

        Role::create(['name' => 'Руководитель компании']);
        Role::create(['name' => 'Руководитель команды']);
        Role::create(['name' => 'Admin команды']);

        $response = $this->postRequest(
            route(
                'common.listing.data',
                ['entity' => 'roles'],
            ),
            [
                "page" => 1,
                "sorting" => [
                    "column" => "id",
                    "state" => "asc"
                ],
                "filters" => [
                    "name" => [
                        "name" => "name",
                        "operator" => "contains",
                        "value" => "водитель"
                    ]
                ],
                "query" => ""
            ],
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

        $this->assertEquals(2, count($response->json('data')));
        $this->assertEquals('Руководитель компании', $response->json('data')[0]['name']);
        $this->assertEquals('Руководитель команды', $response->json('data')[1]['name']);
    }
}
