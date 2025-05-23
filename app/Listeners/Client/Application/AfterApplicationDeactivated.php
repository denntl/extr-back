<?php

namespace App\Listeners\Client\Application;

use App\Enums\NotificationTemplate\Event;
use App\Events\Client\Application\ApplicationDeactivated;
use App\Models\TelegraphChat;
use App\Services\Client\Telegraph\Actions\ActionUrl;
use App\Services\Client\Telegraph\Messages\MessageWithActions;
use App\Services\Client\Telegraph\MessageSender;
use App\Services\Client\User\DTO\UserCompanyOwnerTeamLeadDTO;
use App\Services\Common\User\UserService;

class AfterApplicationDeactivated
{
    public function handle(ApplicationDeactivated $event): void
    {
        $recipientsModels = $this->recipients($event);
        $appOwner = $recipientsModels->getById($event->application->owner_id);

        $pwaRoute = config('app.frontend_url') . '/admin/applications/update/' . $event->application->public_id;

        $message = "⚠️ ПВА было деактивировано!\n";
        $message .= "Пользователь: {$appOwner->name}\n";
        $message .= "Название ПВА: {$event->application->name}\n";
        $message .= "Домен: {$event->application->full_domain}\n";
        $message .= "Дата и время деактивации: {$event->application->updated_at}\n\n";


        foreach ($recipientsModels as $user) {
            if (empty($user)) {
                continue;
            }
            /** @var TelegraphChat $chat */
            $chat = $user->telegraphChats()->withActiveBot()->first();
            if (!$chat || !$user->needNotify(Event::ApplicationDeactivated)) {
                continue;
            }

            /** @var MessageSender $messageSender */
            $messageSender = app(MessageSender::class);
            $messageSender->send($chat, MessageWithActions::fromArray([
                'text' => $message,
                'actions' => [
                    ActionUrl::make('Перейти к ПВА', $pwaRoute),
                ]
            ]));
        }
    }

    private function recipients(ApplicationDeactivated $event): UserCompanyOwnerTeamLeadDTO
    {
        /** @var UserService $userService */
        $userService = app(UserService::class);
        return $userService
            ->getUsersCompanyOwnerAndTeamLead($event->application->owner_id)
            ->unique();
    }
}
