<?php

namespace App\Http\Controllers\Admin\Common;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Common\File\UploadRequest;
use App\Services\Common\File\FileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TelegramWebhookController extends Controller
{
    public function webhook(string $publicId, Request $request): JsonResponse
    {
        logger()->info('Webhook called', ['public_id' => $publicId, 'request' => $request->all()]);
        return response()->json([
            'result' => 'ok',
        ]);
    }
}
