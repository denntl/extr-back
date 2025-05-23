<?php

namespace Tests\Feature\Admin\Client\ApplicationComment;

use App\Enums\Authorization\PermissionName;
use App\Models\Application;
use App\Models\ApplicationComment;
use Tests\TestCase;

class EditTest extends TestCase
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
        $comment = ApplicationComment::factory()->create([
            'application_id' => $application->id,
            'created_by' => $user->id,
        ]);

        $response = $this->getRequest(
            route('client.application.comment.edit', ['id' => $comment->id]),
            $token,
        );

        $response->assertStatus(200);
        $this->assertEquals($comment->id, $response->json('comment.id'));
        $this->assertEquals($comment->author_name, $response->json('comment.author_name'));
        $this->assertEquals($comment->text, $response->json('comment.text'));
    }

    public function testHasNoAccessToApplication()
    {
        [$token] = $this->getUserToken(PermissionName::ClientApplicationSave);
        $comment = ApplicationComment::factory()->create();

        $response = $this->getRequest(route('client.application.comment.edit', ['id' => $comment->id]), $token);

        $response->assertStatus(403);
    }
}
