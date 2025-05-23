<?php

namespace App\Services\Client\Statistic;

use App\Models\ApplicationStatistic;
use App\Services\Client\Statistic\DTO\DailyDTO;
use Carbon\Carbon;

class StatisticService
{
    public function __construct(private int $companyId)
    {
    }

    public function getDailyStatisticModel(Carbon $date): ApplicationStatistic
    {
        return ApplicationStatistic::query()
            ->select('application_statistics.*')
            ->where('date', $date->format('Y-m-d'))
            ->join('applications', 'applications.id', '=', 'application_statistics.application_id')
            ->where('applications.company_id', $this->companyId)
            ->firstOrFail();
    }

    public function getDailyStatistic(Carbon $date): DailyDTO
    {
        try {
            $applicationStatistic = $this->getDailyStatisticModel($date);
        } catch (\Throwable) {
            return new DailyDTO(uniqueClicks: 0, installs: 0, registrations: 0, deposits: 0);
        }
        return new DailyDTO(
            uniqueClicks: $applicationStatistic->unique_clicks,
            installs: $applicationStatistic->installs,
            registrations: $applicationStatistic->registrations,
            deposits: $applicationStatistic->deposits,
        );
    }
}
