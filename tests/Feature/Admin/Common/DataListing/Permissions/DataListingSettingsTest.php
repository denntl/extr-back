<?php

namespace Tests\Feature\Admin\Common\DataListing\Permissions;

use App\Enums\Authorization\PermissionName;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DataListingSettingsTest extends TestCase
{
    use DatabaseTransactions;

    public function testSuccess()
    {
        [$token] = $this->getUserToken(PermissionName::ManagePermissionRead);

        $response = $this->getRequest(
            route('common.listing.settings', ['entity' => 'permissions']),
            $token
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'columns' => [
                '*' => [
                    'label',
                    'name',
                    'type',
                    'listName',
                    'isSortable',
                    'isSearchable',
                    'isFilterable',
                    'isVisible',
                ]
            ],
            'listItems',
            'sorting',
            'filters',
        ]);

        $this->assertEquals(['column' => 'id', 'state' => 'asc'], $response->json('sorting'));
    }
}
