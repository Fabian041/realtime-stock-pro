<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\DcStock;
use App\Models\MaStock;
use App\Models\AssyStock;
use App\Models\PeriodStock;
use Illuminate\Console\Command;

class CaptureStockData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'capture:stock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Capture stock data';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->processStockArea(DcStock::class, 2);
        $this->processStockArea(MaStock::class, 3);
        $this->processStockArea(AssyStock::class, 4);
    }

    protected function processStockArea($modelClass, $areaId)
    {
        $stocks = $modelClass::all();
        foreach ($stocks as $stock) {
            PeriodStock::create([
                'id_area' => $areaId,
                'id_part' => $stock->id_part,
                'current_stock' => $stock->current_stock,
                'captured_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ]);
        }
    }
}
