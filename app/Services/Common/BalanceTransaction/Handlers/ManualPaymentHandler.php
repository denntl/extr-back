<?php

namespace App\Services\Common\BalanceTransaction\Handlers;

use App\Enums\Balance\Type;
use App\Enums\BalanceTransaction\Status;
use App\Enums\BalanceTransaction\Status as BalanceTransactionStatus;
use App\Enums\Payment\Processor;
use App\Models\BalanceTransaction;
use App\Services\Common\BalanceTransaction\Handlers\DTO\Common\ResponseHandleDTO;
use App\Services\Common\BalanceTransaction\Handlers\DTO\ManualPaymentDTO;
use App\Services\Common\BalanceTransaction\Handlers\Interfaces\CreateTransactionDTOInterface;
use App\Services\Common\BalanceTransaction\Handlers\Interfaces\HandlerInterface;
use App\Services\Common\Payment\DTO\CreatePaymentDTO;
use App\Services\Common\Payment\PaymentService;

class ManualPaymentHandler extends BaseHandler implements HandlerInterface
{
    /**
     * @param CreateTransactionDTOInterface $data
     * @return Type
     */
    public function getBalanceType(CreateTransactionDTOInterface $data): Type
    {
        /** @var ManualPaymentDTO $data */
        $this->validateDTO($data, ManualPaymentDTO::class);

        return $data->balanceType;
    }

    /**
     * @param CreateTransactionDTOInterface $data
     * @return float
     */
    public function getAmount(CreateTransactionDTOInterface $data): float
    {
        /** @var ManualPaymentDTO $data */
        $this->validateDTO($data, ManualPaymentDTO::class);

        return $data->amount;
    }

    /**
     * @param CreateTransactionDTOInterface $data
     * @param BalanceTransaction $balanceTransaction
     * @return array
     */
    public function create(CreateTransactionDTOInterface $data, BalanceTransaction $balanceTransaction): array
    {
        /** @var ManualPaymentDTO $data */
        $this->validateDTO($data, ManualPaymentDTO::class);

        /** @var PaymentService $paymentService */
        $paymentService = app(PaymentService::class);
        $paymentService->create(new CreatePaymentDTO(
            processorId: Processor::Manual->value,
            balanceTransactionId: $balanceTransaction->id,
            status: Status::Pending->value,
            comment: $data->comment,
        ));

        return [];
    }

    /**
     * @param BalanceTransaction $balanceTransaction
     * @param array $data
     * @return ResponseHandleDTO
     */
    public function handle(BalanceTransaction $balanceTransaction, array $data = []): ResponseHandleDTO
    {
        /** @var PaymentService $paymentService */
        $paymentService = app(PaymentService::class);
        $paymentService->updateByBalanceTransactionId($balanceTransaction->id, [
            'status' => BalanceTransactionStatus::Approved->value,
        ]);

        return new ResponseHandleDTO(
            $balanceTransaction->amount,
            BalanceTransactionStatus::Approved
        );
    }
}
