<?php

namespace App\Services\Common\DataListing\DTO;

use App\Services\Common\DataListing\Enums\SortingType;

class OrderDto
{
    public string $field;
    public SortingType $direction;

    public function __construct(string $field, SortingType $direction)
    {
        $this->field = $field;
        $this->direction = $direction;
    }

    public function mapDirection(): int
    {
        throw new \Exception('Not implemented');
        $sortingMapping = [
            SortingType::Asc->value => SORT_ASC,
            SortingType::Desc->value => SORT_DESC,
        ];

        return $sortingMapping[$this->direction] ?? SORT_ASC;
    }

    public function toArray(): array
    {
        return [
            'column' => $this->field,
            'state' => $this->direction === SortingType::Asc ? 'asc' : 'desc',
        ];
    }
}
