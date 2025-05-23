<?php

namespace App\Services\Common\OneSignal\Enums;

enum IpAllowlistMode: string
{
    case DISABLED = 'disabled';
    case EXPLICIT = 'explicit';
}
