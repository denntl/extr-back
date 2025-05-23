<?php

namespace Tests\Feature\Admin\Client\OnesignalTemplate;

use App\Enums\Authorization\PermissionName;
use App\Enums\PushNotification\Type;
use App\Jobs\OnesignalDelivery;
use App\Models\Application;
use App\Models\Geo;
use App\Models\OnesignalTemplate;
use App\Models\OnesignalTemplateContents;
use App\Models\OnesignalTemplateSingleSettings;
use App\Services\Common\OnesignalTemplate\OnesignalDeliveryService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    public function testUpdateSingleNotificationsInThirtyMinutesSuccess()
    {
        [$token, $user] = $this->getUserToken(PermissionName::ClientPushNotificationCreate);
        $application1 = Application::factory()->create(['company_id' => $user->company_id, 'created_by_id' => $user->id, 'owner_id' => $user->id]);
        $application2 = Application::factory()->create(['company_id' => $user->company_id, 'created_by_id' => $user->id, 'owner_id' => $user->id]);
        $application3 = Application::factory()->create(['company_id' => $user->company_id, 'created_by_id' => $user->id, 'owner_id' => $user->id]);
        $geo1 = Geo::factory()->create();
        $geo2 = Geo::factory()->create();
        $template1 = OnesignalTemplate::factory()->create([
            'name' => 'test onesignal template',
            'type' => 1,
            'is_active' => false,
            'segments' => ['dep'],
            'created_by' => $user->id
        ]);
        $template1->applications()->sync([$application1->id, $application2->id, $application3->id]);
        $contents1 = OnesignalTemplateContents::factory()->create([
            'onesignal_template_id' => $template1->id,
            'geo_id' => $geo1->id,
            'title' => 'test title1',
            'text' => 'test text1',
            'image' => 'image1.jpg',
        ]);
        $scheduled_at = Carbon::now()->addMinutes(30)->format('Y-m-d H:i:s');
        $settings1 = OnesignalTemplateSingleSettings::factory()->create([
            'onesignal_template_id' => $template1->id,
            'scheduled_at' => $scheduled_at
        ]);

        $response = $this->postRequest(
            route('client.onesignal-template.update', ['id' => $template1->id]),
            [
                'name' => 'name updated',
                'type' => Type::Single->value, //todo: test that in single we don't change type
                'scheduled_at' => $scheduled_at,
                'is_active' => true,
                'application_ids' => [$application1->public_id, $application2->public_id, $application3->public_id],
                'geos' => [$geo1->id, $geo2->id],
                'segments' => ['install', 'dep', 'reg'],
                'contents' => [
                    [
                        'geo' => $geo1->id,
                        'text' => 'updated text',
                        'title' => 'updated title',
                        'image' => 'test1-upd.jpg'
                    ]
                ],
            ],
            $token,
        );

        $response->assertStatus(200);
        $template = OnesignalTemplate::first();
        $this->assertNotNull($template);
        $this->assertEquals('name updated', $template->name);
        $this->assertEquals(Type::Single->value, $template->type);
        $this->assertTrue($template->is_active);
        $this->assertEquals(['install', 'dep', 'reg'], $template->segments);
        $this->assertEquals($user->id, $template->created_by);

        $this->assertDatabaseHas('onesignal_templates_applications', [
            'onesignal_template_id' => $template->id,
            'application_id' => $application1->id,
        ]);
        $this->assertDatabaseHas('onesignal_templates_applications', [
            'onesignal_template_id' => $template->id,
            'application_id' => $application2->id,
        ]);
        $this->assertDatabaseHas('onesignal_templates_applications', [
            'onesignal_template_id' => $template->id,
            'application_id' => $application3->id,
        ]);

        $this->assertDatabaseHas('onesignal_templates_contents', [
            'onesignal_template_id' => $template->id,
            'geo_id' => $geo1->id,
            'text' => 'updated text',
            'title' => 'updated title',
            'image' => 'test1-upd.jpg'
        ]);

        $this->assertDatabaseMissing('onesignal_templates_contents', [
            'onesignal_template_id' => $template->id,
            'geo_id' => $geo2->id
        ]);

        $this->assertDatabaseHas('onesignal_templates_single_settings', [
            'onesignal_template_id' => $template->id,
            'scheduled_at' => $scheduled_at,
            'handled_at' => null
        ]);
    }
    public function testUpdateSingleNotificationsInTwoMinutesChangeNotActiveToActiveSuccess()
    {
        Config::set('services.onesignal.api_organization_key', 'mocked_key');
        Config::set('services.onesignal.api_organization_id', 'mocked_id');
        $appId = 'dfaf3575-da64-4c56-9d45-c64054f28856';
        $appName = 'mocked_app_name';
        $appApiKey = 'mocked_api_key';

        Http::fake([
            'https://api.onesignal.com/notifications' => Http::response(['id' => $appId, 'name' => $appName], 200),
        ]);
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
            'is_active' => false,
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
        $scheduled_at = Carbon::now()->addMinutes(2)->format('Y-m-d H:i:s');
        $settings1 = OnesignalTemplateSingleSettings::factory()->create([
            'onesignal_template_id' => $template1->id,
            'scheduled_at' => $scheduled_at,
        ]);

        $response = $this->postRequest(
            route('client.onesignal-template.update', ['id' => $template1->id]),
            [
                'name' => 'name updated',
                'type' => Type::Single->value, //todo: test that in single we don't change type
                'scheduled_at' => $scheduled_at,
                'is_active' => true,
                'application_ids' => [$application1->public_id, $application2->public_id],
                'geos' => [$geo1->id],
                'segments' => ['install', 'dep', 'reg'],
                'contents' => [
                    [
                        'geo' => $geo1->id,
                        'text' => 'updated text',
                        'title' => 'updated title',
                        'image' => 'test1-upd.jpg'
                    ]
                ],
            ],
            $token,
        );

        $response->assertStatus(200);
        $template = OnesignalTemplate::first();
        $this->assertNotNull($template);
        $this->assertEquals('name updated', $template->name);
        $this->assertEquals(Type::Single->value, $template->type);
        $this->assertTrue($template->is_active);
        $this->assertEquals(['install', 'dep', 'reg'], $template->segments);
        $this->assertEquals($user->id, $template->created_by);

        $this->assertDatabaseHas('onesignal_templates_contents', [
            'onesignal_template_id' => $template->id,
            'geo_id' => $geo1->id,
            'text' => 'updated text',
            'title' => 'updated title',
            'image' => 'test1-upd.jpg'
        ]);

//        Queue::assertPushed(OnesignalDelivery::class, 2);

        /**
         * @var OnesignalDeliveryService $deliveryService
         */
        $deliveryService = app(OnesignalDeliveryService::class);
        $templateArray = $deliveryService->getAllTemplatesById($template->id, Type::Single)->first();
        $job = (new OnesignalDelivery($deliveryService->getAllTemplatesByIdDTO($templateArray)))->withFakeQueueInteractions();
        $job->handle($deliveryService);

        $this->assertDatabaseHas('onesignal_templates_single_settings', [
            'onesignal_template_id' => $template->id,
            'scheduled_at' => $scheduled_at,
        ]);
        $this->assertDatabaseMissing('onesignal_templates_single_settings', [
            'onesignal_template_id' => $template->id,
            'scheduled_at' => $scheduled_at,
            'handled_at' => null
        ]);
    }
    public function testUpdateSingleNotificationsInSixMinutesChangeNotActiveToActiveSuccess()
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
            'is_active' => false,
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
        $scheduled_at = Carbon::now()->addMinutes(2)->format('Y-m-d H:i:s');
        $settings1 = OnesignalTemplateSingleSettings::factory()->create([
            'onesignal_template_id' => $template1->id,
            'scheduled_at' => $scheduled_at,
        ]);

        $scheduled_at_six = Carbon::now()->addMinutes(6)->format('Y-m-d H:i:s');
        $response = $this->postRequest(
            route('client.onesignal-template.update', ['id' => $template1->id]),
            [
                'name' => 'name updated',
                'type' => Type::Single->value, //todo: test that in single we don't change type
                'scheduled_at' => $scheduled_at_six,
                'is_active' => true,
                'application_ids' => [$application1->public_id, $application2->public_id],
                'geos' => [$geo1->id],
                'segments' => ['install', 'dep', 'reg'],
                'contents' => [
                    [
                        'geo' => $geo1->id,
                        'text' => 'updated text',
                        'title' => 'updated title',
                        'image' => 'test1-upd.jpg'
                    ]
                ],
            ],
            $token,
        );

        $response->assertStatus(200);
        $template = OnesignalTemplate::first();
        $this->assertNotNull($template);
        $this->assertEquals('name updated', $template->name);
        $this->assertEquals(Type::Single->value, $template->type);
        $this->assertTrue($template->is_active);
        $this->assertEquals(['install', 'dep', 'reg'], $template->segments);
        $this->assertEquals($user->id, $template->created_by);

        $this->assertDatabaseHas('onesignal_templates_contents', [
            'onesignal_template_id' => $template->id,
            'geo_id' => $geo1->id,
            'text' => 'updated text',
            'title' => 'updated title',
            'image' => 'test1-upd.jpg'
        ]);

        Queue::assertPushed(OnesignalDelivery::class, 0);

        $this->assertDatabaseHas('onesignal_templates_single_settings', [
            'onesignal_template_id' => $template->id,
            'scheduled_at' => $scheduled_at_six,
            'handled_at' => null
        ]);
    }
    public function testUpdateSingleNotificationsChangeHandledNotificationFails()
    {
        Config::set('services.onesignal.api_organization_key', 'mocked_key');
        Config::set('services.onesignal.api_organization_id', 'mocked_id');
        $appId = 'dfaf3575-da64-4c56-9d45-c64054f28856';
        $appName = 'mocked_app_name';
        $appApiKey = 'mocked_api_key';

        Http::fake([
            'https://api.onesignal.com/notifications' => Http::response(['id' => $appId, 'name' => $appName], 200),
        ]);
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
        $scheduled_at = Carbon::now()->addMinutes(2)->format('Y-m-d H:i:s');
        $settings1 = OnesignalTemplateSingleSettings::factory()->create([
            'onesignal_template_id' => $template1->id,
            'scheduled_at' => $scheduled_at,
            'handled_at' => $scheduled_at,
        ]);

        $scheduled_at_six = Carbon::now()->addMinutes(6)->format('Y-m-d H:i:s');
        $response = $this->postRequest(
            route('client.onesignal-template.update', ['id' => $template1->id]),
            [
                'name' => 'name updated',
                'type' => Type::Single->value, //todo: test that in single we don't change type
                'scheduled_at' => $scheduled_at_six,
                'is_active' => false,
                'application_ids' => [$application1->public_id, $application2->public_id],
                'geos' => [$geo1->id],
                'segments' => ['install', 'dep', 'reg'],
                'contents' => [
                    [
                        'geo' => $geo1->id,
                        'text' => 'updated text',
                        'title' => 'updated title',
                        'image' => 'test1-upd.jpg'
                    ]
                ],
            ],
            $token,
        );

        $response->assertStatus(500);
        $response->assertJson(["error" => "You can't update template in 5 minutes to be sent"]);
        $template = OnesignalTemplate::first();
        $this->assertNotNull($template);
        $this->assertEquals('test onesignal template', $template->name);
        $this->assertEquals(Type::Single->value, $template->type);
        $this->assertTrue($template->is_active);
        $this->assertEquals(['dep'], $template->segments);
        $this->assertEquals($user->id, $template->created_by);

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
            'scheduled_at' => $scheduled_at,
            'handled_at' => $scheduled_at
        ]);
    }
}
