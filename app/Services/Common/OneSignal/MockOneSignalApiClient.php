<?php

namespace App\Services\Common\OneSignal;

use App\Models\Application;
use App\Services\Common\OneSignal\DTO\ApiRequest\CreateApiKeyRequestDTO;
use App\Services\Common\OneSignal\DTO\ApiRequest\CreateUpdateApplicationRequestDTO;
use App\Services\Common\OneSignal\DTO\ApiRequest\SendRequestDTO;
use App\Services\Common\OneSignal\DTO\ApiResponse\CancelResponseDTO;
use App\Services\Common\OneSignal\DTO\ApiResponse\ClientUpdateResponseDTO;
use App\Services\Common\OneSignal\DTO\ApiResponse\CreateApiKeyResponseDTO;
use App\Services\Common\OneSignal\DTO\ApiResponse\CreateUpdateApplicationResponseDTO;
use App\Services\Common\OneSignal\DTO\ApiResponse\GetResponseDTO;
use App\Services\Common\OneSignal\DTO\ApiResponse\SendResponseDTO;
use App\Services\Common\OneSignal\Exceptions\InvalidPushNotificationResponseException;
use Illuminate\Support\Str;

class MockOneSignalApiClient
{
    public function __construct(private Application $application)
    {
    }

    /**
     * @param SendRequestDTO $data
     * @return SendResponseDTO
     * @throws InvalidPushNotificationResponseException
     */
    public function sendPushNotification(SendRequestDTO $data): SendResponseDTO
    {
        return new SendResponseDTO([
            'id' => Str::uuid()->toString()
        ]);
    }

    /**
     * @param string $pushNotificationId
     * @return CancelResponseDTO
     * @throws InvalidPushNotificationResponseException
     */
    public function cancelPushNotification(string $pushNotificationId): CancelResponseDTO
    {
        return new CancelResponseDTO([
            'success' => true
        ]);
    }

    /**
     * @param CreateUpdateApplicationRequestDTO $data
     * @return CreateUpdateApplicationResponseDTO
     * @throws InvalidPushNotificationResponseException
     */
    public function updateApplication(CreateUpdateApplicationRequestDTO $data): CreateUpdateApplicationResponseDTO
    {
        return new CreateUpdateApplicationResponseDTO([
            'id' => Str::uuid()->toString(),
            'name' => $this->application->subdomain . '.' . $this->application->domain->domain,
        ]);
    }

    /**
     * @param CreateUpdateApplicationRequestDTO $data
     * @return CreateUpdateApplicationResponseDTO
     * @throws InvalidPushNotificationResponseException
     */
    public function createApplication(CreateUpdateApplicationRequestDTO $data): CreateUpdateApplicationResponseDTO
    {
        return new CreateUpdateApplicationResponseDTO([
            'id' => Str::uuid()->toString(),
            'name' => $this->application->subdomain . '.' . $this->application->domain->domain,
        ]);
    }

    public function createApiKey(CreateApiKeyRequestDTO $body, ?string $OSAppId): CreateApiKeyResponseDTO
    {
        return new CreateApiKeyResponseDTO([
            'token_id' => 'mocked-token-id',
            'formatted_token' => '12345'
        ]);
    }

    /**
     * @param string $notificationId
     * @return GetResponseDTO
     * @throws InvalidPushNotificationResponseException
     */
    public function getPushNotification(string $notificationId): GetResponseDTO
    {
        return new GetResponseDTO([
            'queued_at' => now()->timestamp,
            'completed_at' => now()->timestamp,
            'successful' => 1,
        ]);
    }

    public function updateClientStatus(string $externalId, string $status): ClientUpdateResponseDTO
    {
        return new ClientUpdateResponseDTO([
            'success' => true,
        ]);
    }
}
