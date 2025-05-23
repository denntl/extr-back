<?php

namespace App\Services\Client\TelegramBot;

use App\Models\TelegraphBot;
use App\Services\Client\Invite\DTO\InviteDTO;
use App\Services\Client\TelegramBot\Exceptions\TelegramBotIsActiveException;
use DefStudio\Telegraph\Exceptions\TelegraphException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

readonly class TelegramBotService
{
    public function __construct(protected int $companyId)
    {
    }

    /**
     * @throws ModelNotFoundException
     */
    public function get(?bool $public = true): TelegraphBot
    {
        $query = TelegraphBot::query()->where('company_id', $this->companyId);

        if ($public) {
            $query->select([
                'is_active',
                'token',
                'name',
                'public_id',
            ]);
        }

        return $query->firstOrFail();
    }

    public function create(): TelegraphBot
    {
        return TelegraphBot::query()->create([
            'company_id' => $this->companyId,
            'is_active' => false,
        ]);
    }

    /**
     * @throws TelegramBotIsActiveException
     */
    public function update(array $data): bool
    {
        $telegramBot = $this->get(false);

        if ($telegramBot->is_active) {
            throw new TelegramBotIsActiveException();
        }

        return $telegramBot->update($data);
    }

    /**
     * @throws TelegraphException
     */
    public function changeStatus(bool $isActive): TelegraphBot
    {
        $telegramBot = $this->get(false);

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

    public function getInviteLink(InviteDTO $invite): string
    {
        $telegramBot = $this->get();

        return "https://t.me/$telegramBot->name?start=$invite->key";
    }
}
