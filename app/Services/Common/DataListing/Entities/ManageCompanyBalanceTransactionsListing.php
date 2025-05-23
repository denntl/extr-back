<?php

namespace App\Services\Common\DataListing\Entities;

use App\Enums\Authorization\PermissionName;
use App\Models\BalanceTransaction;
use App\Services\Common\BalanceTransaction\BalanceTransactionService;
use App\Services\Common\CompanyBalance\CompanyBalanceService;
use App\Services\Common\DataListing\CoreListing;
use App\Services\Common\DataListing\DTO\FilterDTO;
use App\Services\Common\DataListing\DTO\OrderDto;
use App\Services\Common\DataListing\Enums\FilterOperator;
use App\Services\Common\DataListing\Enums\SortingType;
use App\Services\Common\DataListing\FieldCollector;
use App\Services\Common\DataListing\Fields\FieldAction;
use App\Services\Common\DataListing\Fields\FieldDateTime;
use App\Services\Common\DataListing\Fields\FieldFloat;
use App\Services\Common\DataListing\Fields\FieldInt;
use App\Services\Common\DataListing\Fields\FieldList;
use App\Services\Common\DataListing\Fields\FieldString;
use App\Services\Common\DataListing\Models\DetailedCompanyBalanceTransactionsListingModel;
use App\Services\Common\Payment\PaymentService;
use App\Services\Manage\User\UserService;
use Exception;
use Illuminate\Database\Eloquent\Model;

/**
 * @param DetailedCompanyBalanceTransactionsListingModel $listingModel
 */
class ManageCompanyBalanceTransactionsListing extends CoreListing
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
        return [PermissionName::ManageCompanyBalanceTransactionsRead];
    }

    protected function getFieldCollector(): FieldCollector
    {
        return FieldCollector::init()
            ->setBatch([
                FieldDateTime::init('created_at', 'balance_transactions.created_at', 'Дата')
                    ->setFilterField('balance_transactions.created_at'),
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
                FieldInt::init('company_id', 'balance_transactions.company_id', 'Компания')
                    ->hideInSelect(),
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
            'users' => $userService->getUsersForSelections(true),
            'balance_transaction_types' => BalanceTransactionService::getTypeList(),
            'balance_types' => CompanyBalanceService::getTypes(),
            'balance_transaction_statuses' => BalanceTransactionService::getStatuses(),
            'payment_processor_types' => PaymentService::getProcessorTypeList(),
        ];
    }

    /**
     * @throws Exception
     */
    protected function predefinedFilters(): array
    {
        if (!empty($this->listingModel->companyId)) {
            return [
                new FilterDTO('company_id', FilterOperator::Equal, $this->listingModel->companyId),
            ];
        }

        return [];
    }
}
