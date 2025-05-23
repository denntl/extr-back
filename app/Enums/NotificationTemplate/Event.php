<?php

namespace App\Enums\NotificationTemplate;

enum Event: string
{
    case UserLoggedIn = 'userLoggedIn';
    case UserStatusChanged = 'userStatusChanged';
    case DomainCreated = 'domainCreated';
    case DomainStatusChanged = 'domainStatusChanged';
    case ApplicationCreated = 'applicationCreated';
    case ApplicationDeactivated = 'applicationDeactivated';

    public static function getByValue(string $value): ?Event
    {
        foreach (self::cases() as $enum) {
            if ($enum->value === $value) {
                return $enum;
            }
        }

        return null;
    }
}
