<?php

namespace App\Services\Common\OneSignal\DTO\ApiResponse;

use App\Services\Common\OneSignal\DTO\Interfaces\ValidatableDTO;
use App\Services\Common\OneSignal\Exceptions\InvalidPushNotificationResponseException;
use Illuminate\Support\Facades\Validator;

class GetResponseDTO implements ValidatableDTO
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
            'response.queued_at' => 'date_format:U|nullable',
            'response.completed_at' => 'date_format:U|nullable',
            'response.successful' => 'integer|nullable',
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
        return $this->response;
    }
}
