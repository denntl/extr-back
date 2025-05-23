<?php

namespace App\Models;

use DefStudio\Telegraph\Models\TelegraphChat as BaseTelegraphChat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * Class TelegraphBot
 *
 * @property int $id
 * @property int $chat_id
 * @property string $name
 * @property int $telegraph_bot_id
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class TelegraphChat extends BaseTelegraphChat
{
    protected $fillable = [
        'chat_id',
        'name',
        'telegraph_bot_id',
        'user_id',
    ];

    public function scopeWithActiveBot(Builder $query)
    {
        $query->whereHas('bot', function (Builder $query) {
            $query->where('is_active', '=', true);
        });
    }
}
