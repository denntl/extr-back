<?php

namespace App\Services\Client\Telegraph;

use App\Models\TelegraphChat;
use App\Services\Client\Telegraph\Messages\MessageManagerInterface;

class MessageSender
{
    /**
     * @param TelegraphChat $telegraphChat
     * @param MessageManagerInterface|string $message
     * @return void
     */
    public function send(TelegraphChat $telegraphChat, MessageManagerInterface|string $message): void
    {
        if (is_string($message)) {
            $telegraphChat->message($message)->send();
            return;
        }

        $message->generateMessage($telegraphChat)->send();
    }
}
