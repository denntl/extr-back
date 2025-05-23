<?php

namespace App\Services\Common\DataListing;

use App\Enums\Authorization\PermissionName;
use App\Services\Common\DataListing\DTO\FilterDTO;
use App\Services\Common\DataListing\DTO\OrderDto;
use App\Services\Common\DataListing\Enums\Aggregation;
use App\Services\Common\DataListing\Enums\FilterOperator;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

abstract class CoreListing implements ListingServiceInterface
{
    protected const PER_PAGE = 10;
    protected const WITH_TRASHED = false;
    protected FieldCollector $fieldCollector;
    protected ListingFilterModel $listingModel;

    public function __construct(ListingFilterModel $model)
    {
        $permissions = $this->getPermission();
        if ($permissions && !$model->user->canAny($permissions)) {
            abort(403, 'You do not have permission to access this resource');
        }

        $this->listingModel = $model;
        $this->fieldCollector = $this->getFieldCollector();
    }

    abstract protected function getModel(): Model;
    abstract protected function getFieldCollector(): FieldCollector;
    abstract protected function defaultOrder(): OrderDto;

    /**
     * @return array|PermissionName[]
     */
    abstract protected function getPermission(): array;

    /**
     * @throws Exception
     */
    public function getListingData(): array
    {
        $data = $this->getData();
        $data = $this->transformData($data);
        $count = $this->getCount();

        return [
            'prev' => $this->listingModel->page == 1 ? null : $this->listingModel->page - 1,
            'next' => $this->listingModel->page * static::PER_PAGE < $count ? $this->listingModel->page + 1 : null,
            'page' => $this->listingModel->page,
            'data' => $data,
            'total' => $count,
            'perPage' => static::PER_PAGE,
            'pages' => ceil($count / static::PER_PAGE),
        ];
    }

    /**
     * @return array|FilterDTO[]
     */
    protected function predefinedFilters(): array
    {
        return [];
    }

    protected function defaultFilter(): array
    {
        return [];
    }

    protected function query(): Builder
    {
        $query = static::WITH_TRASHED ? $this->getModel()->query()->withTrashed() : $this->getModel()->query();

        $this->implementScopes($query);

        $this->implementFilters(
            $query,
            array_merge($this->listingModel->filters, $this->predefinedFilters()),
        );
        $this->implementSearchQuery($query, $this->listingModel->searchQuery);

        $groupBy = $this->fieldCollector->getGroupBy();

        if (count($groupBy)) {
            $query->groupBy($groupBy);
        }

        return $query;
    }

    protected function implementScopes(Builder $queryBuilder): void
    {
        $appliedScopes = [];
        foreach ($this->fieldCollector->getScopedFields() as $column) {
            /** @url https://laravel.com/docs/11.x/eloquent#local-scopes */
            if ($column->getScope() && $queryBuilder->hasNamedScope($column->getScope()) && !in_array($column->getScope(), $appliedScopes)) {
                $queryBuilder->{$column->getScope()}($column->getScopeParams());
            }
        }
    }

    /**
     * @param Builder $query
     * @param array|FilterDTO[] $conditions
     * @return Builder
     */
    protected function implementFilters(Builder $query, array $conditions): Builder
    {
        foreach ($conditions as $filter) {
            $field = $this->fieldCollector->getFieldByName($filter->getField());

            if (!$field) {
                continue;
            }

            $field->applyFilter($query, $filter);
        }

        return $query;
    }

    protected function implementSearchQuery(Builder $query, string $searchQuery): Builder
    {
        if (!$searchQuery) {
            return $query;
        }
        $query->where(function ($query) use ($searchQuery) {
            foreach ($this->fieldCollector->getSearchableFields() as $field) {
                $fieldName = $field->getFieldNameForSearch();
                if ($field->isIntType()) {
                    if (is_numeric($searchQuery) && $field->validateSearchParamRangeLength($searchQuery)) {
                        $query->orWhere(DB::raw($fieldName), '=', (int) $searchQuery);
                    }
                } else {
                    $query->orWhere(DB::raw($fieldName), 'like', '%' . $searchQuery . '%');
                }
            }
        });

        return $query;
    }

    private function implementSelect(Builder $query): Builder
    {
        $usedDistinct = false;
        foreach ($this->fieldCollector->getFields() as $name => $field) {
            if (!$field->isEnableToSelect()) {
                continue;
            }

            $distinct = $this->fieldCollector->getDistinct();
            if ($distinct && !$usedDistinct) {
                $query->selectRaw("DISTINCT " . $field->getDbField() . ' as ' . $name);
                $usedDistinct = true;
            } else {
                $query->selectRaw($field->getDbField() . ' as ' . $name);
            }
        }

        return $query;
    }

    private function implementOrder(Builder $query, ListingFilterModel $filters): Builder
    {
        $orderDTO = $filters->order ?? $this->defaultOrder();

        if ($orderDTO && $this->fieldCollector->hasField($orderDTO->field)) {
            $query->orderBy($orderDTO->field, $orderDTO->direction->value);
        }

        return $query;
    }

    /**
     * @throws Exception
     */
    protected function getData(): Collection
    {
        $query = $this->implementSelect($this->query())
            ->offset(($this->listingModel->page - 1) * static::PER_PAGE)
            ->limit(static::PER_PAGE);

        if (static::WITH_TRASHED) {
            $query->withTrashed();
        }

        return $this->implementOrder($query, $this->listingModel)->get();
    }

    protected function transformData(Collection $rows): Collection
    {
        $fields = $this->fieldCollector->getFields();
        return $rows->map(function ($row) use ($fields) {
            $rowAsArray = $row->toArray();
            foreach ($fields as $field) {
                if ($field->hasTransform()) {
                    if (isset($rowAsArray[$field->getName()])) {
                        $rowAsArray[$field->getName()] = $field->transformData($row[$field->getName()]);
                    }
                }
            }
            return $rowAsArray;
        });
    }

    protected function getCount(): int
    {
        $result = $this->query()
            ->selectRaw('count(*) OVER() as count')
            ->first();

        return $result->count ?? 0;
    }

    protected function getListItems(): array
    {
        return [];
    }

    public function getSettings(): array
    {
        return [
            'columns' => array_values($this->fieldCollector->toArray()),
            'listItems' => $this->getListItems(),
            'sorting' => $this->defaultOrder()->toArray(),
            'filters' => (object) $this->defaultFilter(),
        ];
    }

    protected function getAggregationData(): array
    {
        $query = $this->query();
        $query->selectRaw('DISTINCT ' . $this->getModel()->getTable() . '.' . $this->getModel()->getKeyName());
        $filterQuery = '(' . $query->toRawSql() . ') filter_table';

        $query = static::WITH_TRASHED ? $this->getModel()->query()->withTrashed() : $this->getModel()->query();
        $aggregations = [];
        $usedDistinct = false;
        foreach ($this->fieldCollector->getFields() as $key => $field) {
            if (!$field->isEnableToSelect()) {
                continue;
            }
            $aggregations[$key]['name'] = $field->getName();
            $aggregations[$key]['label'] = '';
            $select = null;
            switch ($field->getAggregationType()) {
                case Aggregation::Sum:
                    $select = "sum(" . $field->getFieldName() . ") as " . $field->getName();
                    break;
            }
            if ($select) {
                $distinct = $this->fieldCollector->getDistinct();
                if ($distinct && !$usedDistinct) {
                    $query->selectRaw("DISTINCT " . $select);
                    $usedDistinct = true;
                } else {
                    $query->selectRaw($select);
                }
            }
        }
        $query->leftJoin(
            DB::raw($filterQuery),
            $this->getModel()->getTable() . '.' . $this->getModel()->getKeyName(),
            '=',
            'filter_table.' . $this->getModel()->getKeyName()
        );
        $query->whereNotNull('filter_table.' . $this->getModel()->getKeyName());
        $aggregatedFields = $query->first()->toArray();
        foreach ($aggregatedFields as $key => $aggregatedField) {
            $aggregations[$key]['label'] = $aggregatedField;
        }

        return $aggregations;
    }

    protected function decorateAggregations(array $data): array
    {
        return $data;
    }

    public function getAggregations(): array
    {
        $data = $this->getAggregationData();

        return array_values($this->decorateAggregations($data));
    }
}
