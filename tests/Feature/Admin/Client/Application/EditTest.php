<?php

namespace Tests\Feature\Admin\Client\Application;

use App\Enums\Application\Category;
use App\Enums\Application\Geo;
use App\Enums\Application\Language;
use App\Enums\Application\PlatformType;
use App\Enums\Application\WhiteType;
use App\Enums\Authorization\PermissionName;
use App\Enums\Application\Status;
use App\Enums\Domain\Status as DomainStatus;
use App\Models\Application;
use App\Models\Domain;
use App\Models\User;
use Tests\TestCase;

class EditTest extends TestCase
{
    public function testSuccess()
    {
        [$token, $user, $company] = $this->getUserToken(PermissionName::ClientApplicationSave);

        $application = Application::factory()->create([
            'owner_id' => $user->id,
            'created_by_id' => $user->id,
            'company_id' => $company->id,
        ]);

        $response = $this->getRequest(
            route('client.application.edit', ['id' => $application->public_id]),
            $token
        );

        $response->assertStatus(200);
        $this->assertCount(Domain::query()->where('status', DomainStatus::Active->value)->count(), $response->json('domains'));
        $this->assertCount(count(Category::cases()), $response->json('categories'));
        $this->assertCount(count(PlatformType::cases()), $response->json('platforms'));
        $this->assertCount(count(WhiteType::cases()), $response->json('whiteTypes'));
        $this->assertCount(count(Geo::cases()), $response->json('geos'));
        $this->assertCount(count(Language::cases()), $response->json('languages'));
        $this->assertCount(count(Status::cases()), $response->json('statuses'));
        $this->assertCount(User::query()->where('company_id', $user->company_id)->count(), $response->json('owners'));
        $this->assertArrayHasKey('registration', $response->json('postbacks'));
        $this->assertArrayHasKey('deposit', $response->json('postbacks'));
        $expectedModel = [
            'status' => $application->status,
            'public_id' => $application->public_id,
            'name' => $application->name,
            'domain_id' => $application->domain_id,
            'uuid' => $application->uuid,
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
            'downloads_count' => (string) $application->downloads_count,
            'rating' => (string) $application->rating,
            'onesignal_id' => $application->onesignal_id,
            'onesignal_name' => $application->onesignal_name,
            'onesignal_auth_key' => $application->onesignal_auth_key,
            'owner_id' => $application->owner_id,
            'display_top_bar' => $application->display_top_bar,
            'full_domain' => $application->subdomain . '.' . $application->domain->domain,
            'display_app_bar' => $application->display_app_bar,
            'files' => [],
            'topApplicationIds' => [],
            'applicationGeoLanguages' => [],
        ];

        $this->assertEquals($expectedModel, $response->json('application'));
    }
}
