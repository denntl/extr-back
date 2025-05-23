<?php

namespace App\Services\Common\CompanyBalance\DTO;

class CompanyBalanceUpdatedDTO
{
    /**
     * @param int $company_id
     * @param float $amount_before
     * @param float $amount_after
     */
    public function __construct(
        public int $company_id,
        public float $amount_before,
        public float $amount_after
    ) {
    }

    public function toArray(): array
    {
        return [
            'company_id' => $this->company_id,
            'amount_before' => $this->amount_before,
            'amount_after' => $this->amount_after,
        ];
    }
}
