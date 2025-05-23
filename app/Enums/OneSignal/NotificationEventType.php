<?php

namespace App\Enums\OneSignal;

enum NotificationEventType: string
{
    case Display = 'notification.willDisplay';
    case Clicked = 'notification.clicked';
    case Dismissed = 'notification.dismissed';
}
