<?php

namespace App\Services\Common\DataListing\Entities;

use App\Enums\Authorization\PermissionName;
use App\Models\ApplicationComment;
use App\Services\Common\DataListing\CoreListing;
use App\Services\Common\DataListing\DTO\FilterDTO;
use App\Services\Common\DataListing\DTO\JoinDTO;
use App\Services\Common\DataListing\DTO\OrderDto;
use App\Services\Common\DataListing\Enums\FilterOperator;
use App\Services\Common\DataListing\Enums\JoinEnum;
use App\Services\Common\DataListing\Enums\SortingType;
use App\Services\Common\DataListing\FieldCollector;
use App\Services\Common\DataListing\Fields\FieldInt;
use App\Services\Common\DataListing\Fields\FieldString;
use Illuminate\Database\Eloquent\Model;

class ManageApplicationCommentListing extends CoreListing
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
        return [PermissionName::ManageApplicationSave];
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
                FieldString::init('application_id', 'applications.id')
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
        ];
    }
}
