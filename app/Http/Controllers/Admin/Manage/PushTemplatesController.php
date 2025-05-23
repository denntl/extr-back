<?php

namespace App\Http\Controllers\Admin\Manage;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Manage\PushTemplate\StoreRequest;
use App\Models\PushTemplate;
use App\Services\Client\Application\ApplicationService;
use App\Services\Manage\PushTemplate\PushTemplateService;
use Illuminate\Http\JsonResponse;

class PushTemplatesController extends Controller
{
    public function create(): JsonResponse
    {
        return response()->json([
            'events' => PushTemplateService::getEventsList(),
            'geos' => ApplicationService::getGeoList(),
        ]);
    }

    public function store(StoreRequest $request, PushTemplateService $service): JsonResponse
    {
        $template = $service->create($request->validated(), auth()->id());
        return response()->json([
            'id' => $template->id,
        ]);
    }

    public function edit(int $id, PushTemplateService $service): JsonResponse
    {
        return response()->json([
            'values' => $service->getById($id)->toArray(),
            'events' => PushTemplateService::getEventsList(),
            'geos' => ApplicationService::getGeoList(),
        ]);
    }

    public function update(int $id, StoreRequest $request, PushTemplateService $service): JsonResponse
    {
        $template = $service->update($id, $request->validated(), auth()->id());
        return response()->json([
            'id' => $template->id,
        ]);
    }
}
