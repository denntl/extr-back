<?php

namespace App\Models;

use App\Enums\PushNotification\Status;
use App\Enums\PushNotification\Type;
use App\Services\Common\OneSignal\DTO\ApiRequest\SendRequestDTO;
use App\Services\Common\OneSignal\Exceptions\InvalidPushNotificationArgumentException;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

/**
 *
 * @property int $id
 * @property int $application_id
 * @property int|null $push_template_id
 * @property int|null $created_by
 * @property int $type
 * @property int $status
 * @property boolean $is_active
 * @property string $name
 * @property string $date
 * @property string $time
 * @property array|string $geo
 * @property array|string $events
 * @property string|null $title
 * @property string|null $content
 * @property string|null $icon
 * @property string|null $image
 * @property string|null $link
 * @property boolean $is_delayed
 * @property Carbon|null $last_onesignal_created_at
 *
 * @property-read User|null $createdBy
 * @property-read PushTemplate|null $pushTemplate
 * @property-read Application $application
 * @property-read OneSignalNotification|null $oneSignalNotification
 */
class PushNotification extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const SKIP_SENDING_PUSH_NOTIFICATION_BEFORE_SECONDS = 60 * 5;
    public const GET_PUSH_NOTIFICATIONS_FOR_UPDATE_HOURS = 5;

    protected $fillable = [
        'name',
        'application_id',
        'push_template_id',
        'date',
        'time',
        'status',
        'type',
        'geo',
        'events',
        'created_by',
        'is_active',
        'title',
        'content',
        'icon',
        'image',
        'link',
        'is_delayed',
        'last_onesignal_created_at',
    ];

    protected $casts = [
        'geo' => 'array',
        'events' => 'array',
    ];

    /**
     * @return bool
     */
    public function canBeCanceled(): bool
    {
        return Carbon::parse($this->date . ' ' . $this->time) > Carbon::now('UTC')
                ->addSeconds(self::SKIP_SENDING_PUSH_NOTIFICATION_BEFORE_SECONDS);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function pushTemplate(): BelongsTo
    {
        return $this->belongsTo(PushTemplate::class, 'push_template_id', 'id');
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'application_id', 'id');
    }

    public function oneSignalNotification(): BelongsTo
    {
        return $this->belongsTo(OneSignalNotification::class, 'id', 'push_notifications_id');
    }

    public function scopeCreatedBy(Builder $builder): void
    {
        $builder->leftJoin('users', 'users.id', '=', 'push_notifications.created_by');
    }

    public function scopePushTemplate(Builder $builder): void
    {
        $builder->leftJoin('push_templates', 'push_templates.id', '=', 'push_notifications.push_template_id');
    }

    public function scopeApplication(Builder $builder): void
    {
        $builder->leftJoin('applications', 'applications.id', '=', 'push_notifications.application_id')
            ->join('domains', 'applications.domain_id', '=', 'domains.id');
    }

    public function scopeOneSignalNotification(Builder $builder): void
    {
        $builder->join('onesignal_notifications', 'onesignal_notifications.push_notifications_id', '=', 'push_notifications.id')
            ->groupBy('push_notifications.id');
    }

    /**
     * @return bool
     */
    public function isWaiting(): bool
    {
        return $this->status === Status::Wait->value;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * @return bool
     */
    public function isSingle(): bool
    {
        return $this->type === Type::Single->value;
    }

    /**
     * @return SendRequestDTO
     * @throws InvalidPushNotificationArgumentException
     */
    public function toDTO(): SendRequestDTO
    {
        return new SendRequestDTO(
            bigPicture: url(Storage::url($this->image)),
            smallIcon: url(Storage::url($this->icon)),
            url: $this->link,
            title: $this->title,
            contents: $this->content,
            events: $this->events,
            geos: $this->geo,
            sendAfter: $this->type === Type::Regular->value ? null : $this->date . ' ' . $this->time,
        );
    }
}
