<?php

namespace Tests\Feature\Admin\Client\CompanyBalanceTransactions;

use App\Enums\Authorization\PermissionName;
use App\Enums\DataListing\EntityName;
use Tests\TestCase;

class DataListingSettingsTest extends TestCase
{
    public function testSuccess()
    {
        [$token]  = $this->getUserToken(PermissionName::ClientCompanyBalanceTransactionsRead);

        $response = $this->getRequest(
            route('common.listing.settings', ['entity' => EntityName::ClientMyCompanyBalanceTransactions->value]),
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

        $this->assertEquals(['column' => 'id', 'state' => 'desc'], $response->json('sorting'));
    }
}
