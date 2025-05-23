<?php

namespace Tests\Feature\Admin\Client\OnesignalTemplate;

use App\Jobs\OnesignalDelivery;
use App\Models\Geo;
use App\Enums\Authorization\PermissionName;
use App\Enums\PushNotification\Type;
use App\Models\Application;
use App\Models\OnesignalTemplate;
use App\Models\OnesignalTemplateContents;
use App\Models\OnesignalTemplateSingleSettings;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class DeleteTest extends TestCase
{
    public function testDeleteSingleNotificationsInSixMinutesBeforeSendSuccess()
    {
        Config::set('services.onesignal.api_organization_key', 'mocked_key');
        Config::set('services.onesignal.api_organization_id', 'mocked_id');
        $appId = 'dfaf3575-da64-4c56-9d45-c64054f28856';
        $appName = 'mocked_app_name';
        $appApiKey = 'mocked_api_key';

        Queue::fake();

        [$token, $user] = $this->getUserToken(PermissionName::ClientPushNotificationCreate);

        $application1 = Application::factory()->create([
            'company_id' => $user->company_id,
            'created_by_id' => $user->id,
            'owner_id' => $user->id,
            'onesignal_id' => $appId,
            'onesignal_name' => $appName,
            'onesignal_auth_key' => $appApiKey,
        ]);
        $application2 = Application::factory()->create([
            'company_id' => $user->company_id,
            'created_by_id' => $user->id,
            'owner_id' => $user->id,
            'onesignal_id' => $appId,
            'onesignal_name' => $appName,
            'onesignal_auth_key' => $appApiKey,
        ]);
        $geo1 = Geo::factory()->create();
        $template1 = OnesignalTemplate::factory()->create([
            'name' => 'test onesignal template',
            'type' => 1,
            'is_active' => true,
            'segments' => ['dep'],
            'created_by' => $user->id
        ]);
        $template1->applications()->sync([$application1->id, $application2->id]);
        $contents1 = OnesignalTemplateContents::factory()->create([
            'onesignal_template_id' => $template1->id,
            'geo_id' => $geo1->id,
            'title' => 'test title1',
            'text' => 'test text1',
            'image' => 'image1.jpg',
        ]);
        $scheduled_at = Carbon::now()->addMinutes(6)->format('Y-m-d H:i:s');
        $settings1 = OnesignalTemplateSingleSettings::factory()->create([
            'onesignal_template_id' => $template1->id,
            'scheduled_at' => $scheduled_at,
            'handled_at' => null
        ]);

        $scheduled_at_six = Carbon::now()->addMinutes(6)->format('Y-m-d H:i:s');
        $response = $this->postRequest(
            route('client.onesignal-template.delete', ['id' => $template1->id]),
            [],
            $token,
        );

        $response->assertStatus(200);
        $template = OnesignalTemplate::onlyTrashed()->first();
        $this->assertNotNull($template);
        $this->assertEquals('test onesignal template', $template->name);
        $this->assertEquals(Type::Single->value, $template->type);
        $this->assertFalse($template->is_active);
        $this->assertEquals(['dep'], $template->segments);
        $this->assertEquals($user->id, $template->created_by);

        $this->assertSoftDeleted('onesignal_templates', ['id' => $template->id]);

        $this->assertDatabaseHas('onesignal_templates_contents', [
            'onesignal_template_id' => $template->id,
            'geo_id' => $geo1->id,
            'text' => 'test text1',
            'title' => 'test title1',
            'image' => 'image1.jpg'
        ]);

        Queue::assertPushed(OnesignalDelivery::class, 0);

        $this->assertDatabaseHas('onesignal_templates_single_settings', [
            'onesignal_template_id' => $template->id,
            'scheduled_at' => $scheduled_at_six,
            'handled_at' => null
        ]);
    }
    public function testDeleteSingleNotificationsInThreeMinutesBeforeSendFails()
    {
        Config::set('services.onesignal.api_organization_key', 'mocked_key');
        Config::set('services.onesignal.api_organization_id', 'mocked_id');
        $appId = 'dfaf3575-da64-4c56-9d45-c64054f28856';
        $appName = 'mocked_app_name';
        $appApiKey = 'mocked_api_key';

        Queue::fake();

        [$token, $user] = $this->getUserToken(PermissionName::ClientPushNotificationCreate);

        $application1 = Application::factory()->create([
            'company_id' => $user->company_id,
            'created_by_id' => $user->id,
            'owner_id' => $user->id,
            'onesignal_id' => $appId,
            'onesignal_name' => $appName,
            'onesignal_auth_key' => $appApiKey,
        ]);
        $application2 = Application::factory()->create([
            'company_id' => $user->company_id,
            'created_by_id' => $user->id,
            'owner_id' => $user->id,
            'onesignal_id' => $appId,
            'onesignal_name' => $appName,
            'onesignal_auth_key' => $appApiKey,
        ]);
        $geo1 = Geo::factory()->create();
        $template1 = OnesignalTemplate::factory()->create([
            'name' => 'test onesignal template',
            'type' => 1,
            'is_active' => true,
            'segments' => ['dep'],
            'created_by' => $user->id
        ]);
        $template1->applications()->sync([$application1->id, $application2->id]);
        $contents1 = OnesignalTemplateContents::factory()->create([
            'onesignal_template_id' => $template1->id,
            'geo_id' => $geo1->id,
            'title' => 'test title1',
            'text' => 'test text1',
            'image' => 'image1.jpg',
        ]);
        $scheduled_at = Carbon::now()->addMinutes(3)->format('Y-m-d H:i:s');
        OnesignalTemplateSingleSettings::factory()->create([
            'onesignal_template_id' => $template1->id,
            'scheduled_at' => $scheduled_at,
            'handled_at' => $scheduled_at
        ]);

        $response = $this->postRequest(
            route('client.onesignal-template.delete', ['id' => $template1->id]),
            [],
            $token,
        );

        $response->assertStatus(500);
        $response->assertJson(["error" => "You can't delete template in 5 minutes to be sent"]);
        $template = OnesignalTemplate::withTrashed()->first();
        $this->assertNotNull($template);
        $this->assertEquals('test onesignal template', $template->name);
        $this->assertEquals(Type::Single->value, $template->type);
        $this->assertTrue($template->is_active);
        $this->assertEquals(['dep'], $template->segments);
        $this->assertEquals($user->id, $template->created_by);

        Queue::assertPushed(OnesignalDelivery::class, 0);

        $this->assertDatabaseHas('onesignal_templates_single_settings', [
            'onesignal_template_id' => $template->id,
            'scheduled_at' => $scheduled_at,
            'handled_at' => $scheduled_at
        ]);
    }
}
