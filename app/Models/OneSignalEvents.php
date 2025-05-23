<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $event_id
 * @property string $notification_id
 * @property-read OneSignalNotification $oneSignalNotification
 */
class OneSignalEvents extends Model
{
    protected $table = 'onesignal_events';

    protected $fillable = [
        'event_id',
        'notification_id',
    ];

    public $timestamps = false;

    public function oneSignalNotification(): BelongsTo
    {
        return $this->belongsTo(OneSignalNotification::class, 'notification_id', 'onesignal_notification_id');
    }
}
