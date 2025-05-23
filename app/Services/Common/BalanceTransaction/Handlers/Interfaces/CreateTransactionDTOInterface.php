<?php

namespace App\Services\Common\BalanceTransaction\Handlers\Interfaces;

use App\Enums\Balance\Type as BalanceType;
use App\Enums\BalanceTransaction\Type;

/**
 * Interface CreateTransactionDTOInterface
 * @readonly property int $companyId
 * @readonly property BalanceType $balanceType,
 * @readonly property string $comment,
 * @readonly property float $amount,
 * @readonly property int $userId,
 */
interface CreateTransactionDTOInterface
{
    public function getCompanyId(): int;

    public function getType(): Type;

    public function getUserId(): ?int;
}
