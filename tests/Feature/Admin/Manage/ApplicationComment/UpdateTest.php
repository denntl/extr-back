<?php

namespace Tests\Feature\Admin\Manage\ApplicationComment;

use App\Enums\Authorization\PermissionName;
use App\Models\Application;
use App\Models\ApplicationComment;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    public function testSuccess()
    {
        [$token, $user] = $this->getUserToken(PermissionName::ManageApplicationSave);
        /** @var Application $application */
        $application = Application::factory()->create([
            'company_id' => $user->company_id,
            'created_by_id' => $user->id,
            'owner_id' => $user->id,
        ]);
        $comment = ApplicationComment::factory()->create([
            'application_id' => $application->id,
            'created_by' => $user->id,
        ]);

        $response = $this->putRequest(
            route('manage.application.comment.update', ['id' => $comment->id]),
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

        $response->assertStatus(200);
        $this->assertEquals($comment->id, $response->json('comment.id'));
        $this->assertEquals('test author_name', $response->json('comment.author_name'));
        $this->assertEquals('test text', $response->json('comment.text'));

        $this->assertDatabaseHas('application_comments', [
            'id' => $comment->id,
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
}
