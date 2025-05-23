<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Tier
 * @property int $id
 */
class Tier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'tariff_id',
    ];

    public $timestamps = true;

    public function tariff(): BelongsTo
    {
        return $this->belongsTo(Tariff::class, 'tariff_id');
    }

    public function countries(): HasMany
    {
        return $this->hasMany(TierCountry::class, 'tier_id');
    }

    public function countriesBelongsTo(): BelongsToMany
    {
        return $this->belongsToMany(TierCountry::class, 'tier_countries', 'tier_id', 'country');
    }
}
