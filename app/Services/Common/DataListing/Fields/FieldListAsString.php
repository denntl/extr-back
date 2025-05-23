<?php

namespace App\Services\Common\DataListing\Fields;

use App\Services\Common\DataListing\DTO\FilterDTO;
use App\Services\Common\DataListing\Enums\FieldType;
use App\Services\Common\DataListing\Enums\FilterOperator;
use Illuminate\Database\Eloquent\Builder;

class FieldListAsString extends BaseField
{
    protected bool $isSearchable = false;

    protected array $filterOperators = [
        FilterOperator::In->value,
        FilterOperator::NotIn->value,
    ];

    protected function __construct(string $label, string $name, ?string $dbField)
    {
        parent::__construct($label, $name, $dbField);

        $this->transformDataFunc = function ($value) {
            if (is_string($value)) {
                $value = json_decode($value, true);
            }

            return $value;
        };
    }

    protected function initType(): FieldType
    {
        return FieldType::ListAsString;
    }

    public function applyFilter(Builder $query, FilterDTO $filter): Builder
    {
        $value = $filter->getValue();
        $condition = $filter->getOperator() === FilterOperator::In ? 'like' : 'not like';

        if (empty($value)) {
            return $query;
        }
        if (!is_array($value)) {
            throw new \Exception("Invalid filter value: $value");
        }
        $query->where(function ($query) use ($value, $condition) {
            foreach ($value as $item) {
                $query->orWhere($this->getFieldName(), $condition, '%"' . $item . '"%');
            }
        });

        return $query;
    }

    public function hasTransform(): bool
    {
        return true;
    }
}
