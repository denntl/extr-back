<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Common\OneSignal\DTO\Webhook\WebhookDTO;
use App\Services\Common\OneSignal\OneSignalEventsService;
use App\Services\Common\OneSignal\OneSignalNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class OneSignalController extends Controller
{
    /**
     * @param $data
     * @return JsonResponse
     */
    public function webhook($data): JsonResponse
    {
        try {
            /** @var OneSignalNotificationService $notificationService */
            $notificationService = app(OneSignalNotificationService::class);
            if (
                empty($data['event']) ||
                empty($data['notificationId']) ||
                empty($notificationService->getByNotificationId($data['notificationId']))
            ) {
                Log::warning('OneSignal notification not found', $data);
                return response()->json(['success' => 'false'], 400);
            }

            /** @var OneSignalEventsService $oneSignalEventsService */
            $oneSignalEventsService = app(OneSignalEventsService::class);
            $oneSignalEventsService->create(WebhookDTO::fromArray($data));
        } catch (\Throwable $e) {
            Log::error($e);
            return response()->json(['success' => 'false'], 500);
        }

        return response()->json([
            'success' => 'true',
        ]);
    }
}
