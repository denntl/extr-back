<?php

namespace Tests\Feature\Site\MainController;

use App\Enums\Application\Category;
use App\Enums\Application\Geo;
use App\Enums\Application\LandingType;
use App\Enums\Application\Language;
use App\Enums\Application\PlatformType;
use App\Enums\Application\Status;
use App\Enums\Application\WhiteType;
use App\Models\Application;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Tests\TestCase;

class PreviewPostTest extends TestCase
{
    use DatabaseTransactions;

    public function testPreview()
    {
        $request = new Request();
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
        $requestParams = [
            'geo' => Geo::UA->value,
            'platform_type' => PlatformType::Multi->value,
            'landing_type' => LandingType::New->value,
            'category' => Category::Finances->value,
            'white_type' => WhiteType::White->value,
            'name' => 'App test name',
            'description' => 'Test app description',
            'downloads_count' => '1010',
            'rating' => '3',
            'app_name' => 'Some app name',
            'developer_name' => 'some developer name',
            'language' => Language::En->value,
            'icon' => '/some-image.png',
            'display_top_bar' => false,
            'status' => Status::Active->value,
            'topApplicationIds' => [],
        ];

        $response = $this->post(route('previewPost'), $requestParams);
        $response->assertStatus(200);
        $response->assertViewIs('site.landings.new');
        $expectedParams = $requestParams;
        $expectedParams['uuid'] = 'uuid';
        $application = new Application($expectedParams);
        $response->assertViewHas('application', $application);

        $this->assertDatabaseEmpty('application_statistics');
        $this->assertDatabaseEmpty('pwa_clients');
        $this->assertDatabaseEmpty('pwa_client_clicks');
    }
}
