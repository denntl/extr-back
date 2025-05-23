<?php

namespace Tests\Feature\Admin\Client\Application;

use App\Enums\Application\Category;
use App\Enums\Application\Geo;
use App\Enums\Application\Language;
use App\Enums\Application\PlatformType;
use App\Enums\Application\WhiteType;
use App\Enums\Authorization\PermissionName;
use App\Enums\Application\Status;
use App\Models\Domain;
use App\Enums\Domain\Status as DomainStatus;
use Tests\TestCase;

class CreateTest extends TestCase
{
    public function testSuccess()
    {
        [$token] = $this->getUserToken(PermissionName::ClientApplicationSave);

        $response = $this->getRequest(
            route('client.application.create'),
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
        $this->assertArrayHasKey('registration', $response->json('postbacks'));
        $this->assertArrayHasKey('deposit', $response->json('postbacks'));
    }
}
