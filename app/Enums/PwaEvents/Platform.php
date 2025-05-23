<?php

namespace App\Enums\PwaEvents;

enum Platform: string
{
    case IOS = 'iOS';
    case Android = 'androidOS';
    case Unknown = 'unknown';
}
