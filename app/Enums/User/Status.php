<?php

namespace App\Enums\User;

enum Status: int
{
    case Deleted = 0;
    case NewReg = 1;
    case Active = 2;
}
