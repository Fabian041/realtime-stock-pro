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
        return [
            // material
            new Channel('stock-wh'),
            new Channel('stock-oh'),
            new Channel('stock-dc'),
            new Channel('stock-ma'),
            new Channel('stock-assy'),

            // part
            new Channel('stock-wip'),

            new Channel('part-dc'),
            new Channel('part-ma'),
            new Channel('part-assy')
        ];
    }

}