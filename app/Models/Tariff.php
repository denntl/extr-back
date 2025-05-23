<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Tariff
 *
 * @property int $id
 * @property string $name
 * @property int $type_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Tariff extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type_id',
    ];

    public $timestamps = true;

    public function tiers(): HasMany
    {
        return $this->hasMany(Tier::class, 'tariff_id');
    }
}
