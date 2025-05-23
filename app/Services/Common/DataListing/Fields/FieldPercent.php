<?php

namespace App\Services\Common\DataListing\Fields;

use App\Services\Common\DataListing\DTO\FilterDTO;
use App\Services\Common\DataListing\Enums\FieldType;
use Illuminate\Database\Eloquent\Builder;

class FieldPercent extends FieldInt
{
    protected function initType(): FieldType
    {
        return FieldType::Percent;
    }

    /**
     * @throws \Exception
     */
    public function applyFilter(Builder $query, FilterDTO $filter): Builder
    {
        $newFilter = new FilterDTO($filter->getField(), $filter->getOperator(), $filter->getValue() / 100);
        return parent::applyFilter($query, $newFilter);
    }
}
