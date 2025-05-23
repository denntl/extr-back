<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

/**
 * App\Models\NotificationTemplateRole
 *
 * @property int $id
 * @property int $notification_template_id
 * @property int $role_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|NotificationTemplateRole newModelQuery()
 * @method static Builder|NotificationTemplateRole newQuery()
 * @method static Builder|NotificationTemplateRole query()
 * @method static Builder|NotificationTemplateRole whereId($value)
 * @method static Builder|NotificationTemplateRole whereNotificationTemplateId($value)
 * @method static Builder|NotificationTemplateRole whereRoleId($value)
 * @method static Builder|NotificationTemplateRole whereCreatedAt($value)
 * @method static Builder|NotificationTemplateRole whereUpdatedAt($value)
 */
class NotificationTemplateRole extends Model
{
    use HasFactory;

    protected $fillable = [
        'notification_template_id',
        'role_id',
    ];
}
