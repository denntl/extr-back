<?php

namespace App\Enums\PushNotification;

enum Status: int
{
    case Draft = 1;
    case Wait = 2;
    case Sent = 3;
}
