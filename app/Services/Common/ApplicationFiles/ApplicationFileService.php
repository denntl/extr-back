<?php

namespace App\Services\Common\ApplicationFiles;

use App\Models\Application;

readonly class ApplicationFileService
{
    public function saveFiles(Application $application, array $files): void
    {
        $files = array_combine(
            array_column($files, 'id'),
            array_map(fn($value, $key) => ['position' => $key], $files, array_keys($files))
        );
        $application->files()->sync($files);
    }
}
