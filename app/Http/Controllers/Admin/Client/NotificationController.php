<?php

namespace App\Http\Controllers\Admin\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Client\Notification\ActivateRequest;
use App\Models\NotificationTemplate;
use App\Services\Client\Notification\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function activate(int $id, ActivateRequest $request, NotificationService $notificationService): JsonResponse
    {
        // fixme need to check via policy
//        $notificationTemplate = NotificationTemplate::query()
//            ->where('enable_for_client', true)
//            ->where('is_active', true)
//            ->where('id', $id)
//            ->first();
        $notificationService->activate($id, $request->validated('isActive'));
        return response()->json(['message' => 'success']);
    }
}
