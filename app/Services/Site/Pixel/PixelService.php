<?php

namespace App\Services\Site\Pixel;

use App\Services\Site\Pixel\DTO\PixelConversionDTO;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PixelService
{
    public function sendConversion(PixelConversionDTO $conversionDTO): bool
    {
        $url = "https://graph.facebook.com/v18.0/$conversionDTO->pixelId/events?access_token";

        $data = [
            [
                "event_name" => $conversionDTO->eventName,
                "event_time" => time(),
                "user_data" => [
                    "client_ip_address" => $conversionDTO->clientIpAddress,
                    "client_user_agent" => $conversionDTO->userAgent,
                    "fbc" => $conversionDTO->fbc,
                    "fbp" => $conversionDTO->fbp,
                    'external_id' => $conversionDTO->externalId,
                ],
                'custom_data' => [
                    'currency' => 'usd',
                    'value' => 1,
                ],
                "event_source_url" => $conversionDTO->sourceUrl,
                "action_source" => $conversionDTO->sourceAction
            ],
        ];
        Log::driver('pixel')->info('Request to FB', ['request' => $data]);

        $requestParams = [
            'data' => $data,
            'access_token' => $conversionDTO->pixelToken,
        ];
        $response = Http::post($url, $requestParams)->json();

        Log::driver('pixel')->info('Facebook response', ['response' => $response, 'request' => $data]);

        return isset($response['events_received']) && $response['events_received'] > 0;
    }
}
