<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\NowPayments\WebhookRequest;
use App\Services\Common\BalanceTransaction\BalanceTransactionService;
use App\Services\Common\BalanceTransaction\Handlers\NowPaymentsHandler;
use App\Services\Common\Payment\PaymentService;
use App\Services\Common\PaymentProcessors\NowPayments\NowPaymentsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class NowPaymentsController extends Controller
{
    /**+
     * @param WebhookRequest $request
     * @return JsonResponse
     */
    public function webhook(WebhookRequest $request): JsonResponse
    {
        try {
            NowPaymentsService::checkIpnRequestIsValid(
                config('services.nowpayments.ipn_key'),
                $request->header('x-nowpayments-sig')
            );
            Log::channel('payments')->info('Webhook', [
                'request' => $request->all(),
                'headers' => $request->headers->all()
            ]);
            NowPaymentsService::webhookHandle($request);
        } catch (\Throwable $e) {
            Log::error($e);
            return response()->json(['success' => 'false'], 500);
        }

        return response()->json([
            'success' => 'true',
        ]);
    }
}
