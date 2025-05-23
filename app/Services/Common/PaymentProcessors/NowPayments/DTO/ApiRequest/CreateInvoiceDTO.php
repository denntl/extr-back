<?php

namespace App\Services\Common\PaymentProcessors\NowPayments\DTO\ApiRequest;

use ErrorException;
use Illuminate\Support\Facades\Validator;

class CreateInvoiceDTO
{
    /**
     * @param float $priceAmount
     * @param string $priceCurrency
     * @param string $orderId
     * @param string $orderDescription
     * @param string $ipnCallbackUrl
     * @param string $successUrl
     * @param string $cancelUrl
     * @throws ErrorException
     */
    public function __construct(
        public float $priceAmount,
        public string $priceCurrency,
        public string $orderId,
        public string $orderDescription,
        public string $ipnCallbackUrl,
        public string $successUrl,
        public string $cancelUrl,
    ) {
        $this->validate();
    }

    /**
     * @return void
     * @throws ErrorException
     */
    public function validate(): void
    {
        $validator = Validator::make([
            'priceAmount' => $this->priceAmount,
            'priceCurrency' => $this->priceCurrency,
            'orderId' => $this->orderId,
            'orderDescription' => $this->orderDescription,
            'ipnCallbackUrl' => $this->ipnCallbackUrl,
            'successUrl' => $this->successUrl,
            'cancelUrl' => $this->cancelUrl,
        ], [
            'priceAmount' => 'required|numeric',
            'priceCurrency' => 'required|string',
            'orderId' => 'required|string',
            'orderDescription' => 'required|string',
            'ipnCallbackUrl' => 'required|string|url',
            'successUrl' => 'required|string|url',
            'cancelUrl' => 'required|string|url',
        ]);

        if ($validator->fails()) {
            throw new ErrorException($validator->errors()->toJson());
        }
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'price_amount' => $this->priceAmount,
            'price_currency' => $this->priceCurrency,
            'order_id' => $this->orderId,
            'order_description' => $this->orderDescription,
            'ipn_callback_url' => $this->ipnCallbackUrl,
            'success_url' => $this->successUrl,
            'cancel_url' => $this->cancelUrl,
        ];
    }
}
