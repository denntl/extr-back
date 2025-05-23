<?php

namespace App\Http\Controllers\Admin\Client;

use App\Events\Client\Application\ApplicationCreated;
use App\Events\Client\Application\ApplicationDeactivated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Client\Application\SaveApplicationRequest;
use App\Services\Client\Application\ApplicationService;
use App\Services\Client\Domain\DomainService;
use App\Services\Client\User\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class ApplicationController extends Controller
{
    public function create(ApplicationService $applicationService): JsonResponse
    {
        return response()->json([
            'domains' => DomainService::getDomainList(),
            'categories' => ApplicationService::getCategoryList(),
            'platforms' => ApplicationService::getPlatformList(),
            'whiteTypes' => ApplicationService::getWhiteTypeList(),
            'geos' => ApplicationService::getGeoList(),
            'languages' => ApplicationService::getLanguages(),
            'statuses' => ApplicationService::getStatusList(),
            'postbacks' => $applicationService->getPostbacks()->toArray(),
            'applications' => $applicationService->getApplicationsForSelection(auth()->id()),
        ]);
    }

    public function edit(int $id, ApplicationService $applicationService, UserService $userService): JsonResponse
    {
        Gate::authorize('update', [$applicationService->getByPublicId($id, false)]);
        $application = $applicationService->getByPublicId($id);

        return response()->json([
            'application' => $application,
            'topApplicationIds' => $applicationService->getTopApplicationsByPublicId($id),
            'domains' => DomainService::getDomainList(),
            'categories' => ApplicationService::getCategoryList(),
            'platforms' => ApplicationService::getPlatformList(),
            'whiteTypes' => ApplicationService::getWhiteTypeList(),
            'geos' => ApplicationService::getGeoList(),
            'languages' => ApplicationService::getLanguages(),
            'owners' => $userService->getUsersForSelections(),
            'statuses' => ApplicationService::getStatusList(),
            'postbacks' => $applicationService->getPostbacks()->toArray(),
            'applicationLink' => "https://$application->full_domain",
            'applications' => $applicationService->getApplicationsForSelection(auth()->id()),
        ]);
    }

    public function save(SaveApplicationRequest $request, ApplicationService $applicationService): JsonResponse
    {
        $applicationPublicId = $request->validated('public_id');
        $parameters = $request->validated();

        if ($applicationPublicId) {
            $application = $applicationService->getByPublicId($applicationPublicId, false);
            Gate::authorize('update', [$application]);
            if (!Gate::check('updateOwner', $application)) {
                // якщо у користувача нема прав на зміну власника, то видаляємо це поле
                unset($parameters['owner_id']);
            }
        }

        try {
            $savedApplication = $applicationService->save($parameters, auth()->id());
        } catch (\Throwable $e) {
            Log::error($e);

            return response()->json(['error' => $e->getMessage()], 500);
        }

        if (!$applicationPublicId) {
            event(new ApplicationCreated($savedApplication));
        }

        return response()->json([
            'application' => $applicationService->getByPublicId($savedApplication->public_id),
        ]);
    }

    public function clone(int $id, ApplicationService $applicationService): JsonResponse
    {
        Gate::authorize('update', [$applicationService->getByPublicId($id, false)]);

        $applicationService->clone($id, auth()->id());

        return response()->json([
            'message' => 'Successfully cloned',
        ]);
    }

    public function deactivate(int $id, ApplicationService $applicationService): JsonResponse
    {
        Gate::authorize('update', [$applicationService->getByPublicId($id, false)]);

        $applicationService->deactivate($id);

        return response()->json([
            'message' => 'Successfully deactivated',
        ]);
    }
    public function delete(int $id, ApplicationService $applicationService): JsonResponse
    {
        Gate::authorize('update', [$applicationService->getByPublicId($id, false)]); //TODO: check if this policy is correct

        $applicationService->deactivate($id);
        $applicationService->delete($id);

        return response()->json([
            'message' => 'Successfully deleted',
        ]);
    }
}
