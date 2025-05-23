<?php

namespace App\Services\Common\DataListing\Enums;

enum FilterOperator: string
{
    case Equal = 'eq';
    case NotEqual = 'neq';
    case Contains = 'contains';
    case NotContains = 'not_contains';
    case GreaterThanOrEqual = 'gte';
    case LessThanOrEqual = 'lte';
    case Between = 'between';
    case In = 'in';
    case NotIn = 'not_in';
    case Empty = 'empty';
    case NotEmpty = 'not_empty';
}
