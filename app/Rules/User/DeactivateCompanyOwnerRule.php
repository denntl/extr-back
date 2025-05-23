<?php

namespace App\Rules\User;

use App\Services\Client\User\UserService;
use App\Services\Manage\Company\CompanyService;
use Illuminate\Contracts\Validation\ValidationRule;

class DeactivateCompanyOwnerRule implements ValidationRule
{
    public function __construct(
        protected int $userId,
        protected bool $status,
        protected string $companyId,
    ) {
    }

    public function validate($attribute, $value, $fail): void
    {
        $company = app(CompanyService::class)->getCompanyByPublicId($this->companyId);
        $userService = app(UserService::class, ['companyId' => $company->id]);
        if (
            $userService->isCompanyOwner($this->userId)
            && $this->status === false
        ) {
            $fail(__('user-actions.cannot-deactivate-company-owner'),);
        }
    }
}
