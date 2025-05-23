<?php

namespace App\Http\Middleware;

use App\Enums\Authorization\RoleName;
use App\Enums\User\Status;
use App\Models\User;
use App\Services\Common\Auth\Exceptions\UserIsDeactivatedException;
use Closure;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Spatie\Permission\Guard;
use Spatie\Permission\Middleware\PermissionMiddleware as Middleware;

class PermissionMiddleware extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, $permission, $guard = null)
    {
        $authGuard = Auth::guard($guard);

        /** @var User|null $user */
        $user = $authGuard->user();

        // For machine-to-machine Passport clients
        if (!$user && $request->bearerToken() && config('permission.use_passport_client_credentials')) {
            $user = Guard::getPassportClient($guard);
        }

        if (!$user) {
            throw UnauthorizedException::notLoggedIn();
        }

        if (!method_exists($user, 'hasAnyPermission')) {
            throw UnauthorizedException::missingTraitHasRoles($user);
        }

        $permissions = is_array($permission)
            ? $permission
            : explode('|', $permission);

        if (!$user->hasRole(RoleName::Admin->value) && !$user->canAny($permissions)) {
            throw UnauthorizedException::forPermissions($permissions);
        }

        return $next($request);
    }
}
