<?php

namespace App\Services\Common\PaymentProcessors\NowPayments\DTO\ApiResponse;

use ErrorException;
use Illuminate\Support\Facades\Validator;

class CreateInvoiceResponseDTO
{
    /**
     * @param array $response
     * @throws ErrorException
     */
    public function __construct(public array $response)
    {
        $this->validate();
    }

    /**
     * @return void
     * @throws ErrorException
     */
    public function validate(): void
    {
        $validator = Validator::make(['response' => $this->response], [
            'response.id' => 'required|string',
            'response.invoice_url' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new ErrorException($validator->errors()->toJson());
        }
    }

    /**
     * @return string
     */
    public function getInvoiceId(): string
    {
        return $this->response['id'];
    }

    /**
     * @return string
     */
    public function getInvoiceUrl(): string
    {
        return $this->response['invoice_url'];
    }
}
