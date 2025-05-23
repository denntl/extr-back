<?php

namespace App\Services\Common\DataListing\Fields;

use App\Services\Common\DataListing\DTO\FilterDTO;
use App\Services\Common\DataListing\Enums\FieldType;
use App\Services\Common\DataListing\Enums\FilterOperator;
use Illuminate\Database\Eloquent\Builder;

class FieldString extends BaseField
{
    protected array $filterOperators = [
        FilterOperator::Equal->value,
        FilterOperator::NotEqual->value,
        FilterOperator::Contains->value,
        FilterOperator::NotContains->value,
    ];

    protected function initType(): FieldType
    {
        return FieldType::String;
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
            case FilterOperator::Contains:
                $query->where($this->getFieldName(), 'like', '%' . $filter->getValue() . '%');
                break;
            case FilterOperator::NotContains:
                $query->where($this->getFieldName(), 'not like', '%' . $filter->getValue() . '%');
                break;
        }

        return $query;
    }
}
