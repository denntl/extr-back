<?php

namespace App\Enums\NotificationTemplate;

enum Entity: int
{
    case User = 1;
    case Domain = 2;
    case Application = 3;
}
