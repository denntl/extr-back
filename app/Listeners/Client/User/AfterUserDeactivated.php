<?php

namespace App\Listeners\Client\User;

use App\Enums\NotificationTemplate\Event;
use App\Enums\User\Status;
use App\Events\Client\UserStatusChanged;
use App\Models\TelegraphChat;
use App\Services\Client\Telegraph\MessageSender;
use App\Services\Client\User\DTO\UserCompanyOwnerTeamLeadDTO;
use App\Services\Common\User\UserService;

class AfterUserDeactivated
{
    public function handle(UserStatusChanged $event): void
    {
        if ($event->status !== Status::Deleted->value) {
            return;
        }

        $user = $event->user;
        $recipientsModels = $this->recipients($user->id);

        $message = "🔔 Пользователь деактивирован \n";
        $message .=  "Пользователь {$user->name} был деактивирован. \n";
        $message .=  "Детали: \n";
        $message .=  "Email: {$user->email} \n";
        $message .=  "Дата и время деактивации: {$user->updated_at} \n\n";
        $message .=  "Статус ПВА: \n";
        $message .=  "Все активные ПВА были переназначены другим пользователям компании";

        foreach ($recipientsModels as $recipient) {
            if (empty($recipient)) {
                continue;
            }

            /** @var TelegraphChat $chat */
            $chat = $recipient->telegraphChats()->withActiveBot()->first();
            if (!$chat || !$recipient->needNotify(Event::UserStatusChanged)) {
                continue;
            }

            /** @var MessageSender $messageSender */
            $messageSender = app(MessageSender::class);
            $messageSender->send($chat, $message);
        }
    }

    private function recipients(int $userId): UserCompanyOwnerTeamLeadDTO
    {
        /** @var UserService $userService */
        $userService = app(UserService::class);
        return $userService
            ->getUsersCompanyOwnerAndTeamLead($userId)
            ->unique();
    }
}
