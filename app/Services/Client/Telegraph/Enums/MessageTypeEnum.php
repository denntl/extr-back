<?php

namespace App\Services\Client\Telegraph\Enums;

enum MessageTypeEnum: string
{
    case Message = 'message';
    case HTML = 'html';
    case Markdown = 'markdown';
}
