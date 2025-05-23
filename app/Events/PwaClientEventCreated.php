<?php

namespace App\Events;

use App\Models\Application;
use App\Models\PwaClientEvent;
use Carbon\Carbon;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PwaClientEventCreated
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public string $date;

    /**
     * Create a new event instance.
     */
    public function __construct(public PwaClientEvent $pwaClientEvent)
    {
        $this->date = Carbon::now()->toDateString();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
