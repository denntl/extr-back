<?php

namespace App\Services\Common\OneSignal;

use App\Models\Application;
use App\Services\Client\OnesignalTemplate\DTOs\PushRequest\SendSingleRequestDTO;
use App\Services\Common\OneSignal\DTO\ApiRequest\CreateApiKeyRequestDTO;
use App\Services\Common\OneSignal\DTO\ApiRequest\CreateUpdateApplicationRequestDTO;
use App\Services\Common\OneSignal\DTO\ApiRequest\SendRequestDTO;
use App\Services\Common\OneSignal\DTO\ApiResponse\CancelResponseDTO;
use App\Services\Common\OneSignal\DTO\ApiResponse\ClientUpdateResponseDTO;
use App\Services\Common\OneSignal\DTO\ApiResponse\CreateApiKeyResponseDTO;
use App\Services\Common\OneSignal\DTO\ApiResponse\CreateUpdateApplicationResponseDTO;
use App\Services\Common\OneSignal\DTO\ApiResponse\GetResponseDTO;
use App\Services\Common\OneSignal\DTO\ApiResponse\SendResponseDTO;
use App\Services\Common\OneSignal\Exceptions\ApplicationHasNoSubscriberException;
use App\Services\Common\OneSignal\Exceptions\ClientUpdateException;
use App\Services\Common\OneSignal\Exceptions\PushNotificationException;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OneSignalApiClient
{
    protected Client $client;
    protected string $appKey;
    protected string $appId;
    protected string $apiOrganizationKey;
    protected string $apiOrganizationId;

    private const BASE_URL = 'https://api.onesignal.com/notifications';
    private const CREATE_APPLICATION_URL = 'https://api.onesignal.com/apps';
    private const VIEW_MESSAGE_URL = 'https://api.onesignal.com/notifications';

    public function __construct(Application $application)
    {
        $this->apiOrganizationKey = config('services.onesignal.api_organization_key');
        $this->apiOrganizationId = config('services.onesignal.api_organization_id');

        $this->appKey = $application->onesignal_auth_key ?? '';
        $this->appId = $application->onesignal_id ?? '';
    }

    /**
     * @param string $url
     * @param array $headers
     * @param string $method
     * @param array $data
     * @return mixed
     * @throws PushNotificationException
     */
    private function sendRequest(string $url, array $headers, string $method, array $data = []): mixed
    {
        $requestConfig = ['headers' => $headers];

        if (!empty($data)) {
            $requestConfig['json'] = $data;
        }

        try {
            $response = Http::send($method, $url, $requestConfig)->json();

            if (!empty($response['errors'])) {
                Log::info("Onesignal sendRequest error request", $data);
                Log::info("Onesignal sendRequest error response", $response);
                throw new PushNotificationException(
                    message: "Error sendRequest method: " . implode(',', $response['errors']),
                    code: 400
                );
            }

            return $response;
        } catch (\Throwable $e) {
            throw new PushNotificationException(
                message: "OneSignalApiClient request error: {$e->getMessage()}",
                code: $e->getCode(),
                previous: $e
            );
        }
    }

    /**
     * @param SendRequestDTO|SendSingleRequestDTO $data
     * @return SendResponseDTO
     * @throws ApplicationHasNoSubscriberException
     * @throws PushNotificationException
     */
    public function sendPushNotification(SendRequestDTO|SendSingleRequestDTO $data): SendResponseDTO
    {
        try {
            return new SendResponseDTO($this->sendRequest(
                self::BASE_URL,
                [
                    'Authorization' => "Basic {$this->appKey}",
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'post',
                array_merge($data->toArray(), [
                    'app_id' => $this->appId,
                    'target_channel' => 'push',
                ])
            ));
        } catch (\Throwable $e) {
            if (str_contains($e->getMessage(), 'All included players are not subscribed')) {
                throw new ApplicationHasNoSubscriberException(
                    message: $e->getMessage(),
                    code: $e->getCode(),
                    previous: $e
                );
            }
            throw new PushNotificationException(
                message: "Error while send push notification: {$e->getMessage()}",
                code: $e->getCode(),
                previous: $e
            );
        }
    }

    /**
     * @param string $pushNotificationId
     * @return CancelResponseDTO
     * @throws PushNotificationException
     */
    public function cancelPushNotification(string $pushNotificationId): CancelResponseDTO
    {
        try {
            return new CancelResponseDTO($this->sendRequest(
                self::BASE_URL . "/$pushNotificationId?app_id={$this->appId}",
                [
                    'Authorization' => "Basic {$this->appKey}",
                    'Accept' => 'application/json',
                ],
                'delete',
            ));
        } catch (\Throwable $e) {
            throw new PushNotificationException(
                message: "Error while cancel push notification: {$e->getMessage()}",
                code: $e->getCode(),
                previous: $e
            );
        }
    }

    /**
     * @param CreateUpdateApplicationRequestDTO $data
     * @return CreateUpdateApplicationResponseDTO
     * @throws PushNotificationException
     */
    public function createApplication(CreateUpdateApplicationRequestDTO $data): CreateUpdateApplicationResponseDTO
    {
        try {
            return new CreateUpdateApplicationResponseDTO($this->sendRequest(
                self::CREATE_APPLICATION_URL,
                [
                    'Authorization' => "Basic {$this->apiOrganizationKey}",
                    'Accept' => 'application/json',
                ],
                'post',
                array_merge($data->toArray(), [
                    'organization_id' => $this->apiOrganizationId,
                    'additional_data_is_root_payload' => false,
                ])
            ));
        } catch (\Throwable $e) {
            throw new PushNotificationException(
                message: "Error while create an application: {$e->getMessage()}",
                code: $e->getCode(),
                previous: $e
            );
        }
    }

    /**
     * @param CreateApiKeyRequestDTO $body
     * @return CreateApiKeyResponseDTO
     * @throws PushNotificationException
     */
    public function createApiKey(CreateApiKeyRequestDTO $body): CreateApiKeyResponseDTO
    {
        try {
            return new CreateApiKeyResponseDTO($this->sendRequest(
                self::CREATE_APPLICATION_URL . "/{$this->appId}/auth/tokens",
                [
                    'Authorization' => "Key {$this->apiOrganizationKey}",
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'post',
                $body->toArray()
            ));
        } catch (\Throwable $e) {
            throw new PushNotificationException(
                message: "Error while create an API key: {$e->getMessage()}",
                code: $e->getCode(),
                previous: $e
            );
        }
    }

    /**
     * @param CreateUpdateApplicationRequestDTO $data
     * @return CreateUpdateApplicationResponseDTO
     * @throws PushNotificationException
     */
    public function updateApplication(CreateUpdateApplicationRequestDTO $data): CreateUpdateApplicationResponseDTO
    {
        try {
            return new CreateUpdateApplicationResponseDTO($this->sendRequest(
                self::CREATE_APPLICATION_URL . '/' . $this->appId,
                [
                    'Authorization' => "Key {$this->apiOrganizationKey}",
                    'Accept' => 'application/json',
                ],
                'put',
                array_merge($data->toArray(), [
                    'additional_data_is_root_payload' => false,
                ])
            ));
        } catch (\Throwable $e) {
            throw new PushNotificationException(
                message: "Error while update an application: {$e->getMessage()}",
                code: $e->getCode(),
                previous: $e
            );
        }
    }

    /**
     * @param string $oneSignalNotificationId
     * @return GetResponseDTO
     * @throws PushNotificationException
     */
    public function getPushNotification(string $oneSignalNotificationId): GetResponseDTO
    {
        try {
            return new GetResponseDTO($this->sendRequest(
                self::VIEW_MESSAGE_URL . "/$oneSignalNotificationId/?app_id=" . $this->appId,
                [
                    'Authorization' => "Basic {$this->appKey}",
                    'Accept' => 'application/json',
                ],
                'get'
            ));
        } catch (\Throwable $e) {
            throw new PushNotificationException(
                message: "Error while get notification info: {$e->getMessage()}",
                code: $e->getCode(),
                previous: $e
            );
        }
    }

    /**
     * @param string $externalId
     * @param string $status
     * @return ClientUpdateResponseDTO
     * @throws ClientUpdateException
     */
    public function updateClientStatus(string $externalId, string $status): ClientUpdateResponseDTO
    {
        try {
            return new ClientUpdateResponseDTO($this->sendRequest(
                self::CREATE_APPLICATION_URL . "/$this->appId/users/$externalId",
                [
                    'Authorization' => "Basic {$this->appKey}",
                    'Accept' => 'application/json',
                ],
                'PUT',
                [
                    'tags' => [
                        'status' => $status
                    ],
                ]
            ));
        } catch (\Throwable $e) {
            throw new ClientUpdateException(
                "Error while update client status: {$e->getMessage()}",
                $e->getCode(),
                $e,
                [
                    'status' => $status,
                    'externalId' => $externalId,
                ]
            );
        }
    }
}
