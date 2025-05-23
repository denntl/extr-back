<?php

namespace App\Enums\OneSignal;

enum NotificationEventId: int
{
    case Display = 1;
    case Clicked = 2;
    case Dismissed = 3;
}
