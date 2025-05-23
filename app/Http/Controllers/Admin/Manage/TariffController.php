<?php

namespace App\Http\Controllers\Admin\Manage;

use App\Http\Controllers\Controller;
use App\Services\Common\Tariff\Exceptions\InvalidValidationException;
use App\Services\Common\Tariff\TariffService;
use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Support\Facades\Log;

class TariffController extends Controller
{
    /**
     * @param int $id
     * @return JsonResponse
     */
    public function edit(int $id): JsonResponse
    {
        /** @var TariffService $tariffService */
        $tariffService = app(TariffService::class, ['tariff_id' => $id]);

        return response()->json($tariffService->getObjectForEdit($id));
    }

    /**
     * @param int $typeId
     * @return JsonResponse
     */
    public function list(int $typeId): JsonResponse
    {
        /** @var TariffService $tariffService */
        $tariffService = app(TariffService::class, ['type_id' => $typeId]);

        return response()->json($tariffService->getListForSelections());
    }

    /**
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     */
    public function update(int $id, Request $request): JsonResponse
    {
        try {
            /** @var TariffService $tariffService */
            $tariffService = app(TariffService::class, ['tariff_id' => $id]);
            $tariffService->update($id, $request);

            return response()->json();
        } catch (InvalidValidationException $e) {
            Log::error($e->getMessage());

            return response()->json(['error' => $e->getMessage()], 422);
        } catch (\ErrorException $e) {
            Log::error($e->getMessage());

            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }
}
