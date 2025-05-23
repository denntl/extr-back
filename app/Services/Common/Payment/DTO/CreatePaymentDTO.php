<?php

namespace App\Services\Common\Payment\DTO;

class CreatePaymentDTO
{
    /**
     * @param int $processorId
     * @param int $balanceTransactionId
     * @param int $status
     * @param string|null $invoiceId
     * @param string|null $paymentId
     * @param string|null $comment
     */
    public function __construct(
        public int $processorId,
        public int $balanceTransactionId,
        public int $status,
        public ?string $invoiceId = null,
        public ?string $paymentId = null,
        public ?string $comment = null,
    ) {
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'processor_id' => $this->processorId,
            'balance_transaction_id' => $this->balanceTransactionId,
            'status' => $this->status,
            'invoice_id' => $this->invoiceId ?? '',
            'payment_id' => $this->payment_id ?? '',
            'comment' => $this->comment ?? '',
        ];
    }
}
