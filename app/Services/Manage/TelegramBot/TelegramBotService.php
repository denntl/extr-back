<?php

namespace App\Services\Manage\TelegramBot;

use App\Models\TelegraphBot;
use App\Models\TelegraphChat;
use App\Models\User;
use App\Services\Manage\TelegramBot\DTO\SendMessagesDTO;
use DefStudio\Telegraph\Exceptions\TelegraphException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;

class TelegramBotService
{
    /**
     * @throws TelegraphException
     */
    public function changeStatus(int $id, bool $isActive): TelegraphBot
    {
        $telegramBot = TelegraphBot::query()->findOrFail($id);

        if ($isActive) {
            $telegramBot->registerWebhook()->send();
        } else {
            $telegramBot->unregisterWebhook()->send();
        }

        $telegramBot->name = $telegramBot->info()['username'] ?? $telegramBot->name;
        $telegramBot->is_active = $isActive;
        $telegramBot->save();

        return $telegramBot;
    }

    public function sendMessagesToCompanies(SendMessagesDTO $dto): Collection
    {
        $query = TelegraphChat::query()
            ->join('telegraph_bots as tb', 'telegraph_chats.telegraph_bot_id', '=', 'tb.id')
            ->join('users', 'telegraph_chats.user_id', '=', 'users.id')
            ->join('model_has_roles', function (JoinClause $builder) {
                $builder->on('users.id', '=', 'model_has_roles.model_id')
                    ->where('model_has_roles.model_type', User::class);
            })
            ->whereRaw('tb.is_active = ?', [true]);

        if (!$dto->isAllUsers) {
            $query->whereIn('model_has_roles.role_id', $dto->roles);
        }

        if (!$dto->isAllCompanies) {
            $query->whereIn('users.company_id', $dto->companies);
        }

        /** @var Collection|TelegraphChat[] $chats */
        $chats = $query->select('telegraph_chats.*')->get();

        foreach ($chats as $chat) {
            $chat->message($dto->message)->send();
        }

        return $chats;
    }
}
