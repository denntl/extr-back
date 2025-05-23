<?php

namespace App\Enums\Site;

enum Landings: string
{
    case AndroidNew = 'new';
    case AndroidOld = 'old';
    case Ios = 'ios';
    case WhiteDefault = 'white-default';
}
