<?php

namespace App\Http\Controllers\Admin\Common;

use App\Enums\User\Status;
use App\Events\Client\UserWasAuthenticated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Common\TelegramAuth\TgAuthRequestRequest;
use App\Http\Requests\Admin\Common\TelegramAuth\TgRegisterRequest;
use App\Services\Common\Auth\AuthService;
use App\Services\Common\Auth\Exceptions\InvalidArgumentException;
use App\Services\Common\Auth\Exceptions\UserIsDeactivatedException;
use App\Services\Common\Auth\PermissionService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class TelegramAuthController extends Controller
{
    public function login(TgAuthRequestRequest $request, AuthService $authService): JsonResponse
    {
        try {
            $user = $authService->loginByTelegram($request->validated());
            if ($user->status === Status::Deleted->value) {
                throw new UserIsDeactivatedException();
            }
        } catch (InvalidArgumentException | ModelNotFoundException $e) {
            Log::info('Failed to login by TG', ['error' => $e->getMessage(), 'request' => $request->all()]);
            return response()->json(['errors' => [
                'common' => [
                    'Авторизация неуспешна, проверьте Telegram аккаунт или обратитесь к вашему личному менеджеру',
                ]
            ]], 422);
        }

        event(new UserWasAuthenticated($user));

        return response()->json($authService->getAuthentication($user));
    }

    public function register(string $key, TgRegisterRequest $request, AuthService $authService)
    {
        try {
            $user = $authService->registerByTelegram($key, $request->validated());
        } catch (InvalidArgumentException | ModelNotFoundException $e) {
            return response()->json(['errors' => [
                'common' => [
                    'Регистрация неуспешна, проверьте Telegram аккаунт или обратитесь к вашему личному менеджеру',
                ]
            ]], 422);
        }

        return response()->json($authService->getAuthentication($user));
    }
}
