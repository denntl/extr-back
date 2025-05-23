<?php

namespace Tests\Feature\Admin\Common\File;

use App\Enums\Authorization\PermissionName;
use App\Models\File;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileUploadTest extends TestCase
{
    use DatabaseTransactions;

    public function testFileUpload()
    {
        [$token, $user] = $this->getUserToken(PermissionName::ClientApplicationSave);

        $files = [
            UploadedFile::fake()->image('avatar.jpg'),
            UploadedFile::fake()->createWithContent('test.txt', 'test text in file')
        ];

        $response = $this->postRequest(
            route('common.file.upload'),
            [
                'files' => $files,
            ],
            $token
        );

        foreach ($response->json('files') as $i => $file) {
            Storage::disk('public')->assertExists($file['path']);

            $this->assertDatabaseHas('files', [
                'company_id' => $user->company_id,
                'uploaded_by' => $user->id,
                'path' => $file['path'],
                'original_name' => $files[$i]->getClientOriginalName(),
                'mime' => $files[$i]->getClientMimeType(),
            ]);
        }

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'files'
        ]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Storage::disk('public')->deleteDirectory('common');
    }
}
