<?php

namespace Tests\Feature\Site\MainController;

use App\Enums\Application\LandingType;
use App\Enums\Application\PlatformType;
use App\Enums\Application\Status;
use App\Enums\BalanceTransaction\Status as BalanceTransactionStatus;
use App\Enums\Balance\Type as BalanceType;
use App\Enums\BalanceTransaction\Type;
use App\Enums\PwaEvents\Event;
use App\Models\Application;
use App\Models\ApplicationGeoLanguage;
use App\Models\Company;
use App\Models\CompanyBalance;
use App\Models\PwaClient;
use App\Models\PwaClientClick;
use App\Models\Tariff;
use Database\Factories\TariffFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AnalyticUpdateCompanyBalanceAfterFirstInstallTest extends TestCase
{
    use DatabaseTransactions;

    #[DataProvider('balanceProvider')] public static function balanceProvider(): array
    {
        return [
            'UA balance bonus success test' => ['UA', BalanceType::BalanceBonus->value, 100, 100, 7.25, 100, 92.75],
            'BE balance bonus success test' => ['BE', BalanceType::BalanceBonus->value, 300, 150, 3.50, 300, 146.50],
            'UA balance success test' => ['UA', BalanceType::Balance->value, 100, 5, 8.75, 91.25, 5],
            'BE balance success test' => ['BE', BalanceType::Balance->value, 300, 2, 4.50, 295.50, 2],
            'UA balance negative success test' => ['UA', BalanceType::Balance->value, 3, 5, 6.25, -3.25, 5],
            'BE balance negative success test' => ['BE', BalanceType::Balance->value, 1, 1, 2.75, -1.75, 1],
        ];
    }

    #[DataProvider('balanceProvider')] public function testSuccessBonusBalance(
        $country,
        $balanceType,
        $balance,
        $balanceBonus,
        $tariffInstallTierPrice,
        $expectedBalance,
        $expectedBalanceBonus
    ) {
        /** @var Company $company */
        $company = Company::factory()->create();

        CompanyBalance::query()->update([
            'company_id' => $company->id,
            'balance' => $balance,
            'balance_bonus' => $balanceBonus,
        ]);

        /** @var Tariff $tariff */
        $tariff = TariffFactory::new()->install()->create()->load('tiers.countries');

        $company->update(['tariff_id' => $tariff->id]);

        DB::table('tiers')
            ->join('tier_countries', 'tiers.id', '=', 'tier_countries.tier_id')
            ->where('tiers.tariff_id', $tariff->id)
            ->where('tier_countries.country', $country)
            ->update(['tiers.price' => $tariffInstallTierPrice]);

        /** @var Application $application */
        $application = Application::factory()->create([
            'status' => Status::Active->value,
            'platform_type' => PlatformType::Multi->value,
            'pixel_id' => 'test_pixel_id',
            'pixel_key' => 'test_pixel_key',
            'landing_type' => LandingType::Old->value,
            'company_id' => $company->id,
        ]);

        ApplicationGeoLanguage::factory()->create([
            'application_id' => $application->id,
            'geo' => $country,
            'language' => 'EN',
        ]);

        /** @var PwaClient $pwaClient */
        $pwaClient = PwaClient::factory()->create([
            'application_id' => $application->id,
        ]);

        /** @var PwaClientClick $pwaClientClick */
        $pwaClientClick = PwaClientClick::factory()->create([
            'pwa_client_id' => $pwaClient->id,
            'country' => $country,
        ]);

        $event = Event::Install->value;
        $url = route('analytic') . "?com=$application->uuid&t=$event&externalId=$pwaClientClick->external_id";
        $response = $this->get($url);
        $response->assertStatus(200);

        $this->assertDatabaseHas('balance_transactions', [
            'company_id' => $company->id,
            'amount' => -$tariffInstallTierPrice,
            'balance_type' => $balanceType,
            'type' => Type::Install->value,
            'status' => BalanceTransactionStatus::Approved->value,
            'balance_before' => $balanceType === BalanceType::BalanceBonus->value ? $balanceBonus : $balance,
            'balance_after' => $balanceType === BalanceType::BalanceBonus->value ? $expectedBalanceBonus : $expectedBalance,
        ]);

        $this->assertDatabaseHas('pwa_client_events', [
            'pwa_client_click_id' => $pwaClientClick->id,
            'event' => $event,
            'is_first' => true,
        ]);

        $this->assertDatabaseHas('company_balances', [
            'company_id' => $company->id,
            'balance' => $expectedBalance,
            'balance_bonus' => $expectedBalanceBonus,
        ]);
    }

    #[DataProvider('balanceSecondProvider')] public static function balanceSecondProvider(): array
    {
        return [
            'QQ balance bonus success test' => ['QQ', BalanceType::BalanceBonus->value, 100, 50, 2.4, 100, 47.6],
            'QQ balance success test' => ['QQ', BalanceType::Balance->value, 300, 1, 3.50, 296.5, 1],
        ];
    }

    #[DataProvider('balanceSecondProvider')] public function testSuccessUnknownCountryBonusBalance(
        $country,
        $balanceType,
        $balance,
        $balanceBonus,
        $tariffInstallTierPrice,
        $expectedBalance,
        $expectedBalanceBonus
    ) {
        /** @var Company $company */
        $company = Company::factory()->create();

        CompanyBalance::query()->update([
            'company_id' => $company->id,
            'balance' => $balance,
            'balance_bonus' => $balanceBonus,
        ]);

        /** @var Tariff $tariff */
        $tariff = TariffFactory::new()->install()->create()->load('tiers.countries');

        $company->update(['tariff_id' => $tariff->id]);

        DB::table('tiers')
            ->where('tariff_id', $tariff->id)
            ->where('name', 'Тир 4')
            ->update(['price' => $tariffInstallTierPrice]);

        /** @var Application $application */
        $application = Application::factory()->create([
            'status' => Status::Active->value,
            'platform_type' => PlatformType::Multi->value,
            'pixel_id' => 'test_pixel_id',
            'pixel_key' => 'test_pixel_key',
            'landing_type' => LandingType::Old->value,
            'company_id' => $company->id,
        ]);

        ApplicationGeoLanguage::factory()->create([
            'application_id' => $application->id,
            'geo' => $country,
            'language' => 'EN',
        ]);

        /** @var PwaClient $pwaClient */
        $pwaClient = PwaClient::factory()->create([
            'application_id' => $application->id,
        ]);

        /** @var PwaClientClick $pwaClientClick */
        $pwaClientClick = PwaClientClick::factory()->create([
            'pwa_client_id' => $pwaClient->id,
            'country' => $country,
        ]);

        $event = Event::Install->value;
        $url = route('analytic') . "?com=$application->uuid&t=$event&externalId=$pwaClientClick->external_id";
        $response = $this->get($url);
        $response->assertStatus(200);

        $this->assertDatabaseHas('balance_transactions', [
            'company_id' => $company->id,
            'amount' => -$tariffInstallTierPrice,
            'balance_type' => $balanceType,
            'type' => Type::Install->value,
            'status' => BalanceTransactionStatus::Approved->value,
            'balance_before' => $balanceType === BalanceType::BalanceBonus->value ? $balanceBonus : $balance,
            'balance_after' => $balanceType === BalanceType::BalanceBonus->value ? $expectedBalanceBonus : $expectedBalance,
        ]);

        $this->assertDatabaseHas('pwa_client_events', [
            'pwa_client_click_id' => $pwaClientClick->id,
            'event' => $event,
            'is_first' => true,
        ]);

        $this->assertDatabaseHas('company_balances', [
            'company_id' => $company->id,
            'balance' => $expectedBalance,
            'balance_bonus' => $expectedBalanceBonus,
        ]);
    }
}
