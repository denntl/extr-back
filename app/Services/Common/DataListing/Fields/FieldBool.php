<?php

namespace App\Services\Common\DataListing\Fields;

use App\Services\Common\DataListing\DTO\FilterDTO;
use App\Services\Common\DataListing\Enums\FieldType;
use App\Services\Common\DataListing\Enums\FilterOperator;
use Illuminate\Database\Eloquent\Builder;

class FieldBool extends BaseField
{
    protected bool $isSearchable = false;

    protected array $filterOperators = [
        FilterOperator::Equal->value,
        FilterOperator::NotEqual->value,
    ];

    protected function initType(): FieldType
    {
        return FieldType::Bool;
    }

    public function applyFilter(Builder $query, FilterDTO $filter): Builder
    {
        switch ($filter->getOperator()) {
            case FilterOperator::Equal:
                $query->where($this->getFieldName(), '=', $filter->getValue());
                break;
            case FilterOperator::NotEqual:
                $query->where($this->getFieldName(), '<>', $filter->getValue());
                break;
        }

        return $query;
    }
}
