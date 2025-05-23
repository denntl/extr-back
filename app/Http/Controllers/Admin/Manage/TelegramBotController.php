<?php

namespace App\Http\Controllers\Admin\Manage;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Manage\TelegramBot\ChangeStatusTelegramBotRequest;
use App\Services\Manage\TelegramBot\TelegramBotService;
use DefStudio\Telegraph\Exceptions\TelegramWebhookException;
use Illuminate\Http\JsonResponse;

class TelegramBotController extends Controller
{
    public function changeStatus(int $id, ChangeStatusTelegramBotRequest $request, TelegramBotService $telegramBotService): JsonResponse
    {
        try {
            $telegramBotService->changeStatus($id, $request->validated('isActive'));
        } catch (TelegramWebhookException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => ['is_active' => [$e->getMessage()]],
            ], 422);
        }
        return response()->json([
            'message' => 'Successfully activated',
        ]);
    }
}
