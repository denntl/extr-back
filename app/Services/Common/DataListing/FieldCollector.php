<?php

namespace App\Services\Common\DataListing;

use App\Services\Common\DataListing\DTO\JoinDTO;
use App\Services\Common\DataListing\Enums\FieldType;
use App\Services\Common\DataListing\Fields\FieldInterface;

class FieldCollector
{
    /** @var array| FieldInterface[] */
    protected array $fields = [];

    /**
     * @var array| JoinDTO[]
     */
    protected array $joins = [];

    protected bool $distinct = false;

    public static function init(): self
    {
        return new self();
    }

    /**
     * @param array|FieldInterface[] $fields
     * @return FieldCollector
     */
    public function setBatch(array $fields): self
    {
        foreach ($fields as $field) {
            $this->set($field);
        }

        return $this;
    }

    public function set(FieldInterface $field): self
    {
        $this->fields[$field->getName()] = $field;

        return $this;
    }

    public function getFieldByName(string $name): ?FieldInterface
    {
        return $this->fields[$name] ?? null;
    }

    public function hasField(string $name): bool
    {
        return (bool) $this->getFieldByName($name);
    }

    /**
     * @return array|FieldInterface[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    public function getSelect($tableName): array
    {
        $selectArray = [];
        foreach ($this->fields as $name => $field) {
            if ($field->isEnableToSelect()) {
                continue;
            }

            $field->getDbField();
            $column = $field->getDbField();
            if ($column) {
                $selectArray[] = "$column as $name";
            } else {
                $column = $field->getName();
                $selectArray[] = "$tableName.$column as $name";
            }
        }

        return $selectArray;
    }

    public function getGroupBy(): array
    {
        $groupBy = [];
        foreach ($this->fields as $field) {
            if ($field->getType() === FieldType::Action) {
                continue;
            }
            foreach ($field->getGroupBy() as $column) {
                $groupBy[] = $column;
            }
        }

        return $groupBy;
    }

    public function getAggregationFields(): array
    {
        $withAggregationFields = [];
        foreach ($this->fields as $field) {
            if ($field->getAggregationType()) {
                $withAggregationFields[] = $field;
            }
        }
        return $withAggregationFields;
    }



    public function addJoin(JoinDTO $joinDTO): self
    {
        $this->joins[] = $joinDTO;
        return $this;
    }

    /**
     * @return array|JoinDTO[]
     */
    public function getJoins(): array
    {
        return $this->joins;
    }

    /**
     * @return array|FieldInterface[]
     */
    public function getSearchableFields(): array
    {
        $searchableFields = [];
        foreach ($this->fields as $field) {
            if ($field->isSearchable()) {
                $searchableFields[] = $field;
            }
        }

        return $searchableFields;
    }

    /**
     * @return array|FieldInterface[]
     */
    public function getScopedFields(): array
    {
        $withScopeFields = [];
        foreach ($this->fields as $field) {
            if ($field->getScope()) {
                $withScopeFields[] = $field;
            }
        }

        return $withScopeFields;
    }

    public function toArray(): array
    {
        $fields = [];

        foreach ($this->fields as $field) {
            $fields[$field->getName()] = $field->toArray();
        }

        return $fields;
    }

    public function getDistinct(): bool
    {
        return $this->distinct;
    }

    public function distinct(): static
    {
        $this->distinct = true;
        return $this;
    }
}
