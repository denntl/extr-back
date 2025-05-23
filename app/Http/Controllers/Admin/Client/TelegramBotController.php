<?php

namespace App\Http\Controllers\Admin\Client;

use App\Enums\Invite\ActionName;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Client\TelegramBot\ChangeStatusTelegramBotRequest;
use App\Http\Requests\Admin\Client\TelegramBot\UpdateTelegramBotRequest;
use App\Models\User;
use App\Services\Client\Invite\DTO\ActionDTO;
use App\Services\Client\Invite\InviteService;
use App\Services\Client\TelegramBot\Exceptions\TelegramBotIsActiveException;
use App\Services\Client\TelegramBot\TelegramBotService;
use DefStudio\Telegraph\Exceptions\TelegramWebhookException;
use DefStudio\Telegraph\Exceptions\TelegraphException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class TelegramBotController extends Controller
{
    public function edit(TelegramBotService $telegramBotService): JsonResponse
    {
        try {
            $telegramBot = $telegramBotService->get();
        } catch (ModelNotFoundException) {
            $telegramBot = $telegramBotService->create();
        }

        return response()->json([
            'telegramBot' => $telegramBot,
        ]);
    }

    public function update(UpdateTelegramBotRequest $request, TelegramBotService $telegramBotService): JsonResponse
    {
        try {
            $telegramBotService->update($request->validated());
        } catch (TelegramBotIsActiveException $e) {
            return response()->json([
                'message' => 'Validation failed',
                ['is_active' => 'The telegram bot is active'],
            ], 422);
        }

        return response()->json([
            'message' => 'Successfully updated',
            'telegramBot' => $telegramBotService->get(),
        ]);
    }

    public function changeStatus(ChangeStatusTelegramBotRequest $request, TelegramBotService $telegramBotService): JsonResponse
    {
        try {
            $telegramBotService->changeStatus($request->validated('isActive'));
        } catch (TelegramWebhookException | TelegraphException $e) {
            logger()->info('failed to change status', ['message' => $e->getMessage(), 'trace' => $e->getTrace()]);
            return response()->json([
                'message' => 'Validation failed',
                'errors' => ['is_active' => ['Указан некорректный токен. Пожалуйста, проверьте его и попробуйте снова.']],
            ], 422);
        }
        return response()->json([
            'message' => 'Successfully activated',
            'telegramBot' => $telegramBotService->get(),
        ]);
    }

    public function getActive(TelegramBotService $telegramBotService): JsonResponse
    {
        $telegramBot = $telegramBotService->get();

        return response()->json([
            'isActive' => $telegramBot->is_active,
        ]);
    }

    public function getInviteLink(TelegramBotService $telegramBotService, InviteService $inviteService): JsonResponse
    {
        $actionDTO = new ActionDTO(auth()->id(), ActionName::TgBot, [
            'user_id' => auth()->id(),
        ]);
        $invite = $inviteService->generateInvite($actionDTO);
        $inviteLink = $telegramBotService->getInviteLink($invite);

        return response()->json([
            'link' => $inviteLink,
        ]);
    }
}
