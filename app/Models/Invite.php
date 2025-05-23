<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Invite
 * @package App\Models
 *
 * @property string $key Unique key of invite
 * @property string $expire_at Date time string when invite expires
 * @property int $company_id ID of company in what user was invited
 * @property string $action
 * @property array $body
 * @property int $created_by
 */
class Invite extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'key',
        'expire_at',
        'company_id',
        'action',
        'body',
        'created_by',
    ];

    protected $casts = [
        'body' => 'array',
    ];

    /**
     * Get the company that the invite belongs to.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the team that the invite belongs to.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
