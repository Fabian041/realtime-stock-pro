<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StockDataUpdated implements shouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $updatedData;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($updatedData)
    {
        $this->updatedData = $updatedData;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('stock-data');
    }

}
