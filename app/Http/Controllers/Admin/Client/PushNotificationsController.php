<?php

namespace App\Http\Controllers\Admin\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Manage\PushNotification\StoreRequest;
use App\Services\Client\Application\ApplicationService;
use App\Services\Client\PushNotification\PushNotificationService;
use App\Services\Manage\Application\ApplicationService as MApplicationService;
use App\Services\Manage\PushTemplate\PushTemplateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class PushNotificationsController extends Controller
{
    public function create(ApplicationService $applicationService): JsonResponse
    {
        return response()->json([
            'events' => PushTemplateService::getEventsList(),
            'geos' => ApplicationService::getGeoList(),
            'applications' => $applicationService->getListForSelections(auth()->id()),
        ]);
    }

    public function templateInfo(int $id, PushTemplateService $templateService): JsonResponse
    {
        $template = $templateService->getById($id);
        return response()->json(['template' => $template->toArray()]);
    }

    public function store(StoreRequest $request, PushNotificationService $notificationService): JsonResponse
    {
        try {
            $notification = $notificationService->create($request->validated(), auth()->id());
        } catch (\Throwable $e) {
            Log::error($e);

            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json([
            'id' => $notification->id,
        ]);
    }

    public function edit(
        int $id,
        PushTemplateService $templateService,
        MApplicationService $applicationService,
        PushNotificationService $notificationService,
    ): JsonResponse {
        return response()->json([
            'values' => $notificationService->getById($id)->toArray(),
            'events' => PushTemplateService::getEventsList(),
            'geos' => ApplicationService::getGeoList(),
            'templates' => $templateService->getListForSelections(),
            'applications' => $applicationService->getListForSelections(),
        ]);
    }

    public function update(int $id, StoreRequest $request, PushNotificationService $notificationService): JsonResponse
    {
        try {
            $notification = $notificationService->update($id, $request->validated());
        } catch (\Throwable $e) {
            Log::error($e);

            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json([
            'id' => $notification->id,
        ]);
    }

    /**
     * @param int $id
     * @param PushNotificationService $notificationService
     * @return JsonResponse
     */
    public function delete(int $id, PushNotificationService $notificationService): JsonResponse
    {
        try {
            $notificationService->delete($id);
        } catch (\Throwable $e) {
            Log::error($e);

            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json();
    }

    public function copy(int $id, PushNotificationService $notificationService): JsonResponse
    {
        $notification = $notificationService->copy($id, auth()->id());
        return response()->json([
            'id' => $notification->id,
        ]);
    }
}
