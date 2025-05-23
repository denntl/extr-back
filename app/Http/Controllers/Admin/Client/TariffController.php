<?php

namespace App\Http\Controllers\Admin\Client;

use App\Http\Controllers\Controller;
use App\Services\Common\Tariff\TariffService;

class TariffController extends Controller
{
    public function show()
    {
        $company = auth()->user()->company;

        /** @var TariffService $tariffService */
        $tariffService = app(TariffService::class, ['type_id' => $company->tariff->type_id]);

        return response()->json($tariffService->getObjectForView($company->tariff));
    }
}
