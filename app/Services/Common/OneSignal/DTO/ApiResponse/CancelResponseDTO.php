<?php

namespace App\Services\Common\OneSignal\DTO\ApiResponse;

use App\Services\Common\OneSignal\DTO\Interfaces\ValidatableDTO;
use App\Services\Common\OneSignal\Exceptions\InvalidPushNotificationResponseException;
use Illuminate\Support\Facades\Validator;

class CancelResponseDTO implements ValidatableDTO
{
    /**
     * @param array $response
     * @throws InvalidPushNotificationResponseException
     */
    public function __construct(public array $response)
    {
        $this->validate();
    }

    /**
     * @return void
     * @throws InvalidPushNotificationResponseException
     */
    public function validate(): void
    {
        $validator = Validator::make(['response' => $this->response], [
            'response' => 'required|array',
            'response.success' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            throw new InvalidPushNotificationResponseException($validator->errors()->toJson());
        }
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return isset($this->response['success']) && $this->response['success'] === true;
    }
}
