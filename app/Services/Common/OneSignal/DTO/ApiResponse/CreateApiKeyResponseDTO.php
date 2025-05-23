<?php

namespace App\Services\Common\OneSignal\DTO\ApiResponse;

use App\Services\Common\OneSignal\DTO\Interfaces\ValidatableDTO;
use App\Services\Common\OneSignal\Exceptions\InvalidPushNotificationResponseException;
use Illuminate\Support\Facades\Validator;

class CreateApiKeyResponseDTO implements ValidatableDTO
{
    public string $token_id;
    public string $formatted_token;
    /**
     * @param array $response
     * @throws InvalidPushNotificationResponseException
     */
    public function __construct(public array $response)
    {
        $this->validate();
        $this->token_id = $this->response['token_id'];
        $this->formatted_token = $this->response['formatted_token'];
    }

    /**
     * @return void
     * @throws InvalidPushNotificationResponseException
     */
    public function validate(): void
    {
        $validator = Validator::make(['response' => $this->response], [
            'response' => 'required|array',
            'response.token_id' => 'required|string',
            'response.formatted_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new InvalidPushNotificationResponseException($validator->errors()->toJson());
        }
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'token_id' => $this->response['token_id'],
            'formatted_token' => $this->response['formatted_token'],
        ];
    }
}
