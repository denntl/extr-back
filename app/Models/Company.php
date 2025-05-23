<?php

namespace App\Models;

use App\Models\Traits\Contracts\WithExternalIdInterface;
use App\Models\Traits\WithExternalId;
use App\Models\Traits\WithStringExternalId;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class Company
 * @property int $tariff_id
 * @property-read Tariff $tariff
 * @property-read User $owner
 * @property-read User $users
 * @property-read CompanyBalance $balances
 * @property-read BalanceTransaction $balanceTransactions
 * @property-read Payment $payments
 */
class Company extends Model implements WithExternalIdInterface
{
    use HasFactory;
    use WithExternalId;
    use WithStringExternalId;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'owner_id',
        'public_id',
        'tariff_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];

    /**
     * Get the user that owns the company.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * @return BelongsTo
     */
    public function tariff(): BelongsTo
    {
        return $this->belongsTo(Tariff::class, 'tariff_id', 'id');
    }

    /**
     * Get the users for the company.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function scopeUserCount(Builder $builder): void
    {
        $subQuery = DB::table('users')
            ->selectRaw('count(*) as count, company_id')
            ->groupBy('company_id');

        $builder->leftJoinSub($subQuery, 'uc', function ($join) {
            $join->on('uc.company_id', '=', 'companies.id');
        });
    }

    public function scopeTeamCount(Builder $builder): void
    {
        $subQuery = DB::table('teams')
            ->selectRaw('count(*) as count, company_id')
            ->groupBy('company_id');

        $builder->leftJoinSub($subQuery, 'tc', function ($join) {
            $join->on('tc.company_id', '=', 'companies.id');
        });
    }

    public function scopeOwner(Builder $builder): void
    {
        $builder->leftJoin('users', 'users.id', '=', 'companies.owner_id');
    }
    public function balances(): HasOne
    {
        return $this->hasOne(CompanyBalance::class);
    }

    public function balanceTransactions(): HasMany
    {
        return $this->hasMany(BalanceTransaction::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
