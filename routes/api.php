<?php

use App\Enums\Authorization\PermissionName;
use App\Http\Controllers\Admin\Client\DomainController;
use App\Http\Controllers\Admin\Client\UserController as ClientUserController;
use App\Http\Controllers\Admin\Manage\PushTemplatesController;
use App\Http\Controllers\Admin\Manage\UserController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'common', 'as' => 'common.'], function () {
    Route::post('telegram/{publicId}/webhook', ['App\Http\Controllers\Admin\Common\TelegramWebhookController', 'webhook'])->name('telegram.webhook');

    Route::group(['prefix' => 'auth', 'as' => 'auth.'], function () {
        Route::post('register', ['App\Http\Controllers\Admin\Common\AuthController', 'register'])->name('register');
        Route::post('login', ['App\Http\Controllers\Admin\Common\AuthController', 'login'])->name('login');
        Route::get('{key}/invite', ['App\Http\Controllers\Admin\Common\AuthController', 'invite'])->name('invite');
        Route::group(['prefix' => 'telegram', 'as' => 'telegram.'], function () {
            Route::post('{key}/register', ['App\Http\Controllers\Admin\Common\TelegramAuthController', 'register'])->name('register');
            Route::post('login', ['App\Http\Controllers\Admin\Common\TelegramAuthController', 'login'])->name('login');
        });
    });

    Route::group(['middleware' => ['auth:sanctum', 'check-user-status']], function () {
        Route::group(['prefix' => 'auth', 'as' => 'auth.'], function () {
            Route::get('user', ['App\Http\Controllers\Admin\Common\AuthController', 'user'])->name('user');
        });

        Route::group(['prefix' => 'listing', 'as' => 'listing.'], function () {
            Route::get('{entity}/settings', ['App\Http\Controllers\Admin\Common\DataListingController', 'settings'])->name('settings');
            Route::post('{entity}/data', ['App\Http\Controllers\Admin\Common\DataListingController', 'data'])->name('data');
            Route::post('{entity}/aggregations', ['App\Http\Controllers\Admin\Common\DataListingController', 'aggregations'])->name('aggregations');
        });
        Route::group(['prefix' => 'file', 'as' => 'file.'], function () {
            Route::post('upload', ['App\Http\Controllers\Admin\Common\FileController', 'upload'])->name('upload');
        });
    });
});

Route::post('onesignal/webhook', function () {
    return app('App\Http\Controllers\Api\OneSignalController')->webhook(
        json_decode(file_get_contents("php://input"), true)
    );
})->name('onesignal.webhooks');

Route::post('nowpayments/webhook', ['App\Http\Controllers\Api\NowPaymentsController', 'webhook'])->name('nowpayments.webhooks');

Route::group(['prefix' => 'manage', 'as' => 'manage.', 'middleware' => ['auth:sanctum', 'check-user-status']], function () {
    Route::group(['prefix' => 'tariff', 'as' => 'tariff.'], function () {
        Route::get('{type}/list', ['App\Http\Controllers\Admin\Manage\TariffController', 'list'])->name('list')->middleware('permission:' . PermissionName::ManageTariffRead->value . '|' . PermissionName::ManageTariffUpdate->value);
        Route::get('{id}/edit', ['App\Http\Controllers\Admin\Manage\TariffController', 'edit'])->name('edit')->middleware('permission:' . PermissionName::ManageTariffRead->value . '|' . PermissionName::ManageTariffUpdate->value);
        Route::post('{id}/update', ['App\Http\Controllers\Admin\Manage\TariffController', 'update'])->name('update')->middleware('permission:' . PermissionName::ManageTariffUpdate->value);
    });
    Route::group(['prefix' => 'company', 'as' => 'company.'], function () {
        Route::get('{id}/edit', ['App\Http\Controllers\Admin\Manage\CompanyController', 'edit'])->name('edit')->middleware('permission:' . PermissionName::ManageCompanyUpdate->value);
        Route::post('{id}/update', ['App\Http\Controllers\Admin\Manage\CompanyController', 'update'])->name('update')->middleware('permission:' . PermissionName::ManageCompanyUpdate->value);
        Route::post('{id}/manual-balance-deposit', ['App\Http\Controllers\Admin\Manage\CompanyController', 'manualBalanceDeposit'])->name('manualBalanceDeposit')->middleware('permission:' . PermissionName::ManageCompanyManualBalanceDeposit->value);
    });
    Route::group(['prefix' => 'user', 'as' => 'user.'], function () {
        Route::get('{id}/edit', ['App\Http\Controllers\Admin\Manage\UserController', 'edit'])->name('edit')->middleware('permission:' . PermissionName::ManageUserUpdate->value);
        Route::post('{id}/update', ['App\Http\Controllers\Admin\Manage\UserController', 'update'])->name('update')->middleware('permission:' . PermissionName::ManageUserUpdate->value);
        Route::post('{id}/login', ['App\Http\Controllers\Admin\Common\AuthController', 'loginAsUser'])->name('loginAsUser')->middleware('permission:' . PermissionName::ManageUserLoginAsUser->value);
        Route::put('{id}/change-status', [UserController::class, 'changeStatus'])->name('changeStatus')->middleware('permission:' . PermissionName::ManageUserUpdate->value);
    });
    Route::group(['prefix' => 'push-template', 'as' => 'push-template.',  'middleware' => 'auth:sanctum'], function () {
        Route::get('create', [PushTemplatesController::class, 'create'])->name('create')->middleware('permission:' . PermissionName::ManagePushTemplateCreate->value);
        Route::post('store', [PushTemplatesController::class, 'store'])->name('store')->middleware('permission:' . PermissionName::ManagePushTemplateCreate->value);
        Route::get('edit/{id}', [PushTemplatesController::class, 'edit'])->name('edit')->middleware('permission:' . PermissionName::ManagePushTemplateUpdate->value);
        Route::post('update/{id}', [PushTemplatesController::class, 'update'])->name('update')->middleware('permission:' . PermissionName::ManagePushTemplateUpdate->value);
    });
    Route::group(['prefix' => 'telegram-bot', 'as' => 'telegram.'], function () {
        Route::post('{id}/change-status', ['App\Http\Controllers\Admin\Manage\TelegramBotController', 'changeStatus'])->name('changeStatus')->middleware('permission:' . PermissionName::ManageTelegramBotUpdate->value);
    });

    Route::group(['prefix' => 'notification-template', 'as' => 'notification-template.'], function () {
        Route::get('create', ['App\Http\Controllers\Admin\Manage\NotificationTemplateController', 'create'])->name('create')->middleware('permission:' . PermissionName::ManageNotificationTemplateCreate->value);
        Route::post('store', ['App\Http\Controllers\Admin\Manage\NotificationTemplateController', 'store'])->name('store')->middleware('permission:' . PermissionName::ManageNotificationTemplateCreate->value);
        Route::post('send-message', ['App\Http\Controllers\Admin\Manage\NotificationTemplateController', 'sendMessage'])->name('sendMessage')->middleware('permission:' . PermissionName::ManageNotificationTemplateSendMessage->value);
        Route::get('{id}/edit', ['App\Http\Controllers\Admin\Manage\NotificationTemplateController', 'edit'])->name('edit')->middleware('permission:' . PermissionName::ManageNotificationTemplateUpdate->value);
        Route::put('{id}/update', ['App\Http\Controllers\Admin\Manage\NotificationTemplateController', 'update'])->name('update')->middleware('permission:' . PermissionName::ManageNotificationTemplateUpdate->value);
        Route::delete('{id}/destroy', ['App\Http\Controllers\Admin\Manage\NotificationTemplateController', 'destroy'])->name('destroy')->middleware('permission:' . PermissionName::ManageNotificationTemplateDelete->value);
    });
    Route::group(['prefix' => 'roles', 'as' => 'roles.'], function () {
        Route::get('{id}/edit', [\App\Http\Controllers\Admin\Manage\RoleController::class, 'edit'])->name('edit')->middleware('permission:' . PermissionName::ManageRoleUpdate->value);
        Route::put('{id}/update', [\App\Http\Controllers\Admin\Manage\RoleController::class, 'update'])->name('update')->middleware('permission:' . PermissionName::ManageRoleUpdate->value);
        Route::get('create', [\App\Http\Controllers\Admin\Manage\RoleController::class, 'create'])->name('create')->middleware('permission:' . PermissionName::ManageRoleCreate->value);
        Route::post('store', [\App\Http\Controllers\Admin\Manage\RoleController::class, 'store'])->name('store')->middleware('permission:' . PermissionName::ManageRoleCreate->value);
        Route::get('{id}/delete', [\App\Http\Controllers\Admin\Manage\RoleController::class, 'delete'])->name('delete')->middleware('permission:' . PermissionName::ManageRoleDelete->value);
        Route::delete('{id}/destroy', [\App\Http\Controllers\Admin\Manage\RoleController::class, 'destroy'])->name('destroy')->middleware('permission:' . PermissionName::ManageRoleDelete->value);
    });
    Route::group(['prefix' => 'domain', 'as' => 'domain.'], function () {
        Route::post('store', [DomainController::class, 'store'])->name('store')->middleware('permission:' . PermissionName::ManageDomainCreate->value);
        Route::put('{id}/change-status', [DomainController::class, 'changeStatus'])->name('changeStatus')->middleware('permission:' . PermissionName::ManageDomainUpdate->value);
    });
    Route::group(['prefix' => 'application', 'as' => 'application.'], function () {
        Route::get('{id}/edit', [\App\Http\Controllers\Admin\Manage\ApplicationController::class, 'edit'])->name('edit')->middleware('permission:' . PermissionName::ManageApplicationSave->value);
        Route::post('update', [\App\Http\Controllers\Admin\Manage\ApplicationController::class, 'save'])->name('update')->middleware('permission:' . PermissionName::ManageApplicationSave->value);
        Route::post('{id}/clone', [\App\Http\Controllers\Admin\Manage\ApplicationController::class, 'clone'])->name('clone')->where('id', '[0-9]+')->middleware('permission:' . PermissionName::ManageApplicationClone->value);
        Route::post('{id}/deactivate', [\App\Http\Controllers\Admin\Manage\ApplicationController::class, 'deactivate'])->name('deactivate')->middleware('permission:' . PermissionName::ManageApplicationSave->value);
        Route::post('{id}/delete', [\App\Http\Controllers\Admin\Manage\ApplicationController::class, 'delete'])->name('delete')->middleware('permission:' . PermissionName::ManageApplicationDelete->value);
        Route::post('{id}/restore', [\App\Http\Controllers\Admin\Manage\ApplicationController::class, 'restore'])->name('restore')->middleware('permission:' . PermissionName::ManageApplicationDelete->value);
        Route::group(['prefix' => 'comment', 'as' => 'comment.', 'middleware' => 'permission:' . PermissionName::ManageApplicationSave->value], function () {
            Route::get('search', [\App\Http\Controllers\Admin\Manage\ApplicationCommentController::class, 'search'])->name('search');
            Route::post('clone', [\App\Http\Controllers\Admin\Manage\ApplicationCommentController::class, 'clone'])->name('clone');
            Route::delete('{id}/destroy', [\App\Http\Controllers\Admin\Manage\ApplicationCommentController::class, 'destroy'])->name('destroy')->where('id', '[0-9]+');
            Route::get('{id}/edit', [\App\Http\Controllers\Admin\Manage\ApplicationCommentController::class, 'edit'])->name('edit');
            Route::put('{id}/update', [\App\Http\Controllers\Admin\Manage\ApplicationCommentController::class, 'update'])->name('update')->where('id', '[0-9]+');
            Route::post('{id}/store', [\App\Http\Controllers\Admin\Manage\ApplicationCommentController::class, 'store'])->name('store')->where('id', '[0-9]+');
        });
    });
});

Route::group(['prefix' => 'client', 'as' => 'client.', 'middleware' => ['auth:sanctum', 'check-user-status']], function () {
    Route::group(['prefix' => 'listing', 'as' => 'listing.'], function () {
        Route::get('{entity}/settings', ['App\Http\Controllers\Admin\Common\DataListingController', 'settings'])->name('settings');
        Route::post('{entity}/data', ['App\Http\Controllers\Admin\Common\DataListingController', 'data'])->name('data');
    });
    Route::group(['prefix' => 'tariff', 'as' => 'tariff.'], function () {
        Route::get('show', ['App\Http\Controllers\Admin\Client\TariffController', 'show'])->name('view')->middleware('permission:' . PermissionName::ClientTariffRead->value);
    });

    Route::group(['prefix' => 'user', 'as' => 'user.'], function () {
        Route::get('{id}/change-status', [ClientUserController::class, 'canChangeStatus'])->name('canChangeStatus')->middleware('permission:' . PermissionName::ClientUserDeactivate->value);
        Route::put('{id}/change-status', [ClientUserController::class, 'changeStatus'])->name('changeStatus')->middleware('permission:' . PermissionName::ClientUserDeactivate->value);
    });

    Route::group(['prefix' => 'company', 'as' => 'company.'], function () {
        Route::get('invite', ['App\Http\Controllers\Admin\Client\CompanyController', 'getInvite'])->name('invite')->middleware('permission:' . PermissionName::ClientCompanyInvite->value);
        Route::get('edit', ['App\Http\Controllers\Admin\Client\CompanyController', 'edit'])->name('edit')->middleware('permission:' . PermissionName::ClientCompanyRead->value . '|' . PermissionName::ClientCompanyUpdate->value);
        Route::post('update', ['App\Http\Controllers\Admin\Client\CompanyController', 'update'])->name('update')->middleware('permission:' . PermissionName::ClientCompanyRead->value . '|' . PermissionName::ClientCompanyUpdate->value);
        Route::post('deposit', ['App\Http\Controllers\Admin\Client\CompanyController', 'deposit'])->name('deposit')->middleware('permission:' . PermissionName::ClientCompanyBalanceTransactionsRead->value);
    });

    Route::group(['prefix' => 'team', 'as' => 'team.'], function () {
        Route::get('{id}/invite', ['App\Http\Controllers\Admin\Client\TeamController', 'getInvite'])->name('invite')->middleware('permission:' . PermissionName::ClientTeamInvite->value);
        Route::post('{id}/edit', ['App\Http\Controllers\Admin\Client\TeamController', 'edit'])->name('edit')->middleware('permission:' . PermissionName::ClientTeamUpdate->value);
        Route::post('{id}/update', ['App\Http\Controllers\Admin\Client\TeamController', 'update'])->name('update')->middleware('permission:' . PermissionName::ClientTeamUpdate->value);
        Route::post('create', ['App\Http\Controllers\Admin\Client\TeamController', 'create'])->name('create')->middleware('permission:' . PermissionName::ClientTeamCreate->value);
        Route::post('store', ['App\Http\Controllers\Admin\Client\TeamController', 'store'])->name('store')->middleware('permission:' . PermissionName::ClientTeamCreate->value);
        Route::delete('{id}/destroy', ['App\Http\Controllers\Admin\Client\TeamController', 'destroy'])->name('destroy')->middleware('permission:' . PermissionName::ClientTeamDelete->value);
    });

    Route::group(['prefix' => 'push-notification', 'as' => 'push-notification.',  'middleware' => 'auth:sanctum'], function () {
        Route::get('create', [\App\Http\Controllers\Admin\Client\PushNotificationsController::class, 'create'])->name('create')->middleware('permission:' . PermissionName::ClientPushNotificationCreate->value);
        Route::get('{id}/template-info', [\App\Http\Controllers\Admin\Client\PushNotificationsController::class, 'templateInfo'])->name('template-info')
            ->middleware('permission:' . PermissionName::ClientPushNotificationCreate->value . '|' . PermissionName::ClientPushNotificationUpdate->value);
        Route::post('store', [\App\Http\Controllers\Admin\Client\PushNotificationsController::class, 'store'])->name('store')->middleware('permission:' . PermissionName::ClientPushNotificationCreate->value);
        Route::get('{id}/edit', [\App\Http\Controllers\Admin\Client\PushNotificationsController::class, 'edit'])->name('edit')
            ->middleware('permission:' . PermissionName::ClientPushNotificationUpdate->value . '|' .  PermissionName::ClientPushNotificationRead->value);
        Route::post('{id}/update', [\App\Http\Controllers\Admin\Client\PushNotificationsController::class, 'update'])->name('update')->middleware('permission:' . PermissionName::ClientPushNotificationUpdate->value);
        Route::post('{id}/delete', [\App\Http\Controllers\Admin\Client\PushNotificationsController::class, 'delete'])->name('delete')->middleware('permission:' . PermissionName::ClientPushNotificationDelete->value);
        Route::post('{id}/copy', [\App\Http\Controllers\Admin\Client\PushNotificationsController::class, 'copy'])->name('copy')->middleware('permission:' . PermissionName::ClientPushNotificationClone->value);
    });

    Route::group(['prefix' => 'onesignal-template', 'as' => 'onesignal-template.'], function () {
        Route::get('create', [\App\Http\Controllers\Admin\Client\OnesignalTemplatesController::class, 'create'])->name('create')->middleware('permission:' . PermissionName::ClientPushNotificationCreate->value);
        Route::post('store', [\App\Http\Controllers\Admin\Client\OnesignalTemplatesController::class, 'store'])->name('store')->middleware('permission:' . PermissionName::ClientPushNotificationCreate->value);
        Route::get('{id}/edit', [\App\Http\Controllers\Admin\Client\OnesignalTemplatesController::class, 'edit'])->name('edit')->middleware('permission:' . PermissionName::ClientPushNotificationUpdate->value);
        Route::post('{id}/update', [\App\Http\Controllers\Admin\Client\OnesignalTemplatesController::class, 'update'])->name('update')->middleware('permission:' . PermissionName::ClientPushNotificationUpdate->value);
        Route::post('{id}/delete', [\App\Http\Controllers\Admin\Client\OnesignalTemplatesController::class, 'delete'])->name('delete')->middleware('permission:' . PermissionName::ClientPushNotificationDelete->value);
    });

    Route::group(['prefix' => 'notifications', 'as' => 'notifications.'], function () {
        Route::group(['prefix' => 'telegram-bot', 'as' => 'telegram-bot.'], function () {
            Route::get('edit', ['App\Http\Controllers\Admin\Client\TelegramBotController', 'edit'])->name('edit')->middleware('permission:' . PermissionName::ClientNotificationTelegramBotUpdate->value);
            Route::put('update', ['App\Http\Controllers\Admin\Client\TelegramBotController', 'update'])->name('update')->middleware('permission:' . PermissionName::ClientNotificationTelegramBotUpdate->value);
            Route::post('change-status', ['App\Http\Controllers\Admin\Client\TelegramBotController', 'changeStatus'])->name('changeStatus')->middleware('permission:' . PermissionName::ClientNotificationTelegramBotUpdate->value);
            Route::get('get-active-bot', ['App\Http\Controllers\Admin\Client\TelegramBotController', 'getActive'])->name('getActive');
            Route::get('get-invite-link', ['App\Http\Controllers\Admin\Client\TelegramBotController', 'getInviteLink'])->name('getInviteLink');
        });
        Route::put('{id}/activate', ['App\Http\Controllers\Admin\Client\NotificationController', 'activate'])->name('activate')->middleware('permission:' . PermissionName::ClientNotificationActivate->value);
    });

    Route::group(['prefix' => 'application', 'as' => 'application.'], function () {
        Route::get('create', ['App\Http\Controllers\Admin\Client\ApplicationController', 'create'])->name('create')->middleware('permission:' . PermissionName::ClientApplicationSave->value);
        Route::post('{id}/clone', ['App\Http\Controllers\Admin\Client\ApplicationController', 'clone'])->name('clone')->middleware('permission:' . PermissionName::ClientApplicationClone->value)->where('id', '[0-9]+');
        Route::post('store', ['App\Http\Controllers\Admin\Client\ApplicationController', 'save'])->name('store')->middleware('permission:' . PermissionName::ClientApplicationSave->value);
        Route::post('update', ['App\Http\Controllers\Admin\Client\ApplicationController', 'save'])->name('update')->middleware('permission:' . PermissionName::ClientApplicationSave->value);
        Route::get('{id}/edit', ['App\Http\Controllers\Admin\Client\ApplicationController', 'edit'])->name('edit')->middleware('permission:' . PermissionName::ClientApplicationSave->value);
        Route::post('{id}/deactivate', ['App\Http\Controllers\Admin\Client\ApplicationController', 'deactivate'])->name('deactivate')->middleware('permission:' . PermissionName::ClientApplicationSave->value);
        Route::post('{id}/delete', ['App\Http\Controllers\Admin\Client\ApplicationController', 'delete'])->name('delete')->middleware('permission:' . PermissionName::ClientApplicationDelete->value);

        Route::group(['prefix' => 'comment', 'as' => 'comment.', 'middleware' => 'permission:' . PermissionName::ClientApplicationSave->value], function () {
            Route::get('search', ['App\Http\Controllers\Admin\Client\ApplicationCommentController', 'search'])->name('search');
            Route::post('clone', ['App\Http\Controllers\Admin\Client\ApplicationCommentController', 'clone'])->name('clone');
            Route::delete('{id}/destroy', ['App\Http\Controllers\Admin\Client\ApplicationCommentController', 'destroy'])->name('destroy')->where('id', '[0-9]+');
            Route::get('{id}/edit', ['App\Http\Controllers\Admin\Client\ApplicationCommentController', 'edit'])->name('edit');
            Route::put('{id}/update', ['App\Http\Controllers\Admin\Client\ApplicationCommentController', 'update'])->name('update')->where('id', '[0-9]+');
            Route::post('{id}/store', ['App\Http\Controllers\Admin\Client\ApplicationCommentController', 'store'])->name('store')->where('id', '[0-9]+');
        });
    });

    Route::group(['prefix' => 'statistic', 'as' => 'statistic.'], function () {
        Route::get('get-daily', ['App\Http\Controllers\Admin\Client\StatisticController', 'getDailyStatistic'])->name('get-daily')->middleware('permission:' . PermissionName::ClientApplicationStatisticRead->value);
    });
});
