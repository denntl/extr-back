<?php

namespace App\Services\Client\Telegraph\Messages;

use App\Models\TelegraphChat;
use App\Services\Client\Telegraph\Enums\MessageTypeEnum;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;
use DefStudio\Telegraph\Telegraph;

class MessageWithActions implements MessageManagerInterface
{
    public array $actions = [];

    /**
     * @param string $text
     * @param string|null $type
     * @param Button ...$actions
     */
    public function __construct(
        public string $text,
        public ?string $type = 'message',
        Button ...$actions
    ) {
        $this->actions = $actions;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['text'],
            $data['type'] ?? null,
            ...$data['actions']
        );
    }

    private function messageType($chat, $type): Telegraph
    {
        return match ($type) {
            MessageTypeEnum::Markdown => $chat
                ->markdown($this->text),
            MessageTypeEnum::HTML => $chat
                ->html($this->text),
            default => $chat
                ->message($this->text),
        };
    }

    public function generateMessage(TelegraphChat $chat): Telegraph
    {
        $message = $this->messageType($chat, $this->type);

        return $message
            ->keyboard(Keyboard::make()->buttons($this->actions));
    }
}
