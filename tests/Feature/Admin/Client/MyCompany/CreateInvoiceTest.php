<?php

namespace Feature\Admin\Client\MyCompany;

use App\Enums\Authorization\PermissionName;
use App\Services\Common\PaymentProcessors\NowPayments\NowPaymentsApiClient;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CreateInvoiceTest extends TestCase
{
    use WithFaker;

    public function testSuccess()
    {
        [$token] = $this->getUserToken(PermissionName::ClientCompanyUpdate);

        $invoiceId = $this->faker->uuid();

        Http::fake([
            NowPaymentsApiClient::CREATE_INVOICE_URL => Http::response([
                'id' => $invoiceId,
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
            'invoice_id' => $invoiceId
        ]);
    }

    public function testHasNoPermission()
    {
        [$token] = $this->getUserToken();

        $response = $this->postRequest(
            route('client.company.deposit'),
            [
                'amount' => 100,
            ],
            $token,
            false
        );

        $response->assertStatus(403);
    }
}
