<?php

namespace App\Enums\BalanceTransaction;

enum Type: int
{
    case Deposit = 1;
    case Install = 2;
    case Click = 3;
}
