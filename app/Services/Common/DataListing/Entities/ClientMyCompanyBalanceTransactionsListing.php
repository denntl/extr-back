<?php

namespace App\Services\Common\DataListing\Entities;

use App\Enums\Authorization\PermissionName;
use App\Models\BalanceTransaction;
use App\Services\Client\User\UserService;
use App\Services\Common\BalanceTransaction\BalanceTransactionService;
use App\Services\Common\CompanyBalance\CompanyBalanceService;
use App\Services\Common\DataListing\CoreListing;
use App\Services\Common\DataListing\DTO\OrderDto;
use App\Services\Common\DataListing\Enums\SortingType;
use App\Services\Common\DataListing\FieldCollector;
use App\Services\Common\DataListing\Fields\FieldAction;
use App\Services\Common\DataListing\Fields\FieldDateTime;
use App\Services\Common\DataListing\Fields\FieldFloat;
use App\Services\Common\DataListing\Fields\FieldList;
use App\Services\Common\DataListing\Fields\FieldString;
use App\Services\Common\Payment\PaymentService;
use Illuminate\Database\Eloquent\Model;

class ClientMyCompanyBalanceTransactionsListing extends CoreListing
{
    protected function getModel(): Model
    {
        return new BalanceTransaction();
    }

    /**
     * @return array|PermissionName[]
     */
    protected function getPermission(): array
    {
        return [PermissionName::ClientCompanyBalanceTransactionsRead];
    }

    protected function getFieldCollector(): FieldCollector
    {
        return FieldCollector::init()
            ->setBatch([
                FieldDateTime::init('created_at', 'balance_transactions.created_at', 'Дата')
                    ->setFilterField('balance_transactions.created_at')
                    ->withScope('companyId', auth()->user()->company->id),
                FieldFloat::init('amount', 'balance_transactions.amount', 'Сумма'),
                FieldList::init('status', 'balance_transactions.status', 'Статус')
                    ->setFilterField('balance_transactions.status')
                    ->setListName('balance_transaction_statuses'),
                FieldFloat::init('balance_after', 'balance_transactions.balance_after', 'Баланс'),
                FieldList::init('balance_type', 'balance_transactions.balance_type', 'Тип баланса')
                    ->setFilterField('balance_transactions.balance_type')
                    ->setListName('balance_types'),
                FieldList::init('type', 'balance_transactions.type', 'Тип операции')
                    ->setFilterField('balance_transactions.type')
                    ->setListName('balance_transaction_types'),
                FieldList::init('processor_id', 'payments.processor_id', 'Провайдер')
                    ->withScope('payment')
                    ->setFilterField('payments.processor_id')
                    ->setListName('payment_processor_types'),
                FieldList::init('user_id', 'balance_transactions.user_id', 'Инициатор')
                    ->setFilterField('balance_transactions.user_id')
                    ->setListName('users'),
                FieldString::init('application_name', 'applications.full_domain', 'Приложение')
                    ->withScope('application')
                    ->setFilterField('applications.full_domain'),
                FieldString::init('comment', 'payments.comment', 'Комментарий')
                    ->makeInvisible()
                    ->makeUnSearchable()
                    ->hideInFilterSelect(),
                FieldAction::init('actions', '', 'Действия'),
            ]);
    }

    protected function defaultOrder(): OrderDto
    {
        return new OrderDto('id', SortingType::Desc);
    }

    protected function getListItems(): array
    {
        /** @var UserService $userService */
        $userService = app(UserService::class);
        return [
            'users' => $userService->getTransactionUsersForSelections(),
            'balance_transaction_types' => BalanceTransactionService::getTypeList(),
            'balance_types' => CompanyBalanceService::getTypes(),
            'balance_transaction_statuses' => BalanceTransactionService::getStatuses(),
            'payment_processor_types' => PaymentService::getProcessorTypeList(),
        ];
    }
}
