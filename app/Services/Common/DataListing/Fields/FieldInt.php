<?php

namespace App\Services\Common\DataListing\Fields;

use App\Services\Common\DataListing\DTO\FilterDTO;
use App\Services\Common\DataListing\Enums\FieldType;
use App\Services\Common\DataListing\Enums\FilterOperator;
use Illuminate\Database\Eloquent\Builder;

class FieldInt extends BaseField
{
    protected int $step = 1;

    protected array $filterOperators = [
        FilterOperator::Equal->value,
        FilterOperator::NotEqual->value,
        FilterOperator::GreaterThanOrEqual->value,
        FilterOperator::LessThanOrEqual->value,
        FilterOperator::Between->value,
    ];

    public function getStep(): int
    {
        return $this->step;
    }

    public function setStep(int $step): void
    {
        $this->step = $step;
    }

    protected function initType(): FieldType
    {
        return FieldType::Int;
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
            case FilterOperator::GreaterThanOrEqual:
                $query->where($this->getFieldName(), '>=', $filter->getValue());
                break;
            case FilterOperator::LessThanOrEqual:
                $query->where($this->getFieldName(), '<=', $filter->getValue());
                break;
            case FilterOperator::In:
                $query->whereIn($this->getFieldName(), $filter->getValue());
                break;
        }

        return $query;
    }

    public function toArray(): array
    {
        $res = parent::toArray();

        $res['step'] = $this->step;

        return $res;
    }
}
