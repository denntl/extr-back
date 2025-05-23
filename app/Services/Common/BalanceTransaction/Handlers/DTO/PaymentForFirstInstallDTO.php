<?php

namespace App\Services\Common\BalanceTransaction\Handlers\DTO;

use App\Enums\BalanceTransaction\Type;
use App\Services\Common\BalanceTransaction\Handlers\DTO\Common\BaseDTO;
use App\Services\Common\BalanceTransaction\Handlers\Interfaces\CreateTransactionDTOInterface;

class PaymentForFirstInstallDTO extends BaseDTO implements CreateTransactionDTOInterface
{
    public function __construct(
        public int $companyId,
        public int $tariffId,
        public string $country
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
     * @return int
     */
    public function getTariffId(): int
    {
        return $this->tariffId;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @return Type
     */
    public function getType(): Type
    {
        return Type::Install;
    }

    /**
     * @param array $data
     * @return PaymentForFirstInstallDTO
     */
    public static function fromArray(array $data): PaymentForFirstInstallDTO
    {
        return new self(
            companyId : $data['companyId'],
            tariffId : $data['companyTariffId'],
            country : $data['country']
        );
    }
}
