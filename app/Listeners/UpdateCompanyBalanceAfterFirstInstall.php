<?php

namespace App\Listeners;

use App\Enums\PwaEvents\Event;
use App\Events\PwaClientEventCreated;
use App\Services\Common\BalanceTransaction\BalanceTransactionApplicationService;
use App\Services\Common\BalanceTransaction\BalanceTransactionService;
use App\Services\Common\BalanceTransaction\Handlers\DTO\PaymentForFirstInstallDTO;
use App\Services\Common\BalanceTransaction\Handlers\PaymentForFirstInstallHandler;
use App\Services\Common\Company\CompanyService;
use App\Services\Common\Tariff\Enums\Type;
use Throwable;

class UpdateCompanyBalanceAfterFirstInstall
{
    /**
     * Handle the event.
     * @throws Throwable
     */
    public function handle(PwaClientEventCreated $event): void
    {
        if (
            $event->pwaClientEvent->event !== Event::Install->value ||
            !$event->pwaClientEvent->is_first ||
            !$event->pwaClientEvent->pwaClientClick->country
        ) {
            return;
        }

        /** @var CompanyService $companyService */
        $companyService = app(CompanyService::class);
        $companyTariff = $companyService->getTariffByCompanyId($event->pwaClientEvent->getCompanyId());
        if (empty($companyTariff) || $companyTariff->type_id !== Type::Install->value) {
            return;
        }

        try {
            /** @var BalanceTransactionService $balanceTransactionService */
            $balanceTransactionService = app(BalanceTransactionService::class, [
                'handler' => new PaymentForFirstInstallHandler($event)
            ]);

            $balanceTransactionServiceCreateDTO = $balanceTransactionService->create(
                PaymentForFirstInstallDTO::fromArray([
                    'companyId' => $event->pwaClientEvent->getCompanyId(),
                    'companyTariffId' => $companyTariff->id,
                    'country' => $event->pwaClientEvent->pwaClientClick->country
                ])
            );
            $balanceTransaction = $balanceTransactionServiceCreateDTO->getBalanceTransaction();

            /** @var BalanceTransactionApplicationService $balanceTransactionApplicationService */
            $balanceTransactionApplicationService = app(BalanceTransactionApplicationService::class);
            $balanceTransactionApplicationService->create(
                $balanceTransaction->id,
                $event->pwaClientEvent->getApplicationId()
            );
            $balanceTransactionService->handle($balanceTransaction);
        } catch (\Throwable $throwable) {
            logger()->error('UpdateCompanyBalanceAfterFirstInstall@handle', [
                'error' => $throwable->getMessage(),
                'trace' => $throwable->getTrace(),
                'event' => $event->pwaClientEvent->toArray(),
            ]);
        }
    }
}
