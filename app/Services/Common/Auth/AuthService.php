<?php

namespace App\Services\Common\Auth;

use App\Enums\Authorization\RoleName;
use App\Enums\User\Status;
use App\Models\Company;
use App\Models\CompanyBalance;
use App\Models\Invite;
use App\Models\User;
use App\Services\Client\Team\TeamService;
use App\Services\Common\Auth\Exceptions\InvalidArgumentException;
use App\Services\Common\Auth\Exceptions\UserIsDeactivatedException;
use App\Services\Common\Company\CompanyService;
use App\Services\Common\Company\Exceptions\UserNotBelongsToCompanyException;
use App\Services\Common\Telegram\TelegramService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;

class AuthService
{
    /**
     * @throws UserNotBelongsToCompanyException
     */
    public function registerWithCompany(array $credentials): User
    {
        $company = Company::create([
            'name' => $credentials['companyName'],
        ]);

        CompanyBalance::create(
            ['company_id' => $company->id],
        );

        $userAttributes = [
            'name' => $credentials['username'],
            'email' => $credentials['email'],
            'password' => bcrypt($credentials['password']),
            'username' => $credentials['username'],
            'status' => Status::NewReg->value,
            'company_id' => $company->id,
            'is_employee' => false,
        ];

        $user = User::create($userAttributes);

        /** @var CompanyService $companyService */
        $companyService = app(CompanyService::class);
        $companyService->setCompanyOwner($company, $user);

        /** @var TeamService $teamService */
        $teamService = app(TeamService::class, ['companyId' => $company->id]);
        $teamService->create(['name' => "Команда $company->name",]);

        $user->assignRole(RoleName::CompanyLead);

        return $user;
    }

    public function getAuthentication(User $user): array
    {
        $token = $user->createToken('auth_token')->plainTextToken;
        $permissionService = app(PermissionService::class, ['user' => $user]);

        return [
            'token' => $token,
            'access' => $permissionService->getAccess()->toArray(),
        ];
    }

    /**
     * @param array $requestData
     * @return User
     * @throws InvalidArgumentException
     * @throws ModelNotFoundException
     */
    public function loginByTelegram(array $requestData): User
    {
        /** @var TelegramService $tgService */
        $tgService = app(TelegramService::class);

        if (!$tgService->isValidAuthRequest($requestData)) {
            throw new InvalidArgumentException();
        }

        $user = User::query()->where('telegram_id', $requestData['id'])->firstOrFail();
        if ($user->status === Status::Deleted->value) {
            throw new UserIsDeactivatedException();
        }

        return $user;
    }

    /**
     * @param string $key
     * @param array $requestData
     * @return User
     * @throws InvalidArgumentException
     * @throws ModelNotFoundException
     */
    public function registerByTelegram(string $key, array $requestData): User
    {
        $invite = Invite::query()->where('key', $key)->firstOrFail();

        /** @var TelegramService $tgService */
        $tgService = app(TelegramService::class);
        if (!$tgService->isValidAuthRequest($requestData)) {
            throw new InvalidArgumentException();
        }

        $lastName = empty($requestData['last_name']) ? '' : ' ' . $requestData['last_name'];
        $userAttributes = [
            'name' => $requestData['first_name'] . $lastName,
            'password' => bcrypt(Str::random(6)),
            'username' => $requestData['username'],
            'telegram_id' => $requestData['id'],
            'status' => Status::Active->value,
            'company_id' => $invite->company_id,
            'is_employee' => false,
        ];

        if (isset($invite->body['team_id'])) {
            $userAttributes['team_id'] = $invite->body['team_id'];
        }

        $user = User::create($userAttributes);

        $user->assignRole(RoleName::Buyer);

        return $user;
    }
}
