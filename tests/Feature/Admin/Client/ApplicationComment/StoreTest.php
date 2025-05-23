<?php

namespace Tests\Feature\Admin\Client\ApplicationComment;

use App\Enums\Authorization\PermissionName;
use App\Models\Application;
use App\Models\ApplicationComment;
use Tests\TestCase;

class StoreTest extends TestCase
{
    public function testSuccess()
    {
        [$token, $user] = $this->getUserToken(PermissionName::ClientApplicationSave);
        /** @var Application $application */
        $application = Application::factory()->create([
            'company_id' => $user->company_id,
            'created_by_id' => $user->id,
            'owner_id' => $user->id,
        ]);

        $response = $this->postRequest(
            route('client.application.comment.store', ['id' => $application->public_id]),
            [
                'answer' => 'test answer',
                'answer_author' => 'test answer_author',
                'author_name' => 'test author_name',
                'icon' => 'test icon',
                'lang' => 'ua',
                'likes' => 23,
                'stars' => 4,
                'text' => 'test text',
            ],
            $token,
        );

        $response->assertStatus(200);

        $this->assertDatabaseHas('application_comments', [
            'application_id' => $application->id,
            'answer' => 'test answer',
            'answer_author' => 'test answer_author',
            'author_name' => 'test author_name',
            'icon' => 'test icon',
            'lang' => 'UA',
            'likes' => 23,
            'stars' => 4,
            'text' => 'test text',
        ]);
    }

    public function testHasNoAccessToApplication()
    {
        [$token] = $this->getUserToken(PermissionName::ClientApplicationSave);
        /** @var Application $application */
        $application = Application::factory()->create();

        $response = $this->postRequest(
            route('client.application.comment.store', ['id' => $application->public_id]),
            [
                'answer' => 'test answer',
                'answer_author' => 'test answer_author',
                'author_name' => 'test author_name',
                'icon' => 'test icon',
                'lang' => 'UA',
                'likes' => 23,
                'stars' => 4,
                'text' => 'test text',
            ],
            $token,
        );

        $response->assertStatus(404);
    }
}
