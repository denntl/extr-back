<?php

namespace App\Models;

use App\Models\Traits\WithExternalId;
use App\Models\Traits\WithSoftDeletesNumberExternalId;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Team
 *
 * @property int $id
 * @property int $company_id
 * @property string $name
 * @property int $public_id
 * @property int|null $team_lead_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Company $company
 * @property-read User|null $teamLead
 * @method static \Illuminate\Database\Eloquent\Builder|Team newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Team newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Team query()
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereTeamLeadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereUpdatedAt($value)
 */
class Team extends Model
{
    use HasFactory;
    use WithExternalId;
    use WithSoftDeletesNumberExternalId;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'name',
        'public_id',
        'team_lead_id',
    ];

    /**
     * Get the company that owns the team.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the team lead for the team.
     */
    public function teamLead(): BelongsTo
    {
        return $this->belongsTo(User::class, 'team_lead_id');
    }

    /**
     * @param Builder $builder
     * @return void
     */
    public function scopeTeamLead(Builder $builder): void
    {
        $builder->leftJoin('users', 'users.id', '=', 'teams.team_lead_id');
    }

    /**
     * @param Builder $builder
     * @return void
     */
    public function scopeUserCount(Builder $builder): void
    {
        $subQuery = DB::table('users')
            ->selectRaw('count(*) as count, team_id')
            ->groupBy('team_id');

        $builder->leftJoinSub($subQuery, 'uc', function ($join) {
            $join->on('uc.team_id', '=', 'teams.id');
        });
    }
}
