<?php

namespace Tests\Feature\Admin\Client\Application;

use App\Enums\Application\Category;
use App\Enums\Application\Status;
use App\Enums\Authorization\PermissionName;
use App\Models\Domain;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;
use Illuminate\Support\Facades\Http;

class StoreTest extends TestCase
{
    public function testSuccessBaseNew()
    {
        [$token] = $this->getUserToken(PermissionName::ClientApplicationSave);
        $domain = Domain::factory()->create();
        Config::set('services.onesignal.api_organization_key', 'mocked_key');
        Config::set('services.onesignal.api_organization_id', 'mocked_id');
        $appId = 'mocked_app_id';
        $appName = 'mocked_app_name';
        $appApiKey = 'mocked_api_key';
        Http::fake([
            'https://api.onesignal.com/apps' => Http::response(['id' => $appId, 'name' => $appName], 200),
            'https://api.onesignal.com/apps/' . $appId . '/auth/tokens' => Http::response(['token_id' => '123', 'formatted_token' => $appApiKey], 200),
        ]);

        $response = $this->postRequest(
            route('client.application.store', ['stage' => 'base']),
            [
                'name' => 'Test name',
                'domain_id' => $domain->id,
                'subdomain' => 'test',
                'pixel_id' => 'test',
                'pixel_key' => 'test',
                'link' => 'test',
                'category' => Category::Gambling,
                'app_name' => 'test',
                'status' => Status::Active,
                'applicationGeoLanguages' => [
                    [
                        'geo' => 'TR',
                        'language' => 'UA',
                    ]
                ]
            ],
            $token
        );

        $response->assertStatus(200);
        $this->assertDatabaseHas('applications', [
            'name' => 'Test name',
            'domain_id' => $domain->id,
            'subdomain' => 'test',
            'pixel_id' => 'test',
            'pixel_key' => 'test',
            'link' => 'test',
            'category' => Category::Gambling,
            'app_name' => 'test',
            'status' => Status::Active,
            'onesignal_id' => $appId,
            'onesignal_name' => $appName,
            'onesignal_auth_key' => $appApiKey,
        ]);
        $this->assertDatabaseHas('application_geo_languages', [
            'geo' => 'TR',
            'language' => 'UA',
        ]);
    }
}
