<?php

namespace App\Services\Common\OneSignal\DTO\ApiResponse;

use App\Services\Common\OneSignal\DTO\Interfaces\ValidatableDTO;
use App\Services\Common\OneSignal\Exceptions\InvalidPushNotificationResponseException;
use Illuminate\Support\Facades\Validator;

class SendResponseDTO implements ValidatableDTO
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
            'response.id' => 'required|string'
        ]);

        if ($validator->fails()) {
            throw new InvalidPushNotificationResponseException($validator->errors()->toJson());
        }
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->response['id'];
    }
}
