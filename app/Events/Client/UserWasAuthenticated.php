<?php

namespace App\Events\Client;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserWasAuthenticated
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public string $eventTime;
    public ?string $ip;

    /**
     * Create a new event instance.
     */
    public function __construct(public User $user)
    {
        $this->eventTime = now()->toDateTimeString();
        $this->ip = request()->ip();
    }
}
