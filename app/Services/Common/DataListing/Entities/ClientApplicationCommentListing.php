<?php

namespace App\Services\Common\DataListing\Entities;

use App\Enums\Authorization\PermissionName;
use App\Models\ApplicationComment;
use App\Models\Company;
use App\Services\Common\DataListing\CoreListing;
use App\Services\Common\DataListing\DTO\FilterDTO;
use App\Services\Common\DataListing\DTO\OrderDto;
use App\Services\Common\DataListing\Enums\FilterOperator;
use App\Services\Common\DataListing\Enums\SortingType;
use App\Services\Common\DataListing\FieldCollector;
use App\Services\Common\DataListing\Fields\FieldAction;
use App\Services\Common\DataListing\Fields\FieldDate;
use App\Services\Common\DataListing\Fields\FieldInt;
use App\Services\Common\DataListing\Fields\FieldList;
use App\Services\Common\DataListing\Fields\FieldString;
use App\Services\Manage\User\UserService;
use Illuminate\Database\Eloquent\Model;

class ClientApplicationCommentListing extends CoreListing
{
    protected const PER_PAGE = 5;

    protected function getModel(): Model
    {
        return new ApplicationComment();
    }

    /**
     * @return array|PermissionName[]
     */
    protected function getPermission(): array
    {
        return [PermissionName::ClientApplicationSave];
    }

    protected function getFieldCollector(): FieldCollector
    {
        return FieldCollector::init()
            ->setBatch([
                FieldInt::init('id', 'application_comments.id', 'ID'),
                FieldInt::init('company_id', 'applications.company_id')->hideInSelect(),
                FieldInt::init('stars', 'application_comments.stars', 'Рейтинг'),
                FieldString::init('author_name', 'application_comments.author_name', 'Автор'),
                FieldString::init('text', 'application_comments.text', 'Комментарий'),
                FieldString::init('public_id', 'applications.public_id')
                    ->withScope('publicId'),
            ]);
    }

    protected function defaultOrder(): OrderDto
    {
        return new OrderDto('created_at', SortingType::Desc);
    }

    /**
     * @throws \Exception
     */
    protected function predefinedFilters(): array
    {
        return [
            new FilterDTO('company_id', FilterOperator::Equal, $this->listingModel->user->company_id),
        ];
    }
}
