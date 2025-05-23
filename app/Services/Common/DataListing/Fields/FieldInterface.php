<?php

namespace App\Services\Common\DataListing\Fields;

use App\Services\Common\DataListing\DTO\FilterDTO;
use App\Services\Common\DataListing\DTO\JoinDTO;
use App\Services\Common\DataListing\Enums\Aggregation;
use App\Services\Common\DataListing\Enums\FieldType;
use Illuminate\Database\Eloquent\Builder;

interface FieldInterface
{
    public function withScope(string $scope, mixed $params = null): self;
    public function getScope(): string;
    public function getScopeParams(): mixed;
    public function getName(): string;
    public function isListType(): bool;
    public function isIntType(): bool;
    public function isSearchable(): bool;
    public function setListName(string $listName): self;
    public function setCustomStyles(array $styles): self;
    public function getType(): FieldType;
    public function getDbField(): ?string;
    public function setFilterField(string $field): self;
    public function getFilterField(): ?string;
    public function getFieldName(): string;
    public function makeUnSortable(): self;
    public function hideInSelect(): self;
    public function isEnableToSelect(): bool;
    public function makeInvisible(): self;
    public function hideInFilterSelect(): self;
    public function makeUnSearchable(): self;
    public function makeSearchable(): self;
    public function getGroupBy(): array;
    public function setGroupBy(array $columns): self;
    public function setTransform(callable $transform): self;
    public function applyFilter(Builder $query, FilterDTO $filter): Builder;
    public function hasJoin(): bool;
    public function getJoin(): ?JoinDTO;
    /**
     * @param mixed $value
     * @return mixed
     */
    public function transformData($value);

    public function hasTransform(): bool;

    public function toArray(): array;

    public function validateSearchParamRangeLength($param): bool;
    public function getFieldNameForSearch(): string;
    public function setAggregation(Aggregation $aggregation): self;
    public function getAggregationType(): ?Aggregation;
}
