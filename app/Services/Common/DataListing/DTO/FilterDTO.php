<?php

namespace App\Services\Common\DataListing\DTO;

use App\Services\Common\DataListing\Enums\FilterOperator;
use Exception;

class FilterDTO
{
    protected string $field;
    protected FilterOperator $operator;
    /** @var array|string|string[]|int|int[] */
    protected array|string|int|float|bool|null $value;

    /**
     * @param string $field
     * @param string|FilterOperator $operator
     * @param $value
     * @throws Exception
     */
    public function __construct(string $field, string|FilterOperator $operator, $value)
    {
        $this->field = $field;
        $this->operator = $operator instanceof FilterOperator ? $operator : $this->mapOperator($operator);
        $this->value = $value;
    }

    public function toArrayFront(): array
    {
        return [
            'name' => $this->field,
            'operator' => $this->operator,
            'value' => $this->value
        ];
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getOperator(): FilterOperator
    {
        return $this->operator;
    }

    /** @return array|string|string[]|int|int[] */
    public function getValue(): array|string|int|float|null
    {
        return $this->value;
    }

    private function mapOperator(string $operator): FilterOperator
    {
        return match ($operator) {
            FilterOperator::Equal->value => FilterOperator::Equal,
            FilterOperator::NotEqual->value => FilterOperator::NotEqual,
            FilterOperator::GreaterThanOrEqual->value => FilterOperator::GreaterThanOrEqual,
            FilterOperator::LessThanOrEqual->value => FilterOperator::LessThanOrEqual,
            FilterOperator::In->value => FilterOperator::In,
            FilterOperator::NotIn->value => FilterOperator::NotIn,
            FilterOperator::Contains->value => FilterOperator::Contains,
            FilterOperator::NotContains->value => FilterOperator::NotContains,
            FilterOperator::Between->value => FilterOperator::Between,
            FilterOperator::Empty->value => FilterOperator::Empty,
            FilterOperator::NotEmpty->value => FilterOperator::NotEmpty,
            default => throw new Exception("Invalid filter operator: $operator"),
        };
    }
}
