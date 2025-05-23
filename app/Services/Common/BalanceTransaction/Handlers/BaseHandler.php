<?php

namespace App\Services\Common\BalanceTransaction\Handlers;

use App\Services\Common\BalanceTransaction\Handlers\Interfaces\CreateTransactionDTOInterface;
use InvalidArgumentException;

abstract class BaseHandler
{
    /**
     * @param CreateTransactionDTOInterface $data
     * @param string $expectedClass
     * @return void
     * @throws InvalidArgumentException
     */
    protected function validateDTO(CreateTransactionDTOInterface $data, string $expectedClass): void
    {
        if (!$data instanceof $expectedClass) {
            throw new InvalidArgumentException("Invalid DTO type. Expected: $expectedClass, given: " . get_class($data));
        }
    }
}
