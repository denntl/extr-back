<?php

namespace App\Enums\PwaEvents;

enum Event: string
{
    case Click = 'click';
    case UniqueClick = 'uniqueClick';
    case Install = 'install';
    case RepeatedInstall = 'repeatedInstall';
    case Open = 'open';
    case RepeatedOpen = 'repeatedOpen';
    case Subscribe = 'subscribe';
    case Registration = 'reg';
    case Deposit = 'dep';

    public static function mapEvent(string $eventName): self
    {
        foreach (self::cases() as $case) {
            if ($case->value === $eventName) {
                return $case;
            }
        }
        throw new \InvalidArgumentException("Unknown event name: $eventName");
    }
}
