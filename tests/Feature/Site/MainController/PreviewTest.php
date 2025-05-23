<?php

namespace Tests\Feature\Site\MainController;

use App\Enums\Application\LandingType;
use App\Enums\Application\PlatformType;
use App\Enums\Application\Status;
use App\Models\Application;
use App\Models\ApplicationGeoLanguage;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Tests\TestCase;

class PreviewTest extends TestCase
{
    use DatabaseTransactions;

    public function testClickWasAdded()
    {
        /** @var Application $application */
        $application = Application::factory()->create([
            'status' => Status::Active->value,
            'platform_type' => PlatformType::Multi->value,
            'pixel_id' => 'test_pixel_id',
            'pixel_key' => 'test_pixel_key',
            'landing_type' => LandingType::Old->value,
            'uuid' => Str::uuid(),
        ]);

        ApplicationGeoLanguage::factory()->create([
            'application_id' => $application->id,
            'geo' => 'CA',
            'language' => 'EN',
        ]);

        $request = new Request();
        $request->headers->set('HOST', $application->full_domain);
        $request->headers->set('CF_IPCOUNTRY', 'CA');
        $request->headers->set('CF_CONNECTING_IP', '222.222.222.222');
        $request->headers->set('USER_AGENT', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36');
        $request->merge([
            'sub_id_1' => 'test1',
            'sub_id_2' => 'test2',
            'sub_id_3' => 'test3',
            'sub_id_4' => 'test4',
            'sub_id_5' => 'test5',
            'sub_id_6' => 'test6',
            'sub_id_7' => 'test7',
            'sub_id_8' => 'test8',
        ]);

        $request->cookies->set('_fbp', 'test_fb_p');
        $request->cookies->set('_fbc', 'test_fb_c');

        $this->instance(Request::class, $request);

        $response = $this->get(route('preview', ['appUuid' => $application->uuid]));
        $response->assertStatus(200);
        $response->assertViewIs('site.landings.old');

        $this->assertDatabaseEmpty('application_statistics');
        $this->assertDatabaseEmpty('pwa_clients');
        $this->assertDatabaseEmpty('pwa_client_clicks');
    }

    public function testWhitePage()
    {
        /** @var Application $application */
        $application = Application::factory()->create([
            'status' => Status::Active->value,
            'platform_type' => PlatformType::Multi->value,
            'pixel_id' => 'test_pixel_id',
            'pixel_key' => 'test_pixel_key',
        ]);

        ApplicationGeoLanguage::factory()->create([
            'application_id' => $application->id,
            'geo' => 'CA',
            'language' => 'EN',
        ]);

        $request = new Request();
        $request->headers->set('HOST', $application->full_domain);
        $request->headers->set('CF_IPCOUNTRY', 'IT');
        $request->headers->set('CF_CONNECTING_IP', '222.222.222.222');
        $request->headers->set('USER_AGENT', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36');
        $request->merge([
            'sub_id_1' => 'test1',
            'sub_id_2' => 'test2',
            'sub_id_3' => 'test3',
            'sub_id_4' => 'test4',
            'sub_id_5' => 'test5',
            'sub_id_6' => 'test6',
            'sub_id_7' => 'test7',
            'sub_id_8' => 'test8',
        ]);

        $request->cookies->set('_fbp', 'test_fb_p');
        $request->cookies->set('_fbc', 'test_fb_c');

        $this->instance(Request::class, $request);

        $response = $this->get(route('preview', ['appUuid' => Str::uuid()]));
        $response->assertStatus(200);
        $response->assertViewIs('site.landings.white-default');

        $this->assertDatabaseEmpty('application_statistics');
        $this->assertDatabaseEmpty('pwa_clients');
        $this->assertDatabaseEmpty('pwa_client_clicks');
    }
}
