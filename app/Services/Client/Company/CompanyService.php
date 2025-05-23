<?php

namespace App\Services\Client\Company;

use App\Models\Company;

/**
 * Class CompanyService
 */
class CompanyService
{
    /**
     * @param int $companyId
     */
    public function __construct(protected int $companyId)
    {
    }

    /**
     * @param bool|null $public
     * @return Company
     */
    public function get(?bool $public = true): Company
    {
        $query = Company::query();
        if ($public) {
            $query->select(['public_id', 'name']);
        }

        return $query
            ->where('id', $this->companyId)
            ->firstOrFail();
    }

    /**
     * @param array $data
     * @return Company
     */
    public function update(array $data): Company
    {
        $company = Company::findOrFail($this->companyId);
        $company->update($data);

        return $company;
    }
}
