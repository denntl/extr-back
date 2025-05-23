<?php

namespace App\Models;

use App\Services\Common\PaymentProcessors\NowPayments\DTO\ApiRequest\CreateInvoiceDTO;
use ErrorException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class BalanceTransaction
 * @package App\Models
 * @property int $id
 * @property int $company_id
 * @property int $user_id
 * @property float $amount
 * @property float $balance_before
 * @property float $balance_after
 * @property int $balance_type
 * @property int $type
 * @property int $status
 * @property Company $company
 * @property User $user
 */
class BalanceTransaction extends Model
{
    use HasFactory;

    protected $casts = [
        'amount' => 'float',
        'balance_before' => 'float',
        'balance_after' => 'float',
    ];

    protected $fillable = [
        'company_id', 'user_id', 'amount', 'balance_before', 'balance_after', 'balance_type', 'type', 'status'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function scopePayment(Builder $builder)
    {
        $builder->leftJoin('payments', 'payments.balance_transaction_id', '=', 'balance_transactions.id');
    }

    public function scopeCompanyId(Builder $builder, int $companyId)
    {
        $builder->where('balance_transactions.company_id', $companyId);
    }

    public function scopeApplication(Builder $builder)
    {
        $builder->leftJoin('balance_transaction_applications', 'balance_transactions.id', '=', 'balance_transaction_applications.balance_transaction_id')
                ->leftJoin('applications', 'applications.id', '=', 'balance_transaction_applications.application_id');
    }

    /**
     * @throws ErrorException
     */
    public function toCreateInvoiceDTO(): CreateInvoiceDTO
    {
        return new CreateInvoiceDTO(
            $this->amount,
            'usd',
            $this->id,
            'Balance transaction #' . $this->id,
            url('/api/nowpayments/webhook'),
            config('app.frontend_url') . '/admin/my-company/transactions',
            config('app.frontend_url') . '/admin/my-company/transactions'
        );
    }
}
