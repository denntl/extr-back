<?php

namespace App\Services\Common\BalanceTransaction;

use App\Enums\BalanceTransaction\Type;
use App\Models\BalanceTransaction;
use App\Enums\BalanceTransaction\Status;
use App\Models\CompanyBalance;
use App\Enums\Balance\Type as BalanceType;
use App\Models\Payment;
use App\Services\Common\BalanceTransaction\DTO\BalanceTransactionCreateDTO;
use App\Services\Common\BalanceTransaction\Handlers\Interfaces\CreateTransactionDTOInterface;
use App\Services\Common\BalanceTransaction\Handlers\Interfaces\HandlerInterface;
use App\Services\Common\CompanyBalance\CompanyBalanceService;
use App\Services\Common\CompanyBalance\DTO\CompanyBalanceUpdatedDTO;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class BalanceTransactionService
{
    public function __construct(private HandlerInterface $handler)
    {
    }

    /**
     * @return array[]
     */
    public static function getTypeList(): array
    {
        return [
            ['value' => Type::Deposit->value, 'label' => 'Депозит'],
            ['value' => Type::Install->value, 'label' => 'Оплата за инсталл'],
        ];
    }

    /**
     * @return array[]
     */
    public static function getStatuses(): array
    {
        return [
            ['value' => Status::Pending->value, 'label' => 'Ожидает'],
            ['value' => Status::Approved->value, 'label' => 'Успешно'],
            ['value' => Status::Declined->value, 'label' => 'Ошибка'],
            ['value' => Status::Canceled->value, 'label' => 'Просрочено'],
        ];
    }

    /**
     * @param CreateTransactionDTOInterface $data
     * @return BalanceTransactionCreateDTO
     */
    public function create(CreateTransactionDTOInterface $data): BalanceTransactionCreateDTO
    {
        $balanceTransaction = BalanceTransaction::create([
            'company_id' => $data->getCompanyId(),
            'user_id' => $data->getUserId(),
            'amount' => $this->handler->getAmount($data),
            'balance_type' => $this->handler->getBalanceType($data)->value,
            'type' => $data->getType()->value,
            'status' => Status::Pending->value,
        ]);
        $handleResponse = $this->handler->create($data, $balanceTransaction);

        return new BalanceTransactionCreateDTO($balanceTransaction, $handleResponse);
    }

    /**
     * @param BalanceTransaction $balanceTransaction
     * @param array $data
     * @return CompanyBalanceUpdatedDTO|null
     * @throws Throwable
     */
    public function handle(BalanceTransaction $balanceTransaction, array $data = []): ?CompanyBalanceUpdatedDTO
    {
        $responseHandle = $this->handler->handle($balanceTransaction, $data);
        if ($responseHandle->getBalanceTransactionStatus() !== Status::Approved) {
            if ($responseHandle->getBalanceTransactionStatus()->value !== $balanceTransaction->status) {
                $balanceTransaction->update([
                    'status' => $responseHandle->getBalanceTransactionStatus()->value,
                ]);
            }

            return null;
        }
        try {
            DB::beginTransaction();
            $balanceTransactionAmount = $balanceTransaction->amount;
            if ($responseHandle->getAmount() !== $balanceTransaction->amount) {
                Log::channel('payments')->info('Webhook Amount Changed', [
                    'newAmount' => $responseHandle->getAmount(),
                    'newStatus' => $responseHandle->getBalanceTransactionStatus()->value,
                    'balanceTransaction' => $balanceTransaction->toArray(),
                ]);
                $balanceTransactionAmount = $responseHandle->getAmount();
            }
            $balanceColumn = CompanyBalanceService::getColumnNameByTypeId($balanceTransaction->balance_type);
            $companyBalance = CompanyBalance::where('company_id', $balanceTransaction->company_id)
                ->lockForUpdate()
                ->firstOrFail();
            $balanceBefore = $companyBalance->{$balanceColumn};
            if (
                $balanceTransaction->balance_type === BalanceType::BalanceBonus->value &&
                $balanceBefore < abs($balanceTransactionAmount)
            ) {
                //TODO: Наблюдать за этим кейсом
                /**
                 * https://git.tradition.com.ua/eg-dev/pwa-app/-/merge_requests/283#note_46770
                 */
                Log::error('BalanceTransactionService@handle', [
                    'message' => 'BalanceBonus is less than amount',
                    'balanceTransaction' => $balanceTransaction->toArray(),
                ]);
            }
            $companyBalance->increment($balanceColumn, $balanceTransactionAmount);
            $balanceAfter = $companyBalance->{$balanceColumn};
            $balanceTransaction->update([
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'status' => Status::Approved->value,
                'amount' => $balanceTransactionAmount,
            ]);

            DB::commit();

            return new CompanyBalanceUpdatedDTO($balanceTransaction->company_id, $balanceBefore, $balanceAfter);
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @param BalanceTransaction $balanceTransaction
     * @param Payment $payment
     * @return BalanceTransaction
     * @throws Throwable
     */
    public function cloneBalanceTransactionWithPayment(BalanceTransaction $balanceTransaction, Payment $payment): BalanceTransaction
    {
        try {
            DB::beginTransaction();

            $newBalanceTransaction = $balanceTransaction->replicate();
            $newBalanceTransaction->status = Status::Pending->value;
            $newBalanceTransaction->balance_after = null;
            $newBalanceTransaction->balance_before = null;
            $newBalanceTransaction->save();

            $newPayment = $payment->replicate();
            $newPayment->balance_transaction_id = $newBalanceTransaction->id;
            $newPayment->status = Status::Pending->value;
            $newPayment->payment_id = null;
            $newPayment->save();

            DB::commit();

            return $newBalanceTransaction;
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @param int $id
     * @return BalanceTransaction
     */
    public function getById(int $id): BalanceTransaction
    {
        return BalanceTransaction::query()->findOrFail($id);
    }
}
