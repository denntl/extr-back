<?php

namespace App\Services\Common\OneSignal\DTO\ApiResponse;

use App\Services\Common\OneSignal\DTO\Interfaces\ValidatableDTO;
use App\Services\Common\OneSignal\Exceptions\InvalidPushNotificationResponseException;
use Illuminate\Support\Facades\Validator;

class CreateUpdateApplicationResponseDTO implements ValidatableDTO
{
    public string $id;
    public string $name;
    /**
     * @param array $response
     * @throws InvalidPushNotificationResponseException
     */
    public function __construct(public array $response)
    {
        $this->validate();
        $this->id = $this->response['id'];
        $this->name = $this->response['name'];
    }

    /**
     * @return void
     * @throws InvalidPushNotificationResponseException
     */
    public function validate(): void
    {
        $validator = Validator::make(['response' => $this->response], [
            'response' => 'required|array',
            'response.id' => 'required|string',
            'response.name' => 'required|string',
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
            'id' => $this->response['id'],
            'name' => $this->response['name'],
        ];
    }
}
