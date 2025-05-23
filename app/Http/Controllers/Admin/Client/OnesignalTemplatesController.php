<?php

namespace App\Http\Controllers\Admin\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Client\Onesignal\StoreRequest;
use App\Services\Client\Application\ApplicationService;
use App\Services\Client\OnesignalTemplate\DTOs\RequestDTO;
use App\Services\Client\OnesignalTemplate\Exceptions\OnesignalTemplateException;
use App\Services\Client\OnesignalTemplate\OnesignalTemplateService;
use App\Services\Common\Geo\GeoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class OnesignalTemplatesController extends Controller
{
    public function create(ApplicationService $applicationService): JsonResponse
    {
        return response()->json([
            'events' => OnesignalTemplateService::getEventsList(),
            'segments' => OnesignalTemplateService::getSegmentsList(),
            'geos' => GeoService::getListForSelect(),
            'applications' => $applicationService->getApplicationsForSelection(auth()->id()),
        ]);
    }

    public function store(StoreRequest $request, OnesignalTemplateService $onesignalTemplateService): JsonResponse
    {
        try {
            $data = new RequestDTO($request->validated(), auth()->id());
            $notification = $onesignalTemplateService->store($data);
        } catch (\Throwable $e) {
            Log::error($e);
            return response()->json(['error' => 'Something went wrong on server side'], 500);
        }

        return response()->json([
            'id' => $notification->id,
        ]);
    }

    public function edit(int $id, ApplicationService $applicationService, OnesignalTemplateService $deliveryService): JsonResponse
    {
        $contents = $deliveryService->getContentsByTemplateId($id);
        return response()->json([
            'values' => $deliveryService->getTemplateById($id),
            'contents' => array_combine(array_column($contents->toArray(), 'code'), $contents->toArray()),
            'events' => OnesignalTemplateService::getEventsList(),
            'segments' => OnesignalTemplateService::getSegmentsList(),
            'geos' => GeoService::getListForSelect(),
            'applications' => $applicationService->getApplicationsForSelection(auth()->id()),
        ]);
    }

    public function update(int $id, StoreRequest $request, OnesignalTemplateService $onesignalTemplateService): JsonResponse
    {
        try {
            $data = new RequestDTO($request->validated(), onesignal_template_id: $id);
            $notification = $onesignalTemplateService->update($data);
        } catch (OnesignalTemplateException $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        } catch (\Throwable $e) {
            Log::error($e);
            return response()->json(['error' => 'Something went wrong on server side'], 500);
        }

        return response()->json([
            'id' => $notification,
        ]);
    }
    public function delete(int $id, OnesignalTemplateService $onesignalTemplateService): JsonResponse
    {
        try {
            $onesignalTemplateService->delete($id);
        } catch (OnesignalTemplateException $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        } catch (\Throwable $e) {
            Log::error($e);
            return response()->json(['error' => 'Something went wrong on server side'], 500);
        }
        return response()->json();
    }
}
