<?php

namespace App\Services\Common\BalanceTransaction;

use App\Models\BalanceTransactionApplication;

class BalanceTransactionApplicationService
{
    public function create(int $balanceTransactionId, int $applicationId): BalanceTransactionApplication
    {
        return BalanceTransactionApplication::query()->create([
            'balance_transaction_id' => $balanceTransactionId,
            'application_id' => $applicationId,
        ]);
    }
}
