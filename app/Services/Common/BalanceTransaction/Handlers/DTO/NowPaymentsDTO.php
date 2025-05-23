<?php

namespace App\Services\Common\BalanceTransaction\Handlers\DTO;

use App\Enums\Balance\Type as BalanceType;
use App\Enums\BalanceTransaction\Type;
use App\Services\Common\BalanceTransaction\Handlers\DTO\Common\BaseDTO;
use App\Services\Common\BalanceTransaction\Handlers\Interfaces\CreateTransactionDTOInterface;

class NowPaymentsDTO extends BaseDTO implements CreateTransactionDTOInterface
{
    public BalanceType $balanceType = BalanceType::Balance;
    public function __construct(
        public int $companyId,
        public float $amount,
        public int $userId,
        public string $comment = '',
    ) {
    }

    /**
     * @return int
     */
    public function getCompanyId(): int
    {
        return $this->companyId;
    }

    /**
     * @return Type
     */
    public function getType(): Type
    {
        return Type::Deposit;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @param array $data
     * @return NowPaymentsDTO
     */
    public static function fromArray(array $data): NowPaymentsDTO
    {
        return new self(
            companyId : $data['companyId'],
            amount: $data['amount'],
            userId: $data['userId'],
            comment: $data['comment'] ?? '',
        );
    }
}
