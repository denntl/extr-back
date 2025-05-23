<?php

namespace App\Services\Common\BalanceTransaction\DTO;

use App\Models\BalanceTransaction;

class BalanceTransactionCreateDTO
{
    public function __construct(
        public BalanceTransaction $balanceTransaction,
        public array $handlerResponse
    ) {
    }

    /**
     * @return BalanceTransaction
     */
    public function getBalanceTransaction(): BalanceTransaction
    {
        return $this->balanceTransaction;
    }

    /**
     * @return array
     */
    public function getHandlerResponse(): array
    {
        return $this->handlerResponse;
    }
}
