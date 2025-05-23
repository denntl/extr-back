<?php

namespace App\Http\Controllers\Admin\Manage;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Manage\NotificationTemplate\SendMessageRequest;
use App\Http\Requests\Admin\Manage\NotificationTemplate\StoreRequest;
use App\Http\Requests\Admin\Manage\NotificationTemplate\UpdateRequest;
use App\Services\Common\Role\RoleService;
use App\Services\Manage\Company\CompanyService;
use App\Services\Manage\NotificationTemplate\NotificationTemplateService;
use App\Services\Manage\TelegramBot\DTO\SendMessagesDTO;
use App\Services\Manage\TelegramBot\TelegramBotService;
use Illuminate\Http\JsonResponse;

class NotificationTemplateController extends Controller
{
    public function create(
        NotificationTemplateService $notificationTemplateService,
        RoleService $roleService,
        CompanyService $companyService,
    ): JsonResponse {
        return response()->json([
            'entities' => $notificationTemplateService->getEntitiesList(),
            'events' => $notificationTemplateService->mapEventsList(),
            'roles' => $roleService->getRolesList(),
            'companies' => $companyService->getCompanyForSelections(),
        ]);
    }

    public function store(StoreRequest $request, NotificationTemplateService $notificationTemplateService): JsonResponse
    {
        $notificationTemplateService->create($request->validated());

        return response()->json(['message' => 'success']);
    }

    public function sendMessage(SendMessageRequest $request, TelegramBotService $telegramBotService): JsonResponse
    {
        $sendMessageDTO = new SendMessagesDTO(
            companies: $request->input('companies'),
            message: $request->input('message'),
            roles: $request->input('roles'),
            isAllUsers: $request->input('isAllUsers'),
            isAllCompanies: $request->input('isAllCompanies'),
        );
        $telegramBotService->sendMessagesToCompanies($sendMessageDTO);

        return response()->json(['message' => 'success']);
    }

    public function edit(
        $id,
        NotificationTemplateService $notificationTemplateService,
        RoleService $roleService
    ): JsonResponse {
        $notificationTemplate = $notificationTemplateService->getById($id);

        return response()->json([
            'notificationTemplate' => [
                'name' => $notificationTemplate->name,
                'entity' => $notificationTemplate->entity,
                'event' => $notificationTemplate->event,
                'isAllUsers' => $notificationTemplate->all_roles,
                'roles' => $notificationTemplate->roles->pluck('id'),
                'isActive' => $notificationTemplate->is_active,
                'isClientShow' => $notificationTemplate->enable_for_client,
            ],
            'entities' => $notificationTemplateService->getEntitiesList(),
            'events' => $notificationTemplateService->mapEventsList(),
            'roles' => $roleService->getRolesList(),
        ]);
    }

    public function update(
        int $id,
        UpdateRequest $request,
        NotificationTemplateService $notificationTemplateService,
    ): JsonResponse {
        $notificationTemplateService->update($id, $request->validated());

        return response()->json(['message' => 'success']);
    }

    public function destroy(
        int $id,
        NotificationTemplateService $notificationTemplateService,
    ): JsonResponse {
        $notificationTemplateService->delete($id);

        return response()->json(['message' => 'success']);
    }
}
