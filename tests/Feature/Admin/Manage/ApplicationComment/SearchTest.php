<?php

namespace Tests\Feature\Admin\Manage\ApplicationComment;

use App\Enums\Authorization\PermissionName;
use App\Models\Application;
use App\Models\ApplicationComment;
use Tests\TestCase;

class SearchTest extends TestCase
{
    public function testSearchQueryIsNotProvided()
    {
        [$token] = $this->getUserToken(PermissionName::ManageApplicationSave);

        ApplicationComment::factory()->count(2)->create();
        $response = $this->getRequest(route('manage.application.comment.search'), $token);

        $response->assertStatus(200);
        $this->assertCount(2, $response->json('comments'));
    }

    public function testSuccess()
    {
        [$token] = $this->getUserToken(PermissionName::ManageApplicationSave);

        $apps = ApplicationComment::factory()->count(2)->create();
        $first = $apps->first();
        $first->text = 'My test description';
        $first->save();

        $response = $this->getRequest(route('manage.application.comment.search', ['query' => 'descript']), $token);

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('comments'));
        $response->assertJson([
            'comments' => [
                [
                    'value' => $first->id,
                    'label' => $first->lang . ' | ' . $first->author_name . ' | ' . mb_substr($first->text, 0, 100) . ' ...',
                ],
            ],
        ]);
    }
}
