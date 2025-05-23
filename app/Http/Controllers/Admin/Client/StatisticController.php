<?php

namespace App\Http\Controllers\Admin\Client;

use App\Http\Controllers\Controller;
use App\Services\Client\Statistic\StatisticService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StatisticController extends Controller
{
    public function getDailyStatistic(StatisticService $statisticService): JsonResponse
    {
        return response()->json($statisticService->getDailyStatistic(Carbon::now())->toArray());
    }
}
