<?php

namespace App\Models;

use App\Models\Traits\WithExternalId;
use App\Models\Traits\WithNumberExternalId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class Domain
 *
 * @property int $id
 * @property string $domain
 * @property int $status
 * @property string|null $took_at
 * @property string|null $banned_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Domain extends Model
{
    use HasFactory;

    protected $fillable = [
        'domain',
        'status',
        'took_at',
        'banned_at',
    ];
}
