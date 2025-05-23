<?php

namespace App\Services\Client\Telegraph\Messages;

use App\Models\TelegraphChat;
use DefStudio\Telegraph\Telegraph;

interface MessageManagerInterface
{
    public function generateMessage(TelegraphChat $chat): Telegraph;
}
