<?php

namespace App\Http\Controllers\Admin\Manage;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Manage\Company\ManualBalanceDepositRequest;
use App\Http\Requests\Admin\Manage\Company\UpdateRequest;
use App\Services\Common\BalanceTransaction\BalanceTransactionService;
use App\Services\Common\BalanceTransaction\Handlers\DTO\ManualPaymentDTO;
use App\Services\Common\BalanceTransaction\Handlers\ManualPaymentHandler;
use App\Services\Manage\Company\CompanyService;
use Illuminate\Http\JsonResponse;
use Throwable;

class CompanyController extends Controller
{
    public function edit(int $id, CompanyService $companyService): JsonResponse
    {
        return response()->json([
            'company' => $companyService->getCompanyById($id)->only('name', 'owner_id'),
            'users' => $companyService->getListOfCompanyUsers($id),
        ]);
    }

    public function update(int $id, UpdateRequest $request, CompanyService $companyService): JsonResponse
    {
        $companyService->update($id, $request->validated());

        return response()->json([
            'isUpdated' => true,
            'message' => 'Компания успешно обновлена',
        ]);
    }

    /**
     * @param int $companyId
     * @param ManualBalanceDepositRequest $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function manualBalanceDeposit(int $companyId, ManualBalanceDepositRequest $request): JsonResponse
    {
        /** @var BalanceTransactionService $balanceTransactionService */
        $balanceTransactionService = app(BalanceTransactionService::class, [
            'handler' => new ManualPaymentHandler()
        ]);
        $balanceTransactionServiceCreate = $balanceTransactionService->create(
            ManualPaymentDTO::fromArray(
                array_merge($request->validated(), [
                    'companyId' => $companyId,
                    'userId' => auth()->id(),
                ])
            )
        );
        $balanceTransactionService->handle($balanceTransactionServiceCreate->getBalanceTransaction());

        return response()->json([
            'isUpdated' => true,
            'message' => 'Баланс компании успешно обновлен',
        ]);
    }
}
