<?php

namespace App\Services\Client\Telegraph;

use App\Enums\Invite\ActionName;
use App\Models\User;
use App\Services\Client\Invite\InviteService;

class MainBotService
{
    /** @var MessageSender $messageSender */
    private MessageSender $messageSender;

    public function __construct(protected int $companyId, protected MyWebhookHandler $handler)
    {
        $this->messageSender = app(MessageSender::class);
    }

    public function auth(string $inviteKey): void
    {
        $chat = $this->handler->getChat();
        if ($chat->user_id) {
            $this->messageSender->send($chat, 'Вы уже авторизованы');
            return;
        }

        if (empty($inviteKey)) {
            $this->messageSender->send($chat, 'Неверный код авторизации');
            return;
        }

        /** @var InviteService $inviteService */
        $inviteService = app(InviteService::class, ['companyId' => $this->companyId]);
        $invite = $inviteService->getByKey($inviteKey, ActionName::TgBot, false);

        if (!$invite) {
            $this->messageSender->send($chat, 'Неверный код авторизации');
            return;
        }
        $userIdFromInvite = $invite->body['user_id'] ?? null;

        if (!$userIdFromInvite) {
            $this->messageSender->send($chat, 'Повторите попытку авторизоваться. Перейдите по ссылке в шапке сайта');
            return;
        }
        $user = User::query()->find($userIdFromInvite);

        if (!$user) {
            $this->messageSender->send($chat, 'Пользователь не найден');
            return;
        }

        if ($user && !$user->telegram_id) {
            $user->telegram_id = $chat->chat_id;
            $user->save();
        }

        $chat->user_id = $user->id;
        $chat->save();

        $this->messageSender->send($chat, 'OK');
    }
}
