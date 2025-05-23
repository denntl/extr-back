<?php

namespace App\Services\Common\File;

use App\Models\File;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

readonly class FileService
{
    private const COMMON_PATH = 'common';
    private Filesystem $fileSystem;

    public function __construct(private int $companyId)
    {
        $this->fileSystem = Storage::disk('public');
        if (!$this->fileSystem->exists(self::COMMON_PATH)) {
            $this->fileSystem->makeDirectory(self::COMMON_PATH);
        }

        if (!$this->fileSystem->exists($this->getCompanyDir())) {
            $this->fileSystem->makeDirectory($this->getCompanyDir());
        }
    }

    public function uploadFile(UploadedFile $file): File
    {
        // Generate a unique filename
        $filename = uniqid() . '.' . $file->getClientOriginalExtension();

        $filePath = $this->fileSystem->putFileAs($this->getCompanyDir(), $file, $filename);

        if (!$filePath) {
            throw new \Exception('Failed to save file');
        }
        // Create a new File model instance
        $fileModel = new File();
        $fileModel->company_id = $this->companyId;
        $fileModel->uploaded_by = auth()->id();
        $fileModel->path = $filePath;
        $fileModel->original_name = $file->getClientOriginalName();
        $fileModel->mime = $file->getClientMimeType();
        $fileModel->save();

        return $fileModel;
    }

    private function getCompanyDir(?string $filename = ''): string
    {
        $filename = $filename ? "/$filename" : '';
        return self::COMMON_PATH . "/$this->companyId" . $filename;
    }
}
