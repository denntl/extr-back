<?php

namespace Feature\Admin\Client\MyCompany;

use App\Enums\Authorization\PermissionName;
use App\Enums\Balance\Type;
use App\Enums\BalanceTransaction\Status;
use App\Enums\NowPayments\PaymentStatus;
use App\Models\BalanceTransaction;
use App\Models\Payment;
use App\Services\Common\CompanyBalance\CompanyBalanceService;
use App\Services\Common\PaymentProcessors\NowPayments\NowPaymentsApiClient;
use App\Services\Common\PaymentProcessors\NowPayments\NowPaymentsService;
use ErrorException;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CreateInvoiceWithApprovedPaymentTest extends TestCase
{
    use WithFaker;

    /**
     * @throws ErrorException
     */
    public function testSuccess()
    {
        [$token, $user, $company] = $this->getUserToken(PermissionName::ClientCompanyUpdate);

        /** @var CompanyBalanceService $companyBalanceService */
        $companyBalanceService = app(CompanyBalanceService::class);
        $currentCompanyBalance = $companyBalanceService->getBalanceByCompanyId($company->id, Type::Balance);

        $invoiceId = $this->faker->numberBetween(1000000000, 9999999999);
        $paymentId = $this->faker->numberBetween(1000000000, 9999999999);
        $customerRealPaidAmount = 97;

        Http::fake([
            NowPaymentsApiClient::CREATE_INVOICE_URL => Http::response([
                'id' => (string) $invoiceId,
                'invoice_url' => 'https://mocked-payment-url.com',
            ]),
        ]);

        $response = $this->postRequest(
            route('client.company.deposit'),
            [
                'amount' => 100,
            ],
            $token
        );

        $response->assertStatus(200);
        $response->assertJson([
            'redirectUrl' => 'https://mocked-payment-url.com',
        ]);

        $this->assertDatabaseHas('payments', [
            'invoice_id' => (string) $invoiceId
        ]);

        $currentPayment = Payment::query()->where('invoice_id', (string) $invoiceId)->firstOrFail();

        $requestData = [
            "actually_paid" => 0,
            "actually_paid_at_fiat" => 0,
            "created_at" => "2025-03-26T14:00:52.822Z",
            "fee" => [
                "currency" => "usdttrc20",
                "depositFee" => 0,
                "serviceFee" => 0,
                "withdrawalFee" => 0,
            ],
            "invoice_id" => $invoiceId,
            "order_description" => "Balance transaction #104",
            "order_id" => (string) $currentPayment->balance_transaction_id,
            "outcome_amount" => $customerRealPaidAmount,
            "outcome_currency" => "usdttrc20",
            "parent_payment_id" => null,
            "pay_address" => "TYTzm2BGQ5pANtNvTaWnZdcqoetR9bQH1a",
            "pay_amount" => 100,
            "pay_currency" => "usdttrc20",
            "payin_extra_id" => null,
            "payment_extra_ids" => null,
            "payment_id" => $paymentId,
            "payment_status" => PaymentStatus::Finished->value,
            "price_amount" => 100,
            "price_currency" => "usd",
            "purchase_id" => "6104008796",
            "updated_at" => "2025-03-26T14:21:20.188Z",
        ];

        $response = $this->postJson(
            route('nowpayments.webhooks'),
            $requestData,
            [
                'x-nowpayments-sig' => NowPaymentsService::generateIpnToken(
                    json_encode($requestData),
                    config('services.nowpayments.ipn_key')
                )
            ]
        );

        $response->assertStatus(200);
        $response->assertJson([
            'success' => 'true',
        ]);

        $currentPayment = Payment::query()->where('payment_id', (string) $paymentId)->firstOrFail();
        $this->assertEquals(Status::Approved->value, $currentPayment->status);

        $balanceTransaction = BalanceTransaction::query()->where('id', $currentPayment->balance_transaction_id)->firstOrFail();
        $this->assertEquals(Status::Approved->value, $balanceTransaction->status);
        $this->assertEquals($customerRealPaidAmount, $balanceTransaction->amount);

        /**
         * Проверяем что баланс поменялся
         */
        $this->assertEquals($currentCompanyBalance + $customerRealPaidAmount, $companyBalanceService->getBalanceByCompanyId($company->id, Type::Balance));
    }
}
