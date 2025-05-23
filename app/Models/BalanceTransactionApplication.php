<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BalanceTransactionApplication extends Model
{
    use HasFactory;

    protected $fillable = ['balance_transaction_id', 'application_id'];

    /**
     * @return BelongsTo
     */
    public function balanceTransaction(): BelongsTo
    {
        return $this->belongsTo(BalanceTransaction::class);
    }

    /**
     * @return BelongsTo
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }
}
