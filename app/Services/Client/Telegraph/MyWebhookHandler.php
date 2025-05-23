<?php

namespace App\Services\Client\Telegraph;

use App\Models\TelegraphChat;
use DefStudio\Telegraph\Handlers\WebhookHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @property TelegraphChat $chat
 */
class MyWebhookHandler extends WebhookHandler
{
    public function start(string $inviteToken = ''): void
    {
        /** @var MainBotService $service */
        $service = app(MainBotService::class, ['companyId' => $this->bot->company_id, 'handler' => $this]);

        $service->auth($inviteToken);
    }

    public function getChat(): TelegraphChat
    {
        return $this->chat;
    }

    protected function setupChat(): void
    {
        if (isset($this->message)) {
            $telegramChat = $this->message->chat();
        } elseif (isset($this->reaction)) {
            $telegramChat = $this->reaction->chat();
        } else {
            $telegramChat = $this->callbackQuery?->message()?->chat();
        }

        assert($telegramChat !== null);

        /** @var TelegraphChat $chat */
        $chat = $this->bot->chats()->firstOrCreate([
            'chat_id' => $telegramChat->id(),
        ]);
        $this->chat = $chat;

        if (!$this->chat->exists) {
            if (!$this->allowUnknownChat()) {
                throw new NotFoundHttpException();
            }

            if (config('telegraph.security.store_unknown_chats_in_db', false)) {
                $this->createChat($telegramChat, $this->chat);
            }
        }
    }
}
