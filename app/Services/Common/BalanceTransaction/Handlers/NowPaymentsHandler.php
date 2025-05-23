<?php

namespace App\Services\Common\BalanceTransaction\Handlers;

use App\Enums\Balance\Type;
use App\Enums\BalanceTransaction\Status as BalanceTransactionStatus;
use App\Enums\Payment\Processor;
use App\Models\BalanceTransaction;
use App\Services\Common\BalanceTransaction\Handlers\DTO\Common\ResponseHandleDTO;
use App\Services\Common\BalanceTransaction\Handlers\DTO\NowPaymentsDTO;
use App\Services\Common\BalanceTransaction\Handlers\Interfaces\CreateTransactionDTOInterface;
use App\Services\Common\BalanceTransaction\Handlers\Interfaces\HandlerInterface;
use App\Services\Common\Payment\DTO\CreatePaymentDTO;
use App\Services\Common\Payment\PaymentService;
use App\Services\Common\PaymentProcessors\NowPayments\NowPaymentsService;
use ErrorException;

class NowPaymentsHandler extends BaseHandler implements HandlerInterface
{
    /**
     * @param CreateTransactionDTOInterface $data
     * @return Type
     */
    public function getBalanceType(CreateTransactionDTOInterface $data): Type
    {
        /** @var NowPaymentsDTO $data */
        $this->validateDTO($data, NowPaymentsDTO::class);

        return $data->balanceType;
    }

    /**
     * @param CreateTransactionDTOInterface $data
     * @return float
     */
    public function getAmount(CreateTransactionDTOInterface $data): float
    {
        /** @var NowPaymentsDTO $data */
        $this->validateDTO($data, NowPaymentsDTO::class);

        return $data->amount;
    }

    /**
     * @param CreateTransactionDTOInterface $data
     * @param BalanceTransaction $balanceTransaction
     * @return array
     * @throws ErrorException
     */
    public function create(CreateTransactionDTOInterface $data, BalanceTransaction $balanceTransaction): array
    {
        /** @var NowPaymentsDTO $data */
        $this->validateDTO($data, NowPaymentsDTO::class);

        /** @var NowPaymentsService $nowPaymentService */
        $nowPaymentService = app(NowPaymentsService::class);
        $createInvoiceResponseDTO = $nowPaymentService->createInvoice($balanceTransaction);

        /** @var PaymentService $paymentService */
        $paymentService = app(PaymentService::class);
        $paymentService->create(new CreatePaymentDTO(
            processorId: Processor::NowPayments->value,
            balanceTransactionId: $balanceTransaction->id,
            status: BalanceTransactionStatus::Pending->value,
            invoiceId: $createInvoiceResponseDTO->getInvoiceId(),
            comment: $data->comment,
        ));

        return [
            'redirectUrl' => $createInvoiceResponseDTO->getInvoiceUrl(),
        ];
    }

    /**
     * @param BalanceTransaction $balanceTransaction
     * @param array $data
     * @return ResponseHandleDTO
     */
    public function handle(BalanceTransaction $balanceTransaction, array $data = []): ResponseHandleDTO
    {
        /** @var NowPaymentsService $nowPaymentService */
        $nowPaymentService = app(NowPaymentsService::class);
        $balanceTransactionStatus = $nowPaymentService->getBalanceTransactionStatus($data['payment_status']);

        /** @var PaymentService $paymentService */
        $paymentService = app(PaymentService::class);
        $paymentService->updateByBalanceTransactionId($balanceTransaction->id, [
            'status' => $balanceTransactionStatus->value,
            'payment_id' => $data['payment_id'],
        ]);

        return new ResponseHandleDTO($data['outcome_amount'], $balanceTransactionStatus);
    }
}
