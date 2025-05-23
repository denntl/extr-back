<?php

namespace Tests\Feature\Admin\Client\OnesignalTemplate;

use App\Enums\Authorization\PermissionName;
use App\Enums\PushNotification\Type;
use App\Jobs\OnesignalDelivery;
use App\Models\Application;
use App\Models\Geo;
use App\Models\OnesignalTemplate;
use App\Services\Common\OnesignalTemplate\OnesignalDeliveryService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class StoreTest extends TestCase
{
    public function testCreateSingleNotificationsIn30MinutesSuccess()
    {
        [$token, $user] = $this->getUserToken(PermissionName::ClientPushNotificationCreate);
        $application1 = Application::factory()->create(['company_id' => $user->company_id, 'created_by_id' => $user->id, 'owner_id' => $user->id]);
        $application2 = Application::factory()->create(['company_id' => $user->company_id, 'created_by_id' => $user->id, 'owner_id' => $user->id]);
        $application3 = Application::factory()->create(['company_id' => $user->company_id, 'created_by_id' => $user->id, 'owner_id' => $user->id]);
        $geo1 = Geo::factory()->create();
        $geo2 = Geo::factory()->create();
        $scheduled_at = Carbon::now()->addMinutes(30)->format('Y-m-d H:i:s');

        $response = $this->postRequest(
            route('client.onesignal-template.store'),
            [
                'name' => 'test onesignal template',
                'type' => Type::Single->value,
                'scheduled_at' => $scheduled_at,
                'is_active' => true,
                'application_ids' => [$application1->public_id, $application2->public_id, $application3->public_id],
                'geos' => [$geo1->id, $geo2->id],
                'segments' => ['install', 'dep', 'reg'],
                'contents' => [
                    [
                        'geo' => $geo1->id,
                        'text' => 'test1 text',
                        'title' => 'test1 title',
                        'image' => 'test1.jpg'
                    ],
                    [
                        'geo' => $geo2->id,
                        'text' => 'test2 text',
                        'title' => 'test2 title',
                        'image' => 'test2.jpg'
                    ]
                ],
            ],
            $token,
        );

        $response->assertStatus(200);
        $template = OnesignalTemplate::first();
        $this->assertNotNull($template);
        $this->assertEquals('test onesignal template', $template->name);
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
            'text' => 'test1 text',
            'title' => 'test1 title',
            'image' => 'test1.jpg'
        ]);
        $this->assertDatabaseHas('onesignal_templates_contents', [
            'onesignal_template_id' => $template->id,
            'geo_id' => $geo2->id,
            'text' => 'test2 text',
            'title' => 'test2 title',
            'image' => 'test2.jpg'
        ]);

        $this->assertDatabaseHas('onesignal_templates_single_settings', [
            'onesignal_template_id' => $template->id,
            'scheduled_at' => $scheduled_at,
            'handled_at' => null
        ]);
    }
    public function testCreateSingleNotificationsIn2MinutesSuccess()
    {
        Config::set('services.onesignal.api_organization_key', 'mocked_key');
        Config::set('services.onesignal.api_organization_id', 'mocked_id');
        $appId = 'dfaf3575-da64-4c56-9d45-c64054f28856';
        $appName = 'mocked_app_name';
        $appApiKey = 'mocked_api_key';

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
        $application3 = Application::factory()->create([
            'company_id' => $user->company_id,
            'created_by_id' => $user->id,
            'owner_id' => $user->id,
            'onesignal_id' => $appId,
            'onesignal_name' => $appName,
            'onesignal_auth_key' => $appApiKey,
            ]);
        $geo1 = Geo::factory()->create();
        $geo2 = Geo::factory()->create();
        $scheduled_at = Carbon::now()->addSeconds(5 * 60)->format('Y-m-d H:i:s');

        Http::fake([
            'https://api.onesignal.com/notifications' => Http::response(['id' => $appId, 'name' => $appName], 200),
        ]);
        Queue::fake();

        $response = $this->postRequest(
            route('client.onesignal-template.store'),
            [
                'name' => 'test onesignal template',
                'type' => Type::Single->value,
                'scheduled_at' => $scheduled_at,
                'is_active' => true,
                'application_ids' => [$application1->public_id, $application2->public_id, $application3->public_id],
                'geos' => [$geo1->id, $geo2->id],
                'segments' => ['install', 'dep', 'reg'],
                'contents' => [
                    [
                        'geo' => $geo1->id,
                        'text' => 'test1 text',
                        'title' => 'test1 title',
                        'image' => 'test1.jpg'
                    ],
                    [
                        'geo' => $geo2->id,
                        'text' => 'test2 text',
                        'title' => 'test2 title',
                        'image' => 'test2.jpg'
                    ]
                ],
            ],
            $token,
        );

        $response->assertStatus(200);
        $template = OnesignalTemplate::first();
        $this->assertNotNull($template);
        $this->assertEquals('test onesignal template', $template->name);
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
            'text' => 'test1 text',
            'title' => 'test1 title',
            'image' => 'test1.jpg'
        ]);
        $this->assertDatabaseHas('onesignal_templates_contents', [
            'onesignal_template_id' => $template->id,
            'geo_id' => $geo2->id,
            'text' => 'test2 text',
            'title' => 'test2 title',
            'image' => 'test2.jpg'
        ]);

//        Queue::assertPushed(OnesignalDelivery::class, 6);

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
            'handled_at' => null,
        ]);

        $this->assertDatabaseHas('onesignal_notifications', [
            'onesignal_notification_id' => $appId,
            'onesignal_template_id' => $template->id,
            'application_id' => $application1->id,
            'geo_id' => $geo1->id,
        ]);
    }
    public function testCreateSingleNotificationsIn2MinutesNotActiveSuccess()
    {
        Config::set('services.onesignal.api_organization_key', 'mocked_key');
        Config::set('services.onesignal.api_organization_id', 'mocked_id');
        $appId = 'dfaf3575-da64-4c56-9d45-c64054f28856';
        $appName = 'mocked_app_name';
        $appApiKey = 'mocked_api_key';

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
        $application3 = Application::factory()->create([
            'company_id' => $user->company_id,
            'created_by_id' => $user->id,
            'owner_id' => $user->id,
            'onesignal_id' => $appId,
            'onesignal_name' => $appName,
            'onesignal_auth_key' => $appApiKey,
            ]);
        $geo1 = Geo::factory()->create();
        $geo2 = Geo::factory()->create();
        $scheduled_at = Carbon::now()->addSeconds(5 * 60)->format('Y-m-d H:i:s');

        Http::fake([
            'https://api.onesignal.com/notifications' => Http::response(['id' => $appId, 'name' => $appName], 200),
        ]);
        Queue::fake();

        $response = $this->postRequest(
            route('client.onesignal-template.store'),
            [
                'name' => 'test onesignal template',
                'type' => Type::Single->value,
                'scheduled_at' => $scheduled_at,
                'is_active' => false,
                'application_ids' => [$application1->public_id, $application2->public_id, $application3->public_id],
                'geos' => [$geo1->id, $geo2->id],
                'segments' => ['install', 'dep', 'reg'],
                'contents' => [
                    [
                        'geo' => $geo1->id,
                        'text' => 'test1 text',
                        'title' => 'test1 title',
                        'image' => 'test1.jpg'
                    ],
                    [
                        'geo' => $geo2->id,
                        'text' => 'test2 text',
                        'title' => 'test2 title',
                        'image' => 'test2.jpg'
                    ]
                ],
            ],
            $token,
        );

        $response->assertStatus(200);
        $template = OnesignalTemplate::first();
        $this->assertNotNull($template);
        $this->assertEquals('test onesignal template', $template->name);
        $this->assertEquals(Type::Single->value, $template->type);
        $this->assertFalse($template->is_active);
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
            'text' => 'test1 text',
            'title' => 'test1 title',
            'image' => 'test1.jpg'
        ]);
        $this->assertDatabaseHas('onesignal_templates_contents', [
            'onesignal_template_id' => $template->id,
            'geo_id' => $geo2->id,
            'text' => 'test2 text',
            'title' => 'test2 title',
            'image' => 'test2.jpg'
        ]);

        Queue::assertNotPushed(OnesignalDelivery::class);

        $this->assertDatabaseHas('onesignal_templates_single_settings', [
            'onesignal_template_id' => $template->id,
            'scheduled_at' => $scheduled_at,
            'handled_at' => null,
        ]);

        $this->assertDatabaseMissing('onesignal_notifications', [
            'onesignal_notification_id' => $appId,
            'onesignal_template_id' => $template->id,
            'application_id' => $application1->id,
            'geo_id' => $geo1->id,
        ]);
    }
}
