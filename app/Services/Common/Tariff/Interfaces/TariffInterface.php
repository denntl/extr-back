<?php

namespace App\Services\Common\Tariff\Interfaces;

use App\Models\Tariff;

interface TariffInterface
{
    public function getObjectForEdit(int $tariffId): array;
    public function getObjectForView(Tariff $tariff): array;

    public function getListForSelections(): array;

    public function getValidationRules(string $action): array;

    public function update(int $tariffId, array $data): void;
}
