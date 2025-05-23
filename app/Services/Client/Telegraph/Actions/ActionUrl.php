<?php

namespace App\Services\Client\Telegraph\Actions;

use DefStudio\Telegraph\Keyboard\Button;

class ActionUrl
{
    private static function url(string $url): string
    {
        if (
            config('app.env') === 'local'
            || config('app.frontend_url') === 'http://localhost:3000'
        ) {
            $url = 'http://copy.next?url=' . $url;
        }
        return $url;
    }
    public static function make(string $label, string $url): Button
    {
        return Button::make($label)->url(self::url($url));
    }

    public static function fromArray(array $data): Button
    {
        return Button::make($data['label'])->url(self::url($data['url']));
    }
}
