<?php

namespace Tests\Feature\Admin\Common\DataListing\Role;

use App\Enums\Authorization\PermissionName;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DataListingSettingsTest extends TestCase
{
    use DatabaseTransactions;

    public function testSuccess()
    {
        [$token] = $this->getUserToken(PermissionName::ManageRoleRead);

        $response = $this->getRequest(
            route('common.listing.settings', ['entity' => 'roles']),
            $token
        );
        ;

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'columns',
            'listItems',
            'sorting',
            'filters',
        ]);

        $this->assertEquals(['column' => 'id', 'state' => 'asc'], $response->json('sorting'));

        $this->assertCount(4, $response->json('columns'));
    }
}
