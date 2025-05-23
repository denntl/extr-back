<?php

namespace App\Services\Common\BalanceTransaction\Handlers\DTO;

use App\Enums\Balance\Type as BalanceType;
use App\Enums\BalanceTransaction\Type;
use App\Services\Common\BalanceTransaction\Handlers\DTO\Common\BaseDTO;
use App\Services\Common\BalanceTransaction\Handlers\Interfaces\CreateTransactionDTOInterface;

class ManualPaymentDTO extends BaseDTO implements CreateTransactionDTOInterface
{
    public function __construct(
        public int $companyId,
        public BalanceType $balanceType,
        public string $comment,
        public float $amount,
        public int $userId,
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
     * @return ManualPaymentDTO
     */
    public static function fromArray(array $data): ManualPaymentDTO
    {
        return new self(
            companyId : $data['companyId'],
            balanceType: BalanceType::tryFrom($data['balanceType']) ?? throw new \InvalidArgumentException('Invalid balance type'),
            comment: $data['comment'],
            amount: $data['amount'],
            userId: $data['userId'],
        );
    }
}
