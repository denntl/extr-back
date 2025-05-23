<?php

namespace App\Services\Common\Company\Exceptions;

class UserNotBelongsToCompanyException extends \Exception
{
    public function __construct(int $userId, int $companyId)
    {
        parent::__construct("User $userId does not belong to this company $companyId");
    }
}
