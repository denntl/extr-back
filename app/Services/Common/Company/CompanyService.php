<?php

namespace App\Services\Common\Company;

use App\Models\Company;
use App\Models\Tariff;
use App\Models\User;
use App\Services\Common\Company\Exceptions\UserNotBelongsToCompanyException;

/**
 * Class CompanyService
 * @package App\Services\Common\Client

 */
class CompanyService
{
    /**
     * @param Company $company
     * @param User $user
     * @return bool
     * @throws UserNotBelongsToCompanyException
     */
    public function setCompanyOwner(Company $company, User $user): bool
    {
        if ($company->id !== $user->company_id) {
            throw new UserNotBelongsToCompanyException($user->id, $company->id);
        }

        $company->owner_id = $user->id;

        return $company->save();
    }

    /**
     * @param int $companyId
     * @return Tariff|null
     */
    public function getTariffByCompanyId(int $companyId): ?Tariff
    {
        return Company::query()
            ->where('companies.id', $companyId)
            ->first()->tariff;
    }
}
