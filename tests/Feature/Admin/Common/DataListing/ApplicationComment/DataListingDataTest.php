<?php

namespace Tests\Feature\Admin\Common\DataListing\ApplicationComment;

use App\Enums\Authorization\PermissionName;
use App\Models\Application;
use App\Models\ApplicationComment;
use App\Services\Common\DataListing\Entities\ClientApplicationCommentListing;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DataListingDataTest extends TestCase
{
    use DatabaseTransactions;

    public function testSuccess()
    {
        Application::factory()->create();

        [$token] = $this->getUserToken(PermissionName::ClientApplicationSave);

        $response = $this->postRequest(
            route('common.listing.data', ['entity' => 'application-comments']),
            [],
            $token
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'prev',
            'next',
            'page',
            'data',
            'total',
            'perPage',
            'pages',
        ]);
    }

    public function testSuccessPaginationSuccess()
    {
        [$token, $user] = $this->getUserToken(PermissionName::ClientApplicationSave);

        $application = Application::factory()->create([
            'company_id' => $user->company_id,
            'owner_id' => $user->id,
        ]);

        $reflectionClass = new \ReflectionClass(ClientApplicationCommentListing::class);
        $perPage = $reflectionClass->getConstant('PER_PAGE');
        $numberOfRows = $perPage * 2 - 1;

        ApplicationComment::factory()->count($numberOfRows)
            ->for($application)
            ->create();

        $response = $this->postRequest(
            route(
                'common.listing.data',
                ['entity' => 'application-comments'],
            ),
            [
                'page' => 1
            ],
            $token
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'prev',
            'next',
            'page',
            'data' =>
                ['*' => [
                    'id',
                    'stars',
                    'author_name',
                    'text',
                    'public_id'
                ]],
            'total',
            'perPage',
            'pages',
        ]);

        $this->assertCount($perPage, $response->json('data'));

        $response = $this->postRequest(
            route(
                'common.listing.data',
                ['entity' => 'application-comments'],
            ),
            [
                'page' => 2
            ],
            $token
        );
        $this->assertCount($numberOfRows - $perPage, $response->json('data'));
    }
}
