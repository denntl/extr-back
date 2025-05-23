<?php

namespace App\Services\Common\BalanceTransaction\Handlers\DTO\Common;

abstract class BaseDTO
{
    public function getUserId(): ?int
    {
        return 0;
    }

    abstract public static function fromArray(array $data): self;
}
