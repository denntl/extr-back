<?php

namespace App\Services\Common\Tariff;

use App\Models\Tariff;
use App\Services\Common\Tariff\Exceptions\InvalidValidationException;
use App\Services\Common\Tariff\Interfaces\TariffInterface;
use Illuminate\Http\Request;

class TariffService
{
    public const VALIDATION_UPDATE_RULE = 'update';

    private TariffInterface $tariff;

    public function __construct(TariffInterface $tariff)
    {
        $this->tariff = $tariff;
    }

    /**
     * @param int $tariffId
     * @return array
     */
    public function getObjectForEdit(int $tariffId): array
    {
        return $this->tariff->getObjectForEdit($tariffId);
    }

    /**
     * @param Tariff $tariff
     * @return array
     */
    public function getObjectForView(Tariff $tariff): array
    {
        return $this->tariff->getObjectForView($tariff);
    }

    /**
     * @return array
     */
    public function getListForSelections(): array
    {
        return $this->tariff->getListForSelections();
    }

    /**
     * @param int $tariffId
     * @param Request $request
     * @return void
     * @throws InvalidValidationException
     */
    public function update(int $tariffId, Request $request): void
    {
        if (!$request->validate($this->tariff->getValidationRules(self::VALIDATION_UPDATE_RULE))) {
            throw new InvalidValidationException('Validation failed');
        }

        $this->tariff->update($tariffId, $request->all());
    }
}
