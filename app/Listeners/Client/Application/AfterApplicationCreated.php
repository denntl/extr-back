<?php

namespace App\Listeners\Client\Application;

use App\Enums\NotificationTemplate\Event;
use App\Events\Client\Application\ApplicationCreated;
use App\Models\TelegraphChat;
use App\Services\Client\Telegraph\Actions\ActionUrl;
use App\Services\Client\Telegraph\Messages\MessageWithActions;
use App\Services\Client\Telegraph\MessageSender;
use App\Services\Client\User\DTO\UserCompanyOwnerTeamLeadDTO;
use App\Services\Client\User\UserService;

class AfterApplicationCreated
{
    public function handle(ApplicationCreated $event): void
    {
        $recipientsModels = $this->recipients($event);
        $appOwner = $recipientsModels->getById($event->application->owner_id);

        $pwaRoute = route('preview', ['appUuid' => $event->application->uuid]);
        $message = "🎉 Новое ПВА успешно создано!\n";
        $message .= "Пользователь: {$appOwner->name}\n";
        $message .= "Название ПВА: {$event->application->name}\n";
        $message .= "Домен: {$event->application->full_domain}\n\n";


        foreach ($recipientsModels as $user) {
            if (empty($user)) {
                continue;
            }
            /** @var TelegraphChat $chat */
            $chat = $user->telegraphChats()->withActiveBot()->first();
            if (!$chat || !$user->needNotify(Event::ApplicationCreated)) {
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

    private function recipients(ApplicationCreated $event): UserCompanyOwnerTeamLeadDTO
    {
        /** @var UserService $userService */
        $userService = app(UserService::class);
        return $userService->getUsersCompanyOwnerAndTeamLead($event->application->owner_id)->unique();
    }
}
