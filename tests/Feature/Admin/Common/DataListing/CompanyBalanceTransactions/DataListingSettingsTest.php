<?php

namespace Tests\Feature\Admin\Common\DataListing\CompanyBalanceTransactions;

use App\Enums\Authorization\PermissionName;
use App\Enums\DataListing\EntityName;
use Tests\TestCase;

class DataListingSettingsTest extends TestCase
{
    public function testSuccess()
    {
        [$token]  = $this->getUserToken(PermissionName::ManageCompanyBalanceTransactionsRead);

        $response = $this->getRequest(
            route('common.listing.settings', ['entity' => EntityName::ManageCompanyBalanceTransactions->value]),
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
