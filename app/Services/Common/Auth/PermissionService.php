<?php

namespace App\Services\Common\Auth;

use App\Enums\Authorization\PermissionName;
use App\Enums\Authorization\RoleName;
use App\Models\User;
use App\Services\Common\Auth\DTO\AccessDTO;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;
use stdClass;

readonly class PermissionService
{
    public static function predefinedRoles(): array
    {
        return [
            RoleName::CompanyLead->value,
            RoleName::TeamLead->value,
            RoleName::Buyer->value,
        ];
    }

    public static function getPermissions(User $user): array
    {
        return $user->getAllPermissions()->map(fn (Permission $permission) => $permission->name)->toArray();
    }

    /**
     * @param User|Authenticatable $user
     */
    public function __construct(public User|Authenticatable $user)
    {
    }

    public function getAccess(): AccessDTO
    {
        return new AccessDTO($this->user->isAdmin(), self::getPermissions($this->user));
    }

    public function getPermissionsForSelections(): Collection
    {
        return Permission::query()
            ->get()
            ->map(fn (Permission $item) => ['value' => $item->id, 'label' => __("permissions.$item->name.name")]);
    }
}
