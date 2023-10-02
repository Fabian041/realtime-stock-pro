<?php

namespace App\Jobs;

use Pusher\Pusher;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class WebSocketPushJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    
    public $area;
    public $dataMaterial;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($area, $dataMaterial)
    {
        $this->area = $area;
        $this->dataMaterial = $dataMaterial;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // connection to pusher
        $options = array(
            'cluster' => 'ap1',
            'encrypted' => true
        );

        $pusher = new Pusher(
            '31df202f78fc0dace852',
            'f1d1fd7c838cdd9f25d6',
            '1567188',
            $options
        );

        // sending data
        $result = $pusher->trigger('stock-' . $this->area , 'StockDataUpdated', $this->dataMaterial);

        return $result;
    }
}
