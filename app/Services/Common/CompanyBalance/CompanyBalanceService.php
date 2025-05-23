<?php

namespace App\Services\Common\CompanyBalance;

use App\Enums\Balance\Type;
use App\Models\CompanyBalance;

class CompanyBalanceService
{
    /**
     * @param int $id
     * @return string
     */
    public static function getColumnNameByTypeId(int $id): string
    {
        return match ($id) {
            Type::Balance->value => 'balance',
            Type::BalanceBonus->value => 'balance_bonus',
            default => throw new \InvalidArgumentException('Invalid balance type'),
        };
    }

    /**
     * @return array[]
     */
    public static function getTypes(): array
    {
        return [
            ['value' => Type::Balance->value, 'label' => 'Основной'],
            ['value' => Type::BalanceBonus->value, 'label' => 'Бонусный'],
        ];
    }

    /**
     * @param $companyId
     * @param Type $balanceType
     * @return float
     */
    public function getBalanceByCompanyId($companyId, Type $balanceType): float
    {
        return CompanyBalance::query()
            ->where('company_id', $companyId)
            ->firstOrFail()
            ->{$this->getColumnNameByTypeId($balanceType->value)};
    }
}
