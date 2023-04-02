<?php

namespace App\Http\Controllers;

use Pusher\Pusher;
use App\Models\TtDc;
use App\Models\TtMa;
use App\Models\Stock;
use App\Models\TmBom;
use App\Models\TmArea;
use App\Models\TmPart;
use App\Models\TtAssy;
use App\Models\TtStock;
use App\Models\TtOutput;
use App\Models\TtMaterial;
use Illuminate\Http\Request;
use App\Models\TmTransaction;
use App\Events\StockDataUpdated;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    public function stock_control($line , $code)
    {
        //ex LINE = MA001
        //ex CODE will be generate as part number or back number in avicenna, so code will be part number or back 
        
        // (i think we need authenticated avicenna username / npk)

        // get id part based on part number or back number
        $part = TmPart::select('id')->where('part_number', $code)->first();

        // get id area based on lihe
        $area = TmArea::select('id')->where('name', 'like', '%' . $line . '%')->first();

        //search bom of the part number based on line in tm bom table
        $boms = TmBom::where('id_area', $area->id)
                ->where('id_part', $part->id)
                ->get();

        // get id transaction
        $transaction = TmTransaction::select('id')->where('name', 'Traceability')->first();
        $reversalTransaction= TmTransaction::select('id')->where('name', 'Traceability (R)')->first();

        try {

            DB::beginTransaction();
            // material transaction
            foreach($boms as $bom){

                // it will decrease current material stock and 
                //increase FG / WIP stock in spesific area
                TtMaterial::create([
                    'id_material' => $bom->id,
                    'qty' => $bom->qty_use,
                    'id_area' => $area->id,
                    'id_transaction' => $reversalTransaction->id,
                    'pic' => 'avicenna user',
                    'date' => date('Y-m-d H:i:s')
                ]);
            }

            // FG / WIP transaction
            $dcModel = 'TtDC';
            $maModel = 'TtMa';
            $assyModel = 'TtAssy';

            function partTransaction($area, $part, $transaction, $qty){
                $area->create([
                    'id_part' => $part,
                    'id_transaction' => $transaction,
                    'pic' => 'avicenna user',
                    'date' => date('Y-m-d H:i:s'),
                    'qty' => $qty
                ]);
            }

            if($line == 'DC'){

                partTransaction($dcModel, $part->id, $transaction->id, 1);

            }elseif($line == 'MA'){

                // increase ma stock
                partTransaction($maModel, $part->id, $transaction->id, 1);

                // decrease dc stock
                partTransaction($dcModel, $part->id, $reversalTransaction->id, 1);

            }elseif($line == 'AS'){

                // increase assy stock
                partTransaction($assyModel, $part->id, $transaction->id, 1);

                // decrease ma stock
                partTransaction($maModel, $part->id, $reversalTransaction->id, 1);
            }
            
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

            // sending stock data all items
            $pusher->trigger('stock-data', 'StockDataUpdated', []);

            return response()->json([
                'message' => 'success'
            ],200);

        } catch (\Throwable $e) {

            return response()->json([
                'message' => $e->getMessage(),
            ],$e->getCode());

        }
    }
}
