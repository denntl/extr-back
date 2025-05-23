<?php

namespace App\Listeners\Client\Domain;

use App\Events\Client\DomainWasCreated;
use App\Models\TelegraphChat;
use App\Services\Client\Telegraph\MessageSender;

class AfterDomainCreated
{
    public function handle(DomainWasCreated $event): void
    {
        $chats = TelegraphChat::query()->withActiveBot()->get();

        $message = "🌟 Новый домен успешно добавлен!\n";
        $message .= '📌 Домен: ' . $event->domain->domain . "\n";
        $message .= "Теперь он доступен для использования в вашем PWA.\n";
        $message .= "📞 Если нужна помощь, свяжитесь с саппортом.";

        /** @var MessageSender $messageSender */
        $messageSender = app(MessageSender::class);
        foreach ($chats as $chat) {
            $messageSender->send($chat, $message);
        }
    }
}
