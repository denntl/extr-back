<?php

namespace App\Services\Common\DataListing\Enums;

enum FieldType: string
{
    case String = 'string';
    case Int = 'int';
    case Float = 'float';
    case Bool = 'bool';
    case Date = 'date';
    case Datetime = 'datetime';
    case List = 'list';
    case ListAsString = 'array_list';
    case Action = 'action';
    case Percent = 'percent';
}
