<?php

namespace App\Listeners\Client\Domain;

use App\Events\Client\DomainStatusWasChanged;
use App\Models\TelegraphChat;
use App\Services\Client\Domain\DomainService;
use App\Services\Client\Telegraph\MessageSender;
use Illuminate\Database\Eloquent\Collection;

class AfterDomainStatusChanged
{
    public function __construct(
        public DomainService $domainService,
    ) {
    }

    public function handle(DomainStatusWasChanged $event): void
    {
        $chats = $this->chats((bool) $event->domain->status, $event->domain->id);
        $message = $this->message((bool) $event->domain->status, $event->domain->domain);

        $messageSender = app(MessageSender::class);
        foreach ($chats as $chat) {
            $messageSender->send($chat, $message);
        }
    }


    private function message(bool $status, string $domainName): string
    {
        if ($status) {
            $message = "✅ Отличная новость!\n";
            $message .= 'Домен ' . $domainName . " снова активный\n\n";
            $message .= "📌 Что это значит?\n";
            $message .= "Теперь он доступен для использования в вашем PWA.\n\n";
            $message .= "📞 Если нужна помощь, свяжитесь с саппортом.";
        } else {
            $message = "⚠️ Внимание!\n";
            $message .= "Домен {$domainName} отключен.\n\n";
            $message .= "📌 Что это значит?\n";
            $message .= "Ваше PWA больше недоступно на текущем домене.\n\n";
            $message .= "🚨 Что нужно сделать?\n\n";
            $message .= "Выберите новый домен для вашего PWA.\n\n";
            $message .= "Протестируйте доступность приложения на новом домене.\n\n";
            $message .= "📞 Если нужна помощь, свяжитесь с саппортом.\n\n";
            $message .= "💡 Чем раньше вы смените домен, тем быстрее приложение снова станет доступным для пользователей!";
        }


        return $message;
    }

    private function chats(bool $status, int $domainId): Collection
    {
        if ($status) {
            $chats = TelegraphChat::query()->withActiveBot()->get();
        } else {
            $users = $this->domainService->getUsersByDomain($domainId)->pluck('owner_id')->toArray();
            $chats = TelegraphChat::query()->withActiveBot()->whereIn('user_id', $users)->get();
        }

        return $chats;
    }
}
