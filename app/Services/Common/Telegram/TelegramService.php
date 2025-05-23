<?php

namespace App\Services\Common\Telegram;

class TelegramService
{
    public function isValidAuthRequest(array $data): bool
    {
        $checkHash = $data['hash'];
        unset($data['hash']);

        $dataCheckString = collect($data)
            ->map(function ($value, $key) {
                return $key . '=' . $value;
            })
            ->sort()
            ->implode("\n");

        $secretKey = hash('sha256', config('services.telegramAuth.token'), true);
        $hash = hash_hmac('sha256', $dataCheckString, $secretKey);

        return hash_equals($hash, $checkHash);
    }
}
