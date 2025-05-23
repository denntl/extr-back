<?php

namespace App\Services\Common\BalanceTransaction\Handlers\Interfaces;

use App\Enums\Balance\Type;
use App\Enums\BalanceTransaction\Status;
use App\Models\BalanceTransaction;
use App\Services\Common\BalanceTransaction\Handlers\DTO\Common\ResponseHandleDTO;

interface HandlerInterface
{
    public function create(CreateTransactionDTOInterface $data, BalanceTransaction $balanceTransaction): array;

    public function handle(BalanceTransaction $balanceTransaction, array $data = []): ResponseHandleDTO;

    public function getBalanceType(CreateTransactionDTOInterface $data): Type;

    public function getAmount(CreateTransactionDTOInterface $data): float;
}
