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

    public $ckd;
    public $import;
    public $local;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($ckd,$import,$local)
    {
        $this->ckd = $ckd;
        $this->import = $import;
        $this->local = $local;
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
