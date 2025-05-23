<?php

namespace App\Services\Common\BalanceTransaction\Exceptions;

use Throwable;

class InvalidTransactionTypeException extends \Exception
{
    private array $context;

    public function __construct(string $message = "", int $code = 0, Throwable $previous = null, array $context = [])
    {
        $this->context = $context;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return array
     */
    public function getContext(): array
    {
        return $this->context;
    }
}
