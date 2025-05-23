<?php

namespace App\Services\Common\DataListing\Enums;

enum Aggregation: string
{
    case Sum = 'sum';
    case Count = 'count';
    case Avg = 'avg';
    case Max = 'max';
    case Min = 'min';
}
