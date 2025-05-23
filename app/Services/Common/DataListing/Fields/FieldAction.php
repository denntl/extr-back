<?php

namespace App\Services\Common\DataListing\Fields;

use App\Services\Common\DataListing\Enums\FieldType;

class FieldAction extends BaseField
{
    protected bool $isFilterable = false;
    protected bool $isSortable = false;
    protected bool $isSearchable = false;
    protected bool $enableToSelect = false;

    protected function initType(): FieldType
    {
        return FieldType::Action;
    }
}
