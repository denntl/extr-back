<?php

namespace App\Http\Controllers\Admin\Client;

use App\Enums\Invite\ActionName;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Client\Company\DepositRequest;
use App\Http\Requests\Admin\Client\Company\UpdateRequest;
use App\Services\Client\Application\ApplicationService;
use App\Services\Client\Company\CompanyService;
use App\Services\Client\Invite\DTO\ActionDTO;
use App\Services\Client\Invite\InviteService;
use App\Services\Common\BalanceTransaction\BalanceTransactionService;
use App\Services\Common\BalanceTransaction\Handlers\DTO\NowPaymentsDTO;
use App\Services\Common\BalanceTransaction\Handlers\NowPaymentsHandler;
use Illuminate\Http\JsonResponse;

class CompanyController extends Controller
{
    /**
     * @param CompanyService $companyService
     * @return JsonResponse
     */
    public function edit(CompanyService $companyService): JsonResponse
    {
        return response()->json([
            'company' => $companyService->get(),
        ]);
    }

    /**
     * @param UpdateRequest $request
     * @param CompanyService $companyService
     * @param ApplicationService $applicationService
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, CompanyService $companyService, ApplicationService $applicationService): JsonResponse
    {
        // TODO: add policy
        $companyService->update($request->validated());

        return response()->json([
            'isUpdated' => true,
            'message' => 'Компания успешно обновлена',
        ]);
    }

    /**
     * @param InviteService $inviteService
     * @return JsonResponse
     */
    public function getInvite(InviteService $inviteService): JsonResponse
    {
        $actionDTO = new ActionDTO(
            auth()->id(),
            ActionName::Registration
        );
        $invite = $inviteService->generateInvite($actionDTO);

        return response()->json(['key' => $invite->key, 'expiredAt' => $invite->expireAt]);
    }

    /**
     * @param DepositRequest $request
     * @param CompanyService $companyService
     * @return JsonResponse
     */
    public function deposit(DepositRequest $request, CompanyService $companyService): JsonResponse
    {
        /** @var BalanceTransactionService $balanceTransactionService */
        $balanceTransactionService = app(BalanceTransactionService::class, [
            'handler' => new NowPaymentsHandler()
        ]);
        $balanceTransactionCreateData = $balanceTransactionService->create(
            NowPaymentsDTO::fromArray(
                array_merge($request->validated(), [
                    'companyId' => $companyService->get(false)->id,
                    'userId' => auth()->id(),
                ])
            )
        );

        return response()->json([
            'redirectUrl' => $balanceTransactionCreateData->getHandlerResponse()['redirectUrl'],
        ]);
    }
}
