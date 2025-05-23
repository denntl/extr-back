<?php

namespace Tests\Feature\Admin\Common\DataListing\CompanyBalanceTransactions;

use App\Enums\Authorization\PermissionName;
use App\Enums\DataListing\EntityName;
use App\Models\BalanceTransaction;
use App\Services\Common\DataListing\Enums\FilterOperator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DataListingDataTest extends TestCase
{
    use DatabaseTransactions;

    public function testSuccess()
    {
        [$token]  = $this->getUserToken(PermissionName::ManageCompanyBalanceTransactionsRead);

        $response = $this->postRequest(
            route('common.listing.data', ['entity' => EntityName::ManageCompanyBalanceTransactions->value]),
            [],
            $token
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'prev',
            'next',
            'page',
            'data',
            'total',
            'perPage',
            'pages',
        ]);
    }

    public function testSuccessQuery()
    {
        [$token, $user, $company]  = $this->getUserToken(PermissionName::ManageCompanyBalanceTransactionsRead);
        /** @var BalanceTransaction $balanceTransaction */
        $balanceTransaction = BalanceTransaction::factory()->create(['company_id' => $company->id]);

        /**
         * @var $target BalanceTransaction
         */
        $target = $balanceTransaction->first();

        $response = $this->postRequest(
            route('common.listing.data', ['entity' => EntityName::ManageCompanyBalanceTransactions->value]),
            ['query' => (string) $target->amount, 'companyId' => $company->id],
            $token
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'prev',
            'next',
            'page',
            'data',
            'total',
            'perPage',
            'pages',
        ]);
        $this->assertEquals(1, count($response->json('data')));

        $this->assertEquals([[
            "type" => $balanceTransaction->type,
            "created_at" => $balanceTransaction->created_at,
            "amount" => $balanceTransaction->amount,
            "balance_after" => $balanceTransaction->balance_after,
            "balance_type" => $balanceTransaction->balance_type,
            "user_id" => $balanceTransaction->user_id,
            "status" => $balanceTransaction->status,
            "comment" => null,
            "processor_id" => null,
            "application_name" => null,
        ]], $response->json('data'));
    }

    public function testSuccessFilter()
    {
        [$token, $user, $company]  = $this->getUserToken(PermissionName::ManageCompanyBalanceTransactionsRead);
        $balanceTransaction = BalanceTransaction::factory()->count(5)->create([
            'company_id' => $company->id,
        ]);

        /**
         * @var $target BalanceTransaction
         */
        $target = $balanceTransaction->first();

        $response = $this->postRequest(
            route('common.listing.data', ['entity' => EntityName::ManageCompanyBalanceTransactions->value]),
            [
                'filters' => [
                    'type' => ['name' => 'type', 'operator' => FilterOperator::Equal, 'value' => [$target->type]],
                    'created_at' => ['name' => 'created_at', 'operator' => FilterOperator::Equal, 'value' => $target->created_at],
                    'amount' => ['name' => 'amount', 'operator' => FilterOperator::Equal, 'value' => $target->amount],
                    'processor_id' => ['name' => 'processor_id', 'operator' => FilterOperator::Equal, 'value' => $target->processor_id],
                    'application_name' =>
                        ['name' => 'application_name', 'operator' => FilterOperator::Equal, 'value' => $target->application->full_domain ?? null],
                    'balance_after' => ['name' => 'balance_after', 'operator' => FilterOperator::Equal, 'value' => $target->balance_after],
                    'balance_type' => ['name' => 'balance_type', 'operator' => FilterOperator::Equal, 'value' => [$target->balance_type]],
                    'user_id' => ['name' => 'user_id', 'operator' => FilterOperator::Equal, 'value' => [$target->user_id]],
                    'status' => ['name' => 'status', 'operator' => FilterOperator::Equal, 'value' => [$target->status]],
                ],
                'companyId' => $company->id
            ],
            $token
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'prev',
            'next',
            'page',
            'data',
            'total',
            'perPage',
            'pages',
        ]);

        $this->assertDatabaseHas('balance_transactions', ['amount' => (string) $target->amount]);

        $this->assertEquals([[
            'type' => $target->type,
            'created_at' => $target->created_at,
            'amount' => $target->amount,
            'balance_after' => $target->balance_after,
            'balance_type' => $target->balance_type,
            'user_id' => $target->user_id,
            'status' => $target->status,
            'comment' => null,
            'processor_id' => null,
            'application_name' => null,
        ]], $response->json('data'));
    }
}
