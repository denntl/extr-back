<?php

namespace App\Enums\BalanceTransaction;

enum Status: int
{
    case Pending = 1;
    case Approved = 2;
    case Declined = 3;
    case Canceled = 4;
}
