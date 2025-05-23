<?php

namespace App\Services\Common\DataListing\Fields;

use App\Services\Common\DataListing\DTO\FilterDTO;
use App\Services\Common\DataListing\Enums\FieldType;
use App\Services\Common\DataListing\Enums\FilterOperator;
use Illuminate\Database\Eloquent\Builder;

class FieldList extends BaseField
{
    protected bool $isSearchable = false;

    protected array $filterOperators = [
        FilterOperator::In->value,
        FilterOperator::NotIn->value,
    ];

    public function applyFilter(Builder $query, FilterDTO $filter): Builder
    {
        $value = $filter->getValue();
        if (empty($value)) {
            return $query;
        }

        if (!is_array($value)) {
            throw new \Exception("Invalid filter value: $value");
        }

        if ($filter->getOperator() === FilterOperator::In) {
            $query->whereIn($this->getFieldName(), $value);
        } elseif ($filter->getOperator() === FilterOperator::NotIn) {
            $query->whereNotIn($this->getFieldName(), $value);
        }

        return $query;
    }


    protected function initType(): FieldType
    {
        return FieldType::List;
    }
}
