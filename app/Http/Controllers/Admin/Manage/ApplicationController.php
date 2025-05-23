<?php

namespace App\Http\Controllers\Admin\Manage;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Manage\Application\SaveApplicationRequest;
use App\Services\Client\Domain\DomainService;
use App\Services\Manage\Application\ApplicationService;
use App\Services\Client\Application\ApplicationService as ClientApplicationService;
use App\Services\Manage\User\UserService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class ApplicationController extends Controller
{
    public function edit(int $id, ApplicationService $applicationService, UserService $userService)
    {
        $application = $applicationService->getById($id);
        return response()->json([
            'application' => $application,
            'domains' => DomainService::getDomainList(),
            'categories' => ClientApplicationService::getCategoryList(),
            'platforms' => ClientApplicationService::getPlatformList(),
            'whiteTypes' => ClientApplicationService::getWhiteTypeList(),
            'geos' => ClientApplicationService::getGeoList(),
            'languages' => ClientApplicationService::getLanguages(),
            'owners' => $userService->getUsersForSelections(),
            'statuses' => ClientApplicationService::getStatusList(),
            'postbacks' => $applicationService->getPostbacks()->toArray(),
            'applicationLink' => "https://$application->full_domain",
            'onesignalWebhook' => route('onesignal.webhooks'),
        ]);
    }

    public function save(SaveApplicationRequest $request, ApplicationService $applicationService)
    {
        $parameters = $request->validated();
        try {
            $savedApplication = $applicationService->save($parameters, auth()->id());
        } catch (\Throwable $e) {
            Log::error($e);

            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json([
            'application' => $applicationService->getById($savedApplication->id),
        ]);
    }

    public function clone(int $id, ApplicationService $applicationService): JsonResponse
    {
        $applicationService->clone($id, auth()->id());

        return response()->json([
            'message' => 'Successfully cloned',
        ]);
    }

    public function deactivate(int $id, ApplicationService $applicationService): JsonResponse
    {
        $applicationService->deactivate($id);

        return response()->json([
            'message' => 'Successfully deactivated',
        ]);
    }

    public function delete(int $id, ApplicationService $applicationService): JsonResponse
    {
        try {
            $applicationService->deactivate($id);
            $applicationService->delete($id);
        } catch (ModelNotFoundException $e) {
            Log::error('Application not found', [
                'message' => $e->getMessage(),
                'trace' => $e->getTrace(),
            ]);
            return response()->json(['errors' => [
                'common' => [
                    'Application not found.',
                ]
            ]], 404);
        } catch (\Throwable $e) {
            Log::error($e);
            return response()->json(['errors' => [
                'common' => [
                    'Something went wrong while deleting application.',
                ]
            ]], 500);
        }

        return response()->json([
            'message' => 'Successfully deleted',
        ]);
    }

    public function restore(int $id, ApplicationService $applicationService): JsonResponse
    {
        $applicationService->restore($id);

        return response()->json([
            'message' => 'Successfully restored',
        ]);
    }
}
