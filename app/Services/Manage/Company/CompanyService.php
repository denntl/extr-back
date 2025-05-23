<?php

namespace App\Services\Manage\Company;

use App\Enums\Authorization\RoleName;
use App\Models\Company;
use App\Services\Manage\User\UserService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

class CompanyService
{
    public function getCompanyForSelections(): Collection
    {
        return Company::query()
            ->selectRaw('id as value, name as label')
            ->get();
    }

    /**
     * @param int $id
     * @return Company
     */
    public function getCompanyById(int $id): Company
    {
        return Company::findOrFail($id);
    }

    /**
     * @param string $publicId
     * @return Company
     */
    public function getCompanyByPublicId(string $publicId): Company
    {
        return Company::query()->where('public_id', $publicId)->firstOrFail();
    }

    /**
     * @param int $companyId
     * @return Collection
     */
    public function getListOfCompanyUsers(int $companyId): Collection
    {
        return Company::query()->findOrFail($companyId)->users()->selectRaw('id as value, username as label')->get();
    }

    /**
     * @param int $id
     * @param array $data
     * @return Company
     * @throws Throwable
     */
    public function update(int $id, array $data): Company
    {
        DB::beginTransaction();
        try {
            $company = Company::findOrFail($id);
            $currentOwnerId = $company->owner_id;
            $company->update($data);

            if ($currentOwnerId !== $data['owner_id']) {
                /** @var UserService $userService */
                $userService = app(UserService::class);

                $currentOwner = $userService->getById($currentOwnerId, false);
                $newOwner = $userService->getById($data['owner_id'], false);

                $currentOwner->removeRole(RoleName::CompanyLead);
                $newOwner->assignRole(RoleName::CompanyLead);
            }
            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }

        return $company;
    }
}
