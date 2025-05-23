<?php

namespace App\Listeners\Client\Telegraph;

use App\Enums\NotificationTemplate\Event;
use App\Events\Client\UserWasAuthenticated;
use App\Models\TelegraphChat;
use App\Services\Client\Telegraph\MessageSender;

class AfterLoginMessage
{
    /**
     * Handle the event.
     */
    public function handle(UserWasAuthenticated $event): void
    {
        $user = $event->user;
        /** @var TelegraphChat $chat */
        $chat = $user->telegraphChats()->withActiveBot()->first();
        if (!$chat || !$user->needNotify(Event::UserLoggedIn)) {
            return;
        }

        $message = "Здравствуйте, {{username}}!\n";
        $message .= "Вы успешно вошли в систему PWA. Если это были вы, можете игнорировать это сообщение.\n";
        $message .= "Дата и время авторизации: {{datetime}}\n";
        $message .= "IP-адрес: {{ip}}\n";
        $message .= "Если вы не совершали этот вход, рекомендуем немедленно связаться с нашей службой поддержки.\n";
        $message .= "С уважением,\n";
        $message .= "Команда PWA";

        $message = str_replace(['{{username}}' , '{{datetime}}', '{{ip}}'], [$user->username, $event->eventTime, $event->ip], $message);

        /** @var MessageSender $messageSender */
        $messageSender = app(MessageSender::class);
        $messageSender->send($chat, $message);
    }
}
