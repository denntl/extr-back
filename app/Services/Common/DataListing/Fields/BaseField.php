<?php

namespace App\Services\Common\DataListing\Fields;

use App\Services\Common\DataListing\DTO\FilterDTO;
use App\Services\Common\DataListing\DTO\JoinDTO;
use App\Services\Common\DataListing\Enums\Aggregation;
use App\Services\Common\DataListing\Enums\FieldType;
use App\Services\Common\DataListing\Enums\FieldTypeRangeLength;
use Illuminate\Database\Eloquent\Builder;

abstract class BaseField implements FieldInterface
{
    protected string $label;
    protected string $name;
    protected FieldType $type;
    protected ?string $listName = null;
    protected bool $enableToSelect = true;
    protected bool $isVisible = true;
    protected bool $isSortable = true;
    protected bool $isFilterable = true;
    protected bool $isSearchable = true;
    protected ?string $filterField = null;
    protected ?string $dbField;
    protected ?JoinDTO $join = null;
    protected array $groupBy = [];
    protected array $filterOperators = [];
    protected string $scope = '';
    protected mixed $scopeParams;
    /** @var callable|null */
    protected $transformDataFunc = null;

    protected ?int $typeRangeLength = null;

    protected array $customStyles = [];

    protected ?Aggregation $aggregation = null;

    public static function init(string $name, string $dbField, ?string $label = ''): self
    {
        return new static($label, $name, $dbField);
    }

    protected function __construct(string $label, string $name, ?string $dbField)
    {
        $this->type = $this->initType();
        $this->label = $label;
        $this->name = $name;
        $this->dbField = $dbField;
    }

    abstract protected function initType(): FieldType;

    public function withScope(string $scope, mixed $params = null): self
    {
        $this->scope = $scope;
        $this->scopeParams = $params;
        return $this;
    }
    public function getScope(): string
    {
        return $this->scope;
    }
    public function getScopeParams(): mixed
    {
        return $this->scopeParams;
    }

    public function applyFilter(Builder $query, FilterDTO $filter): Builder
    {
        return $query;
    }

    public function isListType(): bool
    {
        return $this->type === FieldType::List;
    }

    public function isIntType(): bool
    {
        return $this->type === FieldType::Int;
    }

    public function isSearchable(): bool
    {
        return $this->isSearchable;
    }

    public function setListName(string $listName): self
    {
        $this->listName = $listName;
        return $this;
    }

    public function setCustomStyles(array $styles): self
    {
        $this->customStyles = $styles;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): FieldType
    {
        return $this->type;
    }

    public function getDbField(): ?string
    {
        return $this->dbField;
    }

    public function makeUnSortable(): self
    {
        $this->isSortable = false;

        return $this;
    }

    public function hideInFilterSelect(): self
    {
        $this->isFilterable = false;

        return $this;
    }

    public function hideInSelect(): self
    {
        $this->enableToSelect = false;
        $this->isFilterable = false;
        $this->isSortable = false;
        $this->isVisible = false;

        return $this;
    }

    public function isEnableToSelect(): bool
    {
        return $this->enableToSelect;
    }

    public function makeInvisible(): self
    {
        $this->isVisible = false;

        return $this;
    }

    public function makeUnSearchable(): self
    {
        $this->isSearchable = false;

        return $this;
    }

    public function makeSearchable(): self
    {
        $this->isSearchable = true;

        return $this;
    }

    public function getGroupBy(): array
    {
        return $this->groupBy;
    }

    public function setGroupBy(array $columns): self
    {
        $this->groupBy = $columns;

        return $this;
    }

    public function transformData($value)
    {
        if (!$this->transformDataFunc) {
            return $value;
        }
        return call_user_func($this->transformDataFunc, $value);
    }

    public function hasTransform(): bool
    {
        return (bool) $this->transformDataFunc;
    }

    public function getFilterField(): ?string
    {
        return $this->filterField;
    }

    public function setFilterField(string $field): self
    {
        $this->makeSearchable();
        $this->filterField = $field;
        return $this;
    }

    public function setTransform(callable $transform): self
    {
        $this->transformDataFunc = $transform;
        return $this;
    }

    public function hasJoin(): bool
    {
        return (bool) $this->join;
    }

    public function getJoin(): ?JoinDTO
    {
        return $this->join;
    }

    public function toArray(): array
    {
        return [
            'label' => $this->label,
            'name' => $this->name,
            'type' => $this->type,
            'listName' => $this->listName,
            'isVisible' => $this->isVisible,
            'isSortable' => $this->isSortable,
            'isFilterable' => $this->isFilterable,
            'isSearchable' => $this->isSearchable,
            'filterOperators' => $this->filterOperators,
            '_style' => $this->customStyles,
        ];
    }

    public function getFieldName(): string
    {
        return $this->getFilterField() ?: $this->getDbField() ?: $this->getName();
    }

    public function getFieldNameForSearch(): string
    {
        return $this->getDbField() ?: $this->getName();
    }

    /**
     * @param $param
     * @return bool
     */
    public function validateSearchParamRangeLength($param): bool
    {
        if ($this->setTypeRangeLength()) {
            return $this->typeRangeLength >= $param;
        }

        return true; // accepted all undefined field types
    }

    /**
     * @return bool
     */
    public function setTypeRangeLength(): bool
    {
        switch ($this->initType()) {
            case FieldType::Int:
                $this->typeRangeLength = FieldTypeRangeLength::Int->value;

                return true;
        }

        return false;
    }

    public function setAggregation(Aggregation $aggregation): self
    {
        $this->aggregation = $aggregation;
        return $this;
    }

    public function getAggregationType(): ?Aggregation
    {
        return $this->aggregation;
    }
}
