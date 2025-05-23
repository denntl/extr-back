<?php

namespace App\Models;

use App\Enums\BalanceTransaction\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Payment
 * @package App\Models
 * @property int $id
 * @property int $processor_id
 * @property int $balance_transaction_id
 * @property string $invoice_id
 * @property string $payment_id
 * @property int $status
 * @property string $comment
 * @property BalanceTransaction $balanceTransaction
 * @property Company $company
 */
class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'processor_id', 'balance_transaction_id', 'invoice_id', 'payment_id', 'status', 'comment'
    ];

    public function balanceTransaction(): BelongsTo
    {
        return $this->belongsTo(BalanceTransaction::class);
    }

    public function hasFinalStatus(): bool
    {
        return $this->status !== Status::Pending->value;
    }
}
