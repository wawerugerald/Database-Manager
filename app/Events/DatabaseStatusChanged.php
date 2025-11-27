<?php

namespace App\Events;

use App\Models\DatabaseInstance;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class DatabaseStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $instance;

    public function __construct(DatabaseInstance $instance)
    {
        $this->instance = $instance;
    }

    public function broadcastOn()
    {
        return new Channel('db-status');
    }

    public function broadcastAs()
    {
        return 'DatabaseStatusChanged';
    }
}
