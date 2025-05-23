<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'balance' => 'float',
        'balance_bonus' => 'float',
    ];

    protected $casts = [
        'balance' => 'float',
        'balance_bonus' => 'float',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
