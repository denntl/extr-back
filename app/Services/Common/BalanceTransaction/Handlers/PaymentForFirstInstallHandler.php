<?php

namespace App\Services\Common\BalanceTransaction\Handlers;

use App\Enums\Balance\Type;
use App\Enums\BalanceTransaction\Status as BalanceTransactionStatus;
use App\Events\PwaClientEventCreated;
use App\Models\BalanceTransaction;
use App\Services\Common\BalanceTransaction\Handlers\DTO\Common\ResponseHandleDTO;
use App\Services\Common\BalanceTransaction\Handlers\DTO\PaymentForFirstInstallDTO;
use App\Services\Common\BalanceTransaction\Handlers\Interfaces\CreateTransactionDTOInterface;
use App\Services\Common\BalanceTransaction\Handlers\Interfaces\HandlerInterface;
use App\Services\Common\CompanyBalance\CompanyBalanceService;
use App\Services\Common\Tariff\TierCountryService;

class PaymentForFirstInstallHandler extends BaseHandler implements HandlerInterface
{
    public function __construct(public PwaClientEventCreated $event)
    {
    }

    /**
     * @param CreateTransactionDTOInterface $data
     * @return Type
     */
    public function getBalanceType(CreateTransactionDTOInterface $data): Type
    {
        /** @var PaymentForFirstInstallDTO $data */
        $this->validateDTO($data, PaymentForFirstInstallDTO::class);

        /** @var CompanyBalanceService $companyBalanceService */
        $companyBalanceService = app(CompanyBalanceService::class);
        $bonusCompanyBalance = $companyBalanceService->getBalanceByCompanyId($data->companyId, Type::BalanceBonus);

        return $bonusCompanyBalance >= abs($this->getAmount($data)) ? Type::BalanceBonus : Type::Balance;
    }

    /**
     * @param CreateTransactionDTOInterface $data
     * @return float
     */
    public function getAmount(CreateTransactionDTOInterface $data): float
    {
        /** @var PaymentForFirstInstallDTO $data */
        $this->validateDTO($data, PaymentForFirstInstallDTO::class);

        /** @var TierCountryService $tierCountryService */
        $tierCountryService = app(TierCountryService::class);

        return -$tierCountryService->getTierPriceByTariffIdCountry($data->tariffId, $data->country);
    }

    /**
     * @param CreateTransactionDTOInterface $data
     * @param BalanceTransaction $balanceTransaction
     * @return array
     */
    public function create(CreateTransactionDTOInterface $data, BalanceTransaction $balanceTransaction): array
    {
        return [];
    }

    /**
     * @param BalanceTransaction $balanceTransaction
     * @param array $data
     * @return ResponseHandleDTO
     */
    public function handle(BalanceTransaction $balanceTransaction, array $data = []): ResponseHandleDTO
    {
        return new ResponseHandleDTO(
            $balanceTransaction->amount,
            BalanceTransactionStatus::Approved
        );
    }
}
