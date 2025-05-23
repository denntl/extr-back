<?php

namespace App\Services\Common\DataListing;

use App\Models\User;
use App\Services\Common\DataListing\DTO\FilterDTO;
use App\Services\Common\DataListing\DTO\OrderDto;
use App\Services\Common\DataListing\Enums\FilterOperator;
use App\Services\Common\DataListing\Enums\SortingType;
use Illuminate\Http\Request;

class ListingFilterModel
{
    public int $page = 1;
//    public ?int $companyId = null;
    public User $user;
    public string $searchQuery = '';
    public array $filters = [];
    public ?OrderDto $order = null;

    /**
     * @param Request $request
     * @throws \Exception
     */
    public function __construct(Request $request)
    {
        $validationRules = [];
        if ($request->has('page')) {
            $this->page = (int) $request->input('page');
            $validationRules['page'] = 'required|integer|min:0';
        }
        if ($request->input('query')) {
            $this->searchQuery = $request->input('query');
            $validationRules['query'] = 'required|string';
        }
        if ($request->input('filters')) {
            $filters = $request->input('filters');
            $validationRules['filters'] = 'required|array';
            $validationRules['filters.*.name'] = 'required|string';
            $validationRules['filters.*.operator'] = 'required|string';
            foreach ($filters as $filter) {
                if (
                    !isset($filter['name'], $filter['operator']) &&
                    (!isset($filter['value']) && !in_array($filter['operator'], [FilterOperator::Empty, FilterOperator::NotEmpty]))
                ) {
                    continue;
                }
                $this->filters[] = new FilterDTO($filter['name'], $filter['operator'], $filter['value']);
            }
        }

        if ($request->has('sorting.column')) {
            $column = $request->input('sorting.column');
            $state = $request->input('sorting.state');
            $validationRules['sorting.column'] = 'required|string';
            $validationRules['sorting.state'] = 'required|string';
            $state = match ($state) {
                SortingType::Desc->value => SortingType::Desc,
                default => SortingType::Asc,
            };
            $this->order = new OrderDto($column, $state);
        }

        if (count($validationRules)) {
            $request->validate($validationRules);
        }

        $this->user = $request->user();
    }
}
