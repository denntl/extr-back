<?php

namespace Tests\Feature\Admin\Common\DataListing\PushNotification;

use App\Enums\Authorization\PermissionName;
use Tests\TestCase;

class DataListingSettingsTest extends TestCase
{
    public function testSuccess()
    {
        [$token]  = $this->getUserToken(PermissionName::ClientSinglePushNotificationRead);

        $response = $this->getRequest(
            route('common.listing.settings', ['entity' => 'push-single-notifications']),
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
