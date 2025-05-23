<?php

namespace App\Enums\Payment;

enum Processor: int
{
    case Manual = 1;
    case NowPayments = 2;
}
