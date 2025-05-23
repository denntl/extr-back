<?php

namespace App\Models;

use App\Enums\Authorization\RoleName;
use App\Enums\NotificationTemplate\Event;
use App\Models\Traits\Contracts\WithExternalIdInterface;
use App\Models\Traits\WithExternalId;
use App\Models\Traits\WithNumberExternalId;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Models\Role;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property string $public_id
 * @property string $username
 * @property int $status
 * @property int|null $company_id
 * @property int|null $team_id
 * @property bool $is_employee
 * @property int|null $telegram_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Company $company
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection|Role[] $roles
 * @property-read int|null $roles_count
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereName($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereEmailVerifiedAt($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User whereRememberToken($value)
 * @method static Builder|User wherePublicId($value)
 * @method static Builder|User whereUsername($value)
 * @method static Builder|User whereStatus($value)
 * @method static Builder|User whereCompanyId($value)
 * @method static Builder|User whereIsEmployee($value)
 * @method static Builder|User whereTelegramId($value)
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereUpdatedAt($value)
 */
class User extends Authenticatable implements WithExternalIdInterface
{
    use HasFactory;
    use Notifiable;
    use HasApiTokens;
    use HasRoles;
    use WithExternalId;
    use WithNumberExternalId;

    /** @use HasFactory */
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'remember_token',
        'public_id',
        'username',
        'status',
        'company_id',
        'team_id',
        'is_employee',
        'telegram_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];

    /**
     * @param Builder $builder
     * @return void
     */
    public function scopeTeam(Builder $builder): void
    {
        $builder->leftJoin('teams', 'teams.id', '=', 'users.team_id');
    }

    public function scopeCompany(Builder $builder)
    {
        $builder->join('companies', 'users.company_id', '=', 'companies.id');
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->hasRole(RoleName::Admin->value);
    }

    /**
     * Get the company that the user belongs to.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the team that the user belongs to.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get the telegraph chat that the user belongs to.
     */
    public function telegraphChats(): HasMany
    {
        return $this->hasMany(TelegraphChat::class, 'user_id', 'id');
    }

    public function needNotify(Event $event): bool
    {
         $query = $this->newQuery()
            ->join('companies', 'users.company_id', '=', 'companies.id')
            ->join('company_notifications', 'companies.id', '=', 'company_notifications.company_id')
            ->join('notification_templates', 'company_notifications.notification_template_id', '=', 'notification_templates.id')
            ->leftJoin('notification_template_roles', 'notification_templates.id', '=', 'notification_template_roles.notification_template_id')
            ->leftJoin('model_has_roles', function ($join) {
                $join->on('users.id', '=', 'model_has_roles.model_id')
                    ->where('model_has_roles.model_type', '=', User::class);
            })
            ->where('company_notifications.is_enabled', '=', true)
            ->where('notification_templates.is_active', '=', true)
            ->where('notification_templates.enable_for_client', '=', true)
            ->where('notification_templates.event', $event->value)
            ->where('users.id', $this->id)
            ->whereNull('notification_templates.deleted_at')
            ->where(function (Builder $query) {
                $query->where('notification_templates.all_roles', '=', true)
                    ->orWhereRaw('notification_template_roles.role_id=model_has_roles.role_id');
            });

        return $query->exists();
    }
}
