<?php

namespace App\Enums\PushTemplate;

enum Event: string
{
    case INSTALL = 'install';
    case REGISTRATION = 'reg';
    case DEPOSIT = 'dep';

    /**
     * @return array
     */
    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}
