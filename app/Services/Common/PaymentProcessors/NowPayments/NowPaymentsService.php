<?php

namespace App\Services\Common\PaymentProcessors\NowPayments;

use App\Enums\NowPayments\PaymentStatus;
use App\Enums\BalanceTransaction\Status;
use App\Http\Requests\Api\NowPayments\WebhookRequest;
use App\Models\BalanceTransaction;
use App\Services\Common\BalanceTransaction\BalanceTransactionService;
use App\Services\Common\BalanceTransaction\Handlers\NowPaymentsHandler;
use App\Services\Common\Payment\PaymentService;
use App\Services\Common\PaymentProcessors\NowPayments\DTO\ApiResponse\CreateInvoiceResponseDTO;
use ErrorException;
use Throwable;

readonly class NowPaymentsService
{
    /**
     * @throws ErrorException
     */
    public function createInvoice(BalanceTransaction $balanceTransaction): CreateInvoiceResponseDTO
    {
        /** @var NowPaymentsApiClient $nowPaymentsApiClient */
        $nowPaymentsApiClient = app(NowPaymentsApiClient::class);

        return $nowPaymentsApiClient->createInvoice($balanceTransaction->toCreateInvoiceDTO());
    }

    /**
     * @param string $status
     * @return Status
     */
    public function getBalanceTransactionStatus(string $status): Status
    {
        return match ($status) {
            PaymentStatus::PartiallyPaid->value, PaymentStatus::Finished->value => Status::Approved,
            PaymentStatus::Failed->value => Status::Declined,
            PaymentStatus::Expired->value => Status::Canceled,
            default => Status::Pending,
        };
    }

    /**
     * @param $array
     * @return void
     */
    public static function tkSort(&$array): void
    {
        ksort($array);
        foreach (array_keys($array) as $k) {
            if (gettype($array[$k]) == "array") {
                self::tkSort($array[$k]);
            }
        }
    }

    /**
     * @param string $ipnSecretKey
     * @param string $headerKey
     * @param string|null $requestJson
     * @return void
     * @throws ErrorException
     */
    public static function checkIpnRequestIsValid(string $ipnSecretKey, string $headerKey, string $requestJson = null): void
    {
        if (empty($headerKey)) {
            throw new ErrorException('HMAC signature is empty');
        }
        if (empty($ipnSecretKey)) {
            throw new ErrorException('IPN secret key is empty');
        }
        $requestJson = $requestJson ?? request()->getContent();
        if (empty($requestJson)) {
            throw new ErrorException('Request JSON is empty');
        }
        $hmac = self::generateIpnToken($requestJson, $ipnSecretKey);
        if ($hmac !== $headerKey) {
            throw new ErrorException('HMAC signature does not match');
        }
    }

    /**
     * @param string $requestJson
     * @param string $ipnSecretKey
     * @return string
     * @throws ErrorException
     */
    public static function generateIpnToken(string $requestJson, string $ipnSecretKey): string
    {
        if (empty($requestJson)) {
            throw new ErrorException('Request JSON is empty');
        }
        if (empty($ipnSecretKey)) {
            throw new ErrorException('IPN secret key is empty');
        }
        $requestData = json_decode($requestJson, true);
        self::tkSort($requestData);
        $sortedRequestJson = json_encode($requestData, JSON_UNESCAPED_SLASHES);

        return hash_hmac("sha512", $sortedRequestJson, trim($ipnSecretKey));
    }

    /**
     * @param WebhookRequest $request
     * @return bool
     * @throws Throwable
     */
    public static function webhookHandle(WebhookRequest $request): bool
    {
        $data = $request->validated();
        /** @var PaymentService $paymentService */
        $paymentService = app(PaymentService::class);
        $allPaymentsByInvoiceId = $paymentService->getByInvoiceId($data['invoice_id']);

        /** @var BalanceTransactionService $balanceTransactionService */
        $balanceTransactionService = app(BalanceTransactionService::class, [
            'handler' => new NowPaymentsHandler()
        ]);

        foreach ($allPaymentsByInvoiceId as $payment) {
            if (empty($payment->payment_id) || $payment->payment_id == $data['payment_id']) {
                if ($payment->hasFinalStatus()) {
                    return true;
                }
                $balanceTransaction = $balanceTransactionService->getById($payment->balance_transaction_id);
                $balanceTransactionService->handle($balanceTransaction, $data);

                return true;
            }
        }

        if ($allPaymentsByInvoiceId->count() > 0) {
            $firstPayment = $allPaymentsByInvoiceId->first();
            $balanceTransaction = $balanceTransactionService->getById($firstPayment->balance_transaction_id);
            $newBalanceTransaction = $balanceTransactionService->cloneBalanceTransactionWithPayment(
                $balanceTransaction,
                $firstPayment
            );
            $balanceTransactionService->handle($newBalanceTransaction, $data);

            return true;
        }

        return false;
    }
}
