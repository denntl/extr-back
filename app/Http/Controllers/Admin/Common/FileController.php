<?php

namespace App\Http\Controllers\Admin\Common;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Common\File\UploadRequest;
use App\Services\Common\File\FileService;
use Illuminate\Http\JsonResponse;

class FileController extends Controller
{
    public function upload(UploadRequest $request, FileService $fileService): JsonResponse
    {
        $result = [];
        $files = $request->files->all()['files'] ?? [];
        foreach ($files as $file) {
            $result[] = $fileService->uploadFile($file ?? $file)->only(['id', 'path', 'original_name', 'mime']);
        }

        return response()->json([
            'files' => $result,
        ]);
    }
}
