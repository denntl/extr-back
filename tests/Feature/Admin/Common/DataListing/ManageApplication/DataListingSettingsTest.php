<?php

namespace Tests\Feature\Admin\Common\DataListing\ManageApplication;

use App\Enums\Authorization\PermissionName;
use App\Enums\DataListing\EntityName;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DataListingSettingsTest extends TestCase
{
    use DatabaseTransactions;

    public function testSuccess()
    {
        [$token] = $this->getUserToken(PermissionName::ManageApplicationRead);

        $response = $this->getRequest(
            route('common.listing.settings', ['entity' => EntityName::ManageApplication->value]),
            $token
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'columns',
            'listItems',
            'sorting',
            'filters',
        ]);

        $this->assertEquals(['column' => 'id', 'state' => 'desc'], $response->json('sorting'));
    }
}
