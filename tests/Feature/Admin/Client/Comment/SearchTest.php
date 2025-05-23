<?php

namespace Tests\Feature\Admin\Client\Comment;

use App\Enums\Application\Category;
use App\Enums\Application\Geo;
use App\Enums\Application\Language;
use App\Enums\Application\PlatformType;
use App\Enums\Application\WhiteType;
use App\Enums\Authorization\PermissionName;
use App\Enums\Domain\Status;
use App\Models\ApplicationComment;
use App\Models\Domain;
use Tests\TestCase;

class SearchTest extends TestCase
{
    public function testSuccess()
    {
        ApplicationComment::factory()->count(2)->create();
        [$token] = $this->getUserToken(PermissionName::ClientApplicationSave);

        $response = $this->getRequest(
            route('client.application.create'),
            $token
        );

        $response->assertStatus(200);
        $this->assertCount(Domain::query()->where('status', Status::Active->value)->count(), $response->json('domains'));
        $this->assertCount(count(Category::cases()), $response->json('categories'));
        $this->assertCount(count(PlatformType::cases()), $response->json('platforms'));
        $this->assertCount(count(WhiteType::cases()), $response->json('whiteTypes'));
        $this->assertCount(count(Geo::cases()), $response->json('geos'));
        $this->assertCount(count(Language::cases()), $response->json('languages'));
    }
}
