<?php

namespace Tests\Feature\Admin\Manage\Application;

use App\Enums\Application\Status;
use App\Enums\Authorization\PermissionName;
use App\Models\Application;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CloneTest extends TestCase
{
    public function testSuccess()
    {
        [$token, $user] = $this->getUserToken(PermissionName::ManageApplicationSave);

        $appId = 'mocked_app_id';
        $appName = 'mocked_app_name';
        $appApiKey = 'mocked_api_key';
        $application = Application::factory()->create([
            'company_id' => $user->company_id,
            'created_by_id' => $user->id,
            'owner_id' => $user->id,
            'onesignal_id' => $appId,
            'onesignal_name' => $appName,
            'onesignal_auth_key' => $appApiKey,
        ]);

        Config::set('services.onesignal.api_organization_key', 'mocked_key');
        Config::set('services.onesignal.api_organization_id', 'mocked_id');
        Http::fake([
            'https://api.onesignal.com/apps' => Http::response(['id' => $appId, 'name' => 'copy-' . $appName], 200),
            'https://api.onesignal.com/apps/' . $appId . '/auth/tokens' => Http::response(['token_id' => '123', 'formatted_token' => 'new-' . $appApiKey], 200),
        ]);

        $response = $this->postRequest(route('manage.application.clone', ['id' => $application->id]), [], $token);

        $response->assertStatus(200);

        $this->assertDatabaseHas('applications', [
            'status' => $application->status,
            'company_id' => $application->company_id,
            'created_by_id' => $application->created_by_id,
            'owner_id' => $application->owner_id,
            'name' => $application->name,
            'domain_id' => $application->domain_id,
            'subdomain' => $application->subdomain,
            'pixel_id' => $application->pixel_id,
            'pixel_key' => $application->pixel_key,
            'link' => $application->link,
            'platform_type' => $application->platform_type,
            'landing_type' => $application->landing_type,
            'white_type' => $application->white_type,
            'category' => $application->category,
            'app_name' => $application->app_name,
            'developer_name' => $application->developer_name,
            'icon' => $application->icon,
            'description' => $application->description,
            'downloads_count' => $application->downloads_count,
            'rating' => $application->rating,
            'onesignal_id' => $application->onesignal_id,
            'onesignal_name' => $application->onesignal_name,
            'onesignal_auth_key' => $application->onesignal_auth_key,
        ]);
        $this->assertDatabaseHas('applications', [
            'id' => $application->id + 1,
            'status' => Status::NotActive,
            'company_id' => $application->company_id,
            'created_by_id' => $application->created_by_id,
            'owner_id' => $application->owner_id,
            'name' => $application->name,
            'domain_id' => $application->domain_id,
            'subdomain' => 'copy-' . $application->subdomain,
            'full_domain' => 'copy-' . $application->full_domain,
            'pixel_id' => $application->pixel_id,
            'pixel_key' => $application->pixel_key,
            'link' => $application->link,
            'platform_type' => $application->platform_type,
            'landing_type' => $application->landing_type,
            'white_type' => $application->white_type,
            'category' => $application->category,
            'app_name' => $application->app_name,
            'developer_name' => $application->developer_name,
            'icon' => $application->icon,
            'description' => $application->description,
            'downloads_count' => $application->downloads_count,
            'rating' => $application->rating,
            'onesignal_id' => $application->onesignal_id,
            'onesignal_name' => 'copy-' . $application->onesignal_name,
            'onesignal_auth_key' => 'new-' . $application->onesignal_auth_key,
        ]);
    }
}
