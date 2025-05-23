<?php

namespace App\Http\Middleware;

use App\Enums\User\Status;
use App\Models\User;
use App\Services\Common\Auth\Exceptions\UserIsDeactivatedException;
use Closure;
use Illuminate\Support\Facades\Auth;

class CheckUserStatusMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param $request
     * @param Closure $next
     * @param null $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $authGuard = Auth::guard($guard);

        /** @var User|null $user */
        $user = $authGuard->user();

        if ($user->status === Status::Deleted->value) {
            return response()->json(['message' => __('common.auth.user_is_deactivated_exception')], 401);
        }

        return $next($request);
    }
}
