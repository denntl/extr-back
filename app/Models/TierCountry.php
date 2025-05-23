<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TierCountry extends Model
{
    use HasFactory;

    protected $fillable = [
        'tier_id',
        'country',
    ];

    public $timestamps = true;

    public function tier(): BelongsTo
    {
        return $this->belongsTo(Tier::class, 'tier_id');
    }
}
