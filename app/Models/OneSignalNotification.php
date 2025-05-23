<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property-read PushNotification $pushNotification
 * @@property-read string $onesignal_notification_id
 */
class OneSignalNotification extends Model
{
    use HasFactory;

    protected $table = 'onesignal_notifications';

    protected $fillable = [
        'push_notifications_id',
        'onesignal_notification_id',
        'sent',
        'delivered',
        'clicked',
        'dismissed',
        'queued_at',
        'completed_at',
        'last_webhook_accepted_at',
        'onesignal_template_id',
        'application_id',
        'geo_id',
    ];

    public $timestamps = false;

    protected $dates = [
        'created_at',
        'queued_at',
        'completed_at',
        'last_webhook_accepted_at',
    ];

    public function pushNotification(): BelongsTo
    {
        return $this->belongsTo(PushNotification::class, 'push_notifications_id');
    }

    public function scopePushNotification(Builder $builder): void
    {
        $builder->join(
            'push_notifications',
            'push_notifications.id',
            '=',
            'onesignal_notifications.push_notifications_id'
        )
            ->leftJoin('users', 'users.id', '=', 'push_notifications.created_by')
            ->leftJoin('applications', 'applications.id', '=', 'push_notifications.application_id');
    }
}
