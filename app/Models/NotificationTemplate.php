<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Role;

/**
 * App\Models\NotificationTemplate
 *
 * @property int $id
 * @property string $name
 * @property bool $is_auto
 * @property int $entity
 * @property string $event
 * @property bool $all_roles
 * @property bool $is_active
 * @property bool $enable_for_client
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|NotificationTemplate newModelQuery()
 * @method static Builder|NotificationTemplate newQuery()
 * @method static Builder|NotificationTemplate query()
 * @method static Builder|NotificationTemplate whereId($value)
 * @method static Builder|NotificationTemplate whereName($value)
 * @method static Builder|NotificationTemplate whereIsAuto($value)
 * @method static Builder|NotificationTemplate whereEntity($value)
 * @method static Builder|NotificationTemplate whereEvent($value)
 * @method static Builder|NotificationTemplate whereAllRoles($value)
 * @method static Builder|NotificationTemplate whereIsActive($value)
 * @method static Builder|NotificationTemplate whereEnableForClient($value)
 * @method static Builder|NotificationTemplate whereCreatedAt($value)
 * @method static Builder|NotificationTemplate whereUpdatedAt($value)
 */
class NotificationTemplate extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'is_auto',
        'entity',
        'event',
        'all_roles',
        'is_active',
        'enable_for_client',
    ];

    public function scopeCompanyNotifications(Builder $builder, int $companyId): void
    {
        $builder->leftJoin('company_notifications', function (JoinClause $join) use ($companyId) {
            $join->on('company_notifications.notification_template_id', '=', 'notification_templates.id')
                ->where('company_notifications.company_id', $companyId);
        })
            ->where(function ($query) use ($companyId) {
                $query->where('company_notifications.company_id', $companyId)
                    ->orWhereNull('company_notifications.company_id');
            });
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'notification_template_roles', 'notification_template_id', 'role_id');
    }
}
