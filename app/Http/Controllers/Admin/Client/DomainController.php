<?php

namespace App\Http\Controllers\Admin\Client;

use App\Events\Client\DomainStatusWasChanged;
use App\Events\Client\DomainWasCreated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Client\Domain\ChangeStatusRequest;
use App\Http\Requests\Admin\Client\Domain\StoreRequest;
use App\Services\Client\Domain\DomainService;
use Illuminate\Http\JsonResponse;

class DomainController extends Controller
{
    public function changeStatus(int $id, ChangeStatusRequest $request, DomainService $service): JsonResponse
    {
        $domain = $service->toggleStatus($id, $request->validated());
        event(new DomainStatusWasChanged($domain));

        return response()->json([
            'isChanged' => true,
            'message' => 'Статус домена обновлен',
        ]);
    }
    public function store(StoreRequest $request, DomainService $service): JsonResponse
    {
        $domain = $service->store($request->validated());
        event(new DomainWasCreated($domain));

        return response()->json([
            'isCreated' => true,
            'message' => 'Домен успешно создан',
        ]);
    }
}
