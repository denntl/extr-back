<?php

namespace App\Services\Common\DataListing\Enums;

enum DateFilterOption: string
{
    case Today = 'today';
    case Yesterday = 'yesterday';
    case ThisWeek = 'this_week';
    case ThisMonth = 'this_month';
    case NextWeek = 'next_week';
    case NextMonth = 'next_month';
}
