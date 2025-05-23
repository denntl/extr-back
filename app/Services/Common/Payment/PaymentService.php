<?php

namespace App\Services\Common\Payment;

use App\Enums\Payment\Processor;
use App\Models\Payment;
use App\Services\Common\Payment\DTO\CreatePaymentDTO;
use Illuminate\Support\Collection;

class PaymentService
{
    /**
     * @param CreatePaymentDTO $data
     * @return Payment
     */
    public function create(CreatePaymentDTO $data): Payment
    {
        return Payment::query()->create($data->toArray());
    }

    /**
     * @return array[]
     */
    public static function getProcessorTypeList(): array
    {
        return [
            ['value' => Processor::Manual->value, 'label' => 'Manual'],
            ['value' => Processor::NowPayments->value, 'label' => 'Crypto'],
        ];
    }

    /**
     * @param int $balanceTransactionId
     * @param array $data
     * @return Payment
     */
    public function updateByBalanceTransactionId(int $balanceTransactionId, array $data): Payment
    {
        $payment = Payment::query()->where('balance_transaction_id', $balanceTransactionId)->firstOrFail();
        $payment->update($data);

        return $payment;
    }

    public function getByInvoiceId($invoiceId): Collection
    {
        return Payment::query()->where('invoice_id', $invoiceId)->get();
    }
}
