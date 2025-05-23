<?php

namespace App\Services\Common\BalanceTransaction\Handlers\DTO\Common;

use App\Enums\BalanceTransaction\Status;

class ResponseHandleDTO
{
    public function __construct(
        public float $amount,
        public Status $balanceTransactionStatus,
    ) {
    }

    /**
     * @return Status
     */
    public function getBalanceTransactionStatus(): Status
    {
        return $this->balanceTransactionStatus;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }
}
