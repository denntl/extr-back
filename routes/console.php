<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('resolve-filebeat-logs', function () {
    Artisan::call('app:resolve-filebeat-logs');
})->purpose('Resolve filebeat log symlinks')->dailyAt('00:00');

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('send-regular-push-notifications', function () {
    Artisan::call('app:send-regular-push-notifications');
})->purpose('Send regular push notifications')->withoutOverlapping(0)
    ->everyMinute();

Artisan::command('update-new-push-notifications', function () {
    Artisan::call('app:update-new-push-notifications');
})->purpose('Update new push notifications')->withoutOverlapping(0)
    ->everyMinute();

Artisan::command('process-scheduled-notifications', function () {
    Artisan::call('app:process-scheduled-notifications');
})->purpose('Send single push notification')->withoutOverlapping(0)->everyMinute();
