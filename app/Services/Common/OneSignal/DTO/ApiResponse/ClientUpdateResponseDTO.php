<?php

namespace App\Services\Common\OneSignal\DTO\ApiResponse;

use App\Services\Common\OneSignal\DTO\Interfaces\ValidatableDTO;
use Illuminate\Support\Facades\Validator;

class ClientUpdateResponseDTO implements ValidatableDTO
{
    public function __construct(public array $response)
    {
        $this->validate();
    }

    public function validate(): void
    {
        $validator = Validator::make(['response' => $this->response], [
            'response' => 'required|array',
            'response.success' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            throw new \Exception("Filed to update client");
        }
    }
}
