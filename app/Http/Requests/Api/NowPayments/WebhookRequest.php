<?php

namespace App\Http\Requests\Api\NowPayments;

use App\Enums\NowPayments\PaymentStatus;
use App\Rules\InEnum;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

class WebhookRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'invoice_id' => ['required', 'numeric', 'exists:payments,invoice_id'],
            'payment_id' => ['required', 'numeric'],
            'payment_status' => ['required', 'string', new InEnum(PaymentStatus::class)],
            'order_id' => ['required', 'string'],
            'outcome_amount' => ['required', 'numeric'],
            'pay_address' => ['required', 'string'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        Log::channel('payments')->info('Webhook Error', [
            'errors' => $validator->errors()->toJson(),
            'request' => $this->all()
        ]);

        throw new HttpResponseException(response()->json([]));
    }
}
