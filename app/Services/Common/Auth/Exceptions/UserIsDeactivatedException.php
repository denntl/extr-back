<?php

namespace App\Services\Common\Auth\Exceptions;

use App\Exceptions\ValidationException;
use Throwable;

class UserIsDeactivatedException extends ValidationException
{
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        $message = $message ?: __('common.auth.user_is_deactivated_exception');
        parent::__construct($message, $code, $previous);
    }
}
