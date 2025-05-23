<?php

namespace Tests\Feature\Admin\Common\DataListing\ApplicationStatistic;

use App\Enums\Authorization\PermissionName;
use App\Enums\DataListing\EntityName;
use App\Models\Application;
use App\Models\ApplicationGeoLanguage;
use App\Models\ApplicationStatistic;
use App\Services\Common\DataListing\Enums\FilterOperator;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DataListingDataTest extends TestCase
{
    use DatabaseTransactions;

    public function testSuccess()
    {
        Application::factory()->create();

        [$token] = $this->getUserToken(PermissionName::ClientApplicationStatisticRead);

        $response = $this->postRequest(
            route('common.listing.data', ['entity' => EntityName::ApplicationStatistics->value]),
            [],
            $token,
            false
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
        [$token, $user] = $this->getUserToken(PermissionName::ClientApplicationStatisticRead);

        $application = Application::factory()->create([
            'owner_id' => $user->id,
            'company_id' => $user->company_id,
        ]);

        $geos = ApplicationGeoLanguage::factory()->create([
            'application_id' => $application->id,
            'geo' => 'UA',
            'language' => 'EN',
        ]);

        /** @var ApplicationStatistic $stat */
        $stat = ApplicationStatistic::factory()->create([
            'application_id' => $application->id,
            'date' => Carbon::now(),
            'unique_clicks' => 999919,
        ]);

        ApplicationStatistic::factory()->create([
            'application_id' => $application->id,
            'date' => Carbon::yesterday(),
            'unique_clicks' => 100,
        ]);

        $response = $this->postRequest(
            route(
                'common.listing.data',
                ['entity' => EntityName::ApplicationStatistics->value],
            ),
            ['query' => "$stat->unique_clicks"],
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
            'date' => Carbon::now()->toDateString(),
            'domain' => $application->full_domain,
            'geos' => $geos->geo,
            'owner' => $user->name,
            'clicks' => $stat->clicks,
            'push_subscriptions' => $stat->push_subscriptions,
            'unique_clicks' => $stat->unique_clicks,
            'installs' => $stat->installs,
            'ins_to_uc' => $stat->ins_to_uc,
            'registrations' => $stat->registrations,
            'reg_to_ins' => (string) $stat->reg_to_ins,
            'deposits' => (int) $stat->deposits,
            'dep_to_ins' => (string) $stat->dep_to_ins,
            'dep_to_reg' => (string) $stat->dep_to_reg,
            'first_installs' => $stat->first_installs,
            'repeated_installs' => $stat->repeated_installs,
            'opens' => $stat->opens,
            'first_opens' => $stat->first_opens,
            'repeated_opens' => $stat->repeated_opens,
            'id' => $stat->id
        ]], $response->json('data'));
    }

    public function testSuccessFilter()
    {
        [$token, $user] = $this->getUserToken(PermissionName::ClientApplicationStatisticRead);

        $application = Application::factory()->create([
            'owner_id' => $user->id,
            'company_id' => $user->company_id,
        ]);

        $geos = ApplicationGeoLanguage::factory()->create([
            'application_id' => $application->id,
            'geo' => 'UA',
            'language' => 'EN',
        ]);

        /** @var ApplicationStatistic $stat */
        $stat = ApplicationStatistic::factory()->create([
            'application_id' => $application->id,
            'date' => Carbon::now(),
            'unique_clicks' => 999919,
        ]);

        ApplicationStatistic::factory()->create([
            'application_id' => $application->id,
            'date' => Carbon::now()->subDays(5),
            'unique_clicks' => 100,
        ]);

        $response = $this->postRequest(
            route(
                'common.listing.data',
                ['entity' => EntityName::ApplicationStatistics->value],
            ),
            ['filters' => [
                'date' => ['name' => 'date', 'operator' => FilterOperator::Between->value, 'value' => [
                    Carbon::now()->subDay()->toDateString(),
                    Carbon::now()->addDay()->toDateString(),
                ]],
                'domain' => [
                    'name' => 'domain',
                    'operator' => FilterOperator::Between->value,
                    'value' => substr($application->full_domain, 1, 3),
                ],
                'clicks' => [
                    'name' => 'clicks',
                    'operator' => FilterOperator::Equal->value,
                    'value' => $stat->clicks,
                ],
                'push_subscriptions' => [
                    'name' => 'push_subscriptions',
                    'operator' => FilterOperator::Equal->value,
                    'value' => $stat->push_subscriptions,
                ],
                'unique_clicks' => [
                    'name' => 'unique_clicks',
                    'operator' => FilterOperator::Equal->value,
                    'value' => $stat->unique_clicks,
                ],
                'installs' => [
                    'name' => 'installs',
                    'operator' => FilterOperator::Equal->value,
                    'value' => $stat->installs,
                ],
                'ins_to_uc' => [
                    'name' => 'ins_to_uc',
                    'operator' => FilterOperator::GreaterThanOrEqual->value,
                    'value' => $stat->ins_to_uc * 100 - 1,
                ],
                'registrations' => [
                    'name' => 'registrations',
                    'operator' => FilterOperator::Equal->value,
                    'value' => $stat->registrations,
                ],
                'reg_to_ins' => [
                    'name' => 'reg_to_ins',
                    'operator' => FilterOperator::GreaterThanOrEqual->value,
                    'value' => $stat->reg_to_ins * 100 - 1,
                ],
                'dep_to_reg' => [
                    'name' => 'dep_to_reg',
                    'operator' => FilterOperator::LessThanOrEqual->value,
                    'value' => $stat->dep_to_reg * 100 + 1,
                ],
            ]],
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
            'date' => $stat->date->toDateString(),
            'domain' => $application->full_domain,
            'geos' => $geos->geo,
            'owner' => $user->name,
            'clicks' => $stat->clicks,
            'push_subscriptions' => $stat->push_subscriptions,
            'unique_clicks' => $stat->unique_clicks,
            'installs' => $stat->installs,
            'ins_to_uc' => (string) $stat->ins_to_uc,
            'registrations' => $stat->registrations,
            'reg_to_ins' => (string) $stat->reg_to_ins,
            'deposits' => $stat->deposits,
            'dep_to_ins' => (string) $stat->dep_to_ins,
            'dep_to_reg' => (string) $stat->dep_to_reg,
            'first_installs' => $stat->first_installs,
            'repeated_installs' => $stat->repeated_installs,
            'opens' => $stat->opens,
            'first_opens' => $stat->first_opens,
            'repeated_opens' => $stat->repeated_opens,
            'id' => $stat->id
        ]], $response->json('data'));
    }
}
