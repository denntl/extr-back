<?php

namespace App\Services\Common\OneSignal;

use App\Models\PwaClient;

class OneSignalClientService
{
    public function updateStatus(PwaClient $client, string $status)
    {
        /**
         * @var OneSignalApiClient $apiClient
         */
        $apiClient = app(OneSignalApiClient::class, [
            'application' => $client->application,
        ]);

        return $apiClient->updateClientStatus($client->external_id, $status);
    }
}
