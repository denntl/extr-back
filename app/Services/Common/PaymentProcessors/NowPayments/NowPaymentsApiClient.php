<?php

namespace App\Services\Common\PaymentProcessors\NowPayments;

use App\Services\Common\PaymentProcessors\NowPayments\DTO\ApiRequest\CreateInvoiceDTO;
use App\Services\Common\PaymentProcessors\NowPayments\DTO\ApiResponse\CreateInvoiceResponseDTO;
use ErrorException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NowPaymentsApiClient
{
    protected string $apiKey;
    public const CREATE_INVOICE_URL = 'https://api.nowpayments.io/v1/invoice';

    public function __construct()
    {
        $this->apiKey = config('services.nowpayments.api_key');

        if (empty($this->apiKey)) {
            throw new ErrorException('NowPayments API key is not set');
        }
    }

    /**
     * @param string $url
     * @param array $headers
     * @param string $method
     * @param array $data
     * @return mixed
     * @throws ErrorException
     */
    private function sendRequest(string $url, array $headers, string $method, array $data = []): mixed
    {
        $requestConfig = ['headers' => $headers];

        if (!empty($data)) {
            $requestConfig['json'] = $data;
        }

        try {
            $response = Http::send($method, $url, $requestConfig)->json();

            if (!empty($response['errors'])) {
                Log::info("NowPayments sendRequest error request", $data);
                Log::info("NowPayments sendRequest error response", $response);
                throw new ErrorException(
                    message: "Error sendRequest method: " . implode(',', $response['errors']),
                    code: 400
                );
            }

            return $response;
        } catch (\Throwable $e) {
            throw new ErrorException(
                message: "NowPayments request error: {$e->getMessage()}",
                code: $e->getCode(),
                previous: $e
            );
        }
    }

    /**
     * @param CreateInvoiceDTO $data
     * @return CreateInvoiceResponseDTO
     * @throws ErrorException
     */
    public function createInvoice(CreateInvoiceDTO $data): CreateInvoiceResponseDTO
    {
        try {
            return new CreateInvoiceResponseDTO($this->sendRequest(
                self::CREATE_INVOICE_URL,
                [
                    'x-api-key' => $this->apiKey,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'post',
                array_merge($data->toArray(), [
                    'is_fixed_rate' => true,
                    'is_fee_paid_by_user' => true,
                ])
            ));
        } catch (\Throwable $e) {
            throw new ErrorException(
                message: "Error while create invoice: {$e->getMessage()}",
                code: $e->getCode(),
                previous: $e
            );
        }
    }
}
