<?php

namespace App\Services\Common\DataListing\Fields;

use App\Services\Common\DataListing\DTO\FilterDTO;
use App\Services\Common\DataListing\Enums\DateFilterOption;
use App\Services\Common\DataListing\Enums\FieldType;
use App\Services\Common\DataListing\Enums\FilterOperator;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class FieldDateTime extends BaseField
{
    protected bool $isSearchable = false;

    public function __construct(string $label, string $name, ?string $dbField)
    {
        parent::__construct($label, $name, $dbField);

        $this->transformDataFunc = function (Carbon|string|null $value) {
            if (!$value) {
                return null;
            }

            if ($value instanceof Carbon) {
                return $value->toDateTimeString();
            }

            return Carbon::parse($value)->toDateTimeString();
        };
    }

    protected array $filterOperators = [
        FilterOperator::Equal->value,
        FilterOperator::NotEqual->value,
        FilterOperator::GreaterThanOrEqual->value,
        FilterOperator::LessThanOrEqual->value,
        FilterOperator::Between->value,
        FilterOperator::Empty->value,
        FilterOperator::NotEmpty->value,
    ];

    protected array $quickFilters = [
        DateFilterOption::Today->value,
        DateFilterOption::Yesterday->value,
        DateFilterOption::ThisWeek->value,
        DateFilterOption::ThisMonth->value,
        DateFilterOption::NextWeek->value,
        DateFilterOption::NextMonth->value,
    ];

    public function setQuickFilters(array $filters): self
    {
        $this->quickFilters = $filters;

        return $this;
    }

    protected function initType(): FieldType
    {
        return FieldType::Datetime;
    }

    /**
     * @param Builder $query
     * @param FilterDTO $filter
     * @return Builder
     */
    public function applyFilter(Builder $query, FilterDTO $filter): Builder
    {
        $value = $filter->getValue();
        if (
            empty($value) &&
            !in_array($filter->getOperator(), [FilterOperator::Empty, FilterOperator::NotEmpty])
        ) {
            return $query;
        }

        switch ($filter->getOperator()) {
            case FilterOperator::Equal:
                $query->where($this->getFieldName(), '=', $value);
                break;
            case FilterOperator::NotEqual:
                $query->where($this->getFieldName(), '<>', $value);
                break;
            case FilterOperator::GreaterThanOrEqual:
                $query->where($this->getFieldName(), '>=', $value);
                break;
            case FilterOperator::LessThanOrEqual:
                $query->where($this->getFieldName(), '<=', $value);
                break;
            case FilterOperator::Between:
                $query->whereBetween($this->getFieldName(), [$value[0], $value[1]]);
                break;
            case FilterOperator::Empty:
                $query->whereNull($this->getFieldName());
                break;
            case FilterOperator::NotEmpty:
                $query->whereNotNull($this->getFieldName());
                break;
        }

        return $query;
    }
}
