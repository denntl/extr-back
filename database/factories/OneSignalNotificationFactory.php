<?php

namespace Database\Factories;

use App\Models\OneSignalNotification;
use App\Models\PushNotification;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OneSignalNotificationFactory extends Factory
{
    protected $model = OneSignalNotification::class;

    public function definition()
    {
        return [
            'push_notifications_id' => PushNotification::factory(),
            'onesignal_notification_id' => Str::uuid(),
        ];
    }
}
