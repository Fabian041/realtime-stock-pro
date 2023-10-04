<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Pusher\Pusher;
use App\Models\TtDc;
use App\Models\TtMa;
use App\Models\Stock;
use App\Models\TmBom;
use App\Models\TmArea;
use App\Models\TmPart;
use App\Models\TtAssy;
use App\Models\DcStock;
use App\Models\MaStock;
use App\Models\TtStock;
use App\Models\TtOutput;
use App\Models\AssyStock;
use App\Models\TtMaterial;
use Illuminate\Http\Request;
use App\Models\MaterialStock;
use App\Models\TmTransaction;
use App\Jobs\WebSocketPushJob;
use App\Events\StockDataUpdated;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    public function pushData($area, $dataMaterial){
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
        $result = $pusher->trigger('stock-' . $area , 'StockDataUpdated', $dataMaterial);

        return $result;
    }

    public function queryCurrentMaterialStock($area,$source){
        
        $result = DB::table('material_stocks')
        ->join('tm_materials', 'tm_materials.id', '=', 'material_stocks.id_material')
        ->select(DB::raw('SUM(current_stock) as current_stock'))
        ->where('id_area', $area)
        ->where('tm_materials.source', 'like', '%' . $source . '%')
        ->first();
        
        return $result;
    }

    public function getCurrentMaterialStock($area){

        // source
        $ckd = 'CKD';
        $import = 'IMPORT';
        $local = 'LOCAL';

        $dataCkd = $this->queryCurrentMaterialStock($area,$ckd);
        $dataImport = $this->queryCurrentMaterialStock($area, $import);
        $dataLocal = $this->queryCurrentMaterialStock($area, $local);

        $dataCkd = ($dataCkd) ? $dataCkd->current_stock : 0;
        $dataImport = ($dataImport) ? $dataImport->current_stock : 0;
        $dataLocal = ($dataLocal) ? $dataLocal->current_stock : 0;

        return [$dataCkd,$dataImport,$dataLocal];
    }
    
    public function stock_control($line , $code, $qty, $codepart = null)
    {
        //ex LINE = MA001
        //ex CODE will be generate as back number in avicenna, so code will be part number or back 
        
        // (i think we need authenticated avicenna username / npk)

        // get id part based on part number or back number
        $part = TmPart::select('id')->where('back_number', $code)->first();
        if(!$part){
            return response()->json([
                'status' => 'error',
                'message' => 'Back number does not exist!'
            ], 404);
        }

        // get id area based on lihe
        $area = TmArea::select('id')->where('name', 'LIKE', '%' . $line . '%')->first();
        if(!$area){
            return response()->json([
                'status' => 'error',
                'message' => 'Area does not exist!'
            ], 404);
        }

        if($line !== 'PULL'){
            //search bom of the part number based on line in tm bom table
            $boms = TmBom::where('id_area', $area->id)
                    ->where('id_part', $part->id)
                    ->get();
                    
                    if($boms->first() == null){
                        return response()->json([
                            'status' => 'error',
                            'message' => 'BOM does not exist!'
                        ], 404);
                    }
        }

        // get id area
        $wh = TmArea::select('id')->where('name', 'Warehouse')->first();

        // get id transaction
        $transaction = TmTransaction::select('id')->where('name', 'Traceability')->first();
        $reversalTransaction= TmTransaction::select('id')->where('name', 'Traceability (R)')->first();

        // FG / WIP transaction
        $dcModel = new TtDc();
        $maModel = new TtMa();
        $assyModel = new TtAssy();

        try {

            DB::beginTransaction();
            // material transaction

            if($line !== 'PULL'){
                foreach($boms as $bom){
    
                    // it will decrease current material stock and 
                    //increase FG / WIP stock in spesific area
                    TtMaterial::create([
                        'id_material' => $bom->id_material,
                        'qty' => $bom->qty_use,
                        'id_area' => $wh->id,
                        'id_transaction' => $reversalTransaction->id,
                        'pic' => 'avicenna user',
                        'date' => Carbon::now()->format('Y-m-d H:i:s')
                    ]);
    
                    // insert to BOM table
                    TtOutput::create([
                        'id_bom' => $bom->id,
                        'date' => Carbon::now()->format('Y-m-d H:i:s')
                    ]);
    
                    // get current stock after scan
                    $result = $this->getCurrentMaterialStock($wh->id);
    
                    // push to websocket
                    // $this->pushData('wh',$result);
                    WebSocketPushJob::dispatch('wh', $result);
    
                DB::commit();
                }
            }

            function partTransaction($area, $part, $transaction, $qty, $codepart = null){
                $result = $area->create([
                    'code' => $codepart,
                    'id_part' => $part,
                    'id_transaction' => $transaction,
                    'pic' => 'avicenna user',
                    'date' => Carbon::now()->format('Y-m-d H:i:s'),
                    'qty' => $qty
                ]);

                return $result;
            }

            if($line == 'DC'){

                partTransaction($dcModel, $part->id, $transaction->id, $qty, $codepart);

            }elseif($line == 'MA'){

                // increase ma stock
                partTransaction($maModel, $part->id, $transaction->id, $qty, $codepart);

                // decrease dc stock
                partTransaction($dcModel, $part->id, $reversalTransaction->id, $qty, $codepart);

            }elseif($line == 'AS'){

                // increase assy stock
                partTransaction($assyModel, $part->id, $transaction->id, $qty, $codepart);

                // decrease ma stock
                partTransaction($maModel, $part->id, $reversalTransaction->id, $qty, $codepart);

            }elseif($line == 'PULL'){

                $reversalTransaction= TmTransaction::select('id')->where('name', 'Pulling Delivery (R)')->first();
                
                if($code == 'DI01' || $code == 'DI02') {
                    // decrease dc stock
                    partTransaction($dcModel, $part->id, $reversalTransaction->id, $qty, $codepart);
                    
                }else if($code == 'EI11' || $code == 'EI12' || $code == 'EI13' || $code == 'EI14' ){
                    // decrease ma stock
                    partTransaction($maModel, $part->id, $reversalTransaction->id, $qty, $codepart);
                }else{
                    // decrease assy stock
                    partTransaction($assyModel, $part->id, $reversalTransaction->id, $qty, $codepart);
                }
                DB::commit();
            }
            
            // get current dc stock
            function getWipDc($model){
                $result = DB::table('dc_stocks')
                        ->join('tm_parts', 'tm_parts.id', '=', 'dc_stocks.id_part')
                        ->select(DB::raw('SUM(current_stock) as current_stock'))
                        ->where('tm_parts.status', '<>' ,0)
                        ->where('tm_parts.part_name', 'LIKE', '%' . $model . '%')
                        ->first();
    
                return $result;
            }

            // get current dc stock
            $tcc = (getWipDc('TCC')) ? getWipDc('TCC')->current_stock : 0;
            $opn = (getWipDc('OPN')) ? getWipDc('OPN')->current_stock : 0;

            // wip data
            $wipData = [$tcc,$opn];

            // push to websocket
            // Dispatch the WebSocket push job to the queue 
            WebSocketPushJob::dispatch('wip', $wipData);

            return response()->json([
                'message' => 'success',
                'data' =>  [
                    'line' => $line,
                    'back_number' => $code,
                    'quantity' => $qty
                ]
            ],200);

            DB::commit();
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ],500);

        }
    }

    public function ng_part($line, $code)
    {
        $part = TmPart::select('id')->where('back_number', $code)->first();

        // get id area based on lihe
        $area = TmArea::select('id')->where('name', 'LIKE', '%' . $line . '%')->first();

        //search bom of the part number based on line in tm bom table
        $boms = TmBom::where('id_area', $area->id)
                ->where('id_part', $part->id)
                ->get();

        // get id area
        $wh = TmArea::select('id')->where('name', 'Warehouse')->first();

        // get id transaction
        $transaction= TmTransaction::select('id')->where('name', 'NG Judgement')->first();
        $reversalTransaction= TmTransaction::select('id')->where('name', 'NG Judgement (R)')->first();

        // FG / WIP transaction
        $dcModel = new TtDc();
        $maModel = new TtMa();
        $assyModel = new TtAssy();

        function ngPartTransaction($area, $part, $reversalTransaction){
            $result = $area->create([
                'id_part' => $part,
                'id_transaction' => $reversalTransaction,
                'pic' => 'avicenna user',
                'date' => Carbon::now()->format('Y-m-d H:i:s'),
                'qty' => 1
            ]);

            return $result;
        }

        function getIngot($part){
            $result = DB::table('tm_boms')
                        ->join('tm_materials', 'tm_material.id', '=', 'tm_boms.id_material')
                        ->select('tm_boms.qty_use', 'tm_materials.id')
                        ->where('tm_boms.id_part', $part)
                        ->where('tm_material.source', 'LIKE', '%R/M%')
                        ->first();

            return $result;
        }

        try {
            DB::beginTransaction();

            if($line == 'DC'){
                // do nothing (component still same && stock dc stll same)
            }else if($line == 'MA'){

                // decrease dc stock
                ngPartTransaction($dcModel, $part->id, $reversalTransaction->id);

                // get ingot from spesific part bom
                $ingot = getIngot($part->id);
                
                // increase ingot stock
                TtMaterial::create([
                    'id_material' => $ingot->id,
                    'qty' => $ingot->qty_used,
                    'id_area' => $area->id,
                    'id_transaction' => $transaction->id,
                    'pic' => 'avicenna user',
                    'date' => Carbon::now()->format('Y-m-d H:i:s')
                ]);

            }else if($line == 'AS'){

                // decrease MA stock
                ngPartTransaction($maModel, $part->id, $reversalTransaction->id);

                // get ingot from spesific part bom
                $ingot = getIngot($part->id);

                // increase ingot stock
                TtMaterial::create([
                    'id_material' => $ingot->id,
                    'qty' => $ingot->qty_used,
                    'id_area' => $area->id,
                    'id_transaction' => $transaction->id,
                    'pic' => 'avicenna user',
                    'date' => Carbon::now()->format('Y-m-d H:i:s')
                ]);
            }

            return response()->json([
                'message' => 'success',
                'data' =>  [
                    'line' => $line,
                    'back_number' => $code,
                ]
            ],200);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ],500);
        }
    }

    public function stockBalancing()
    {
        return view('layouts.stockBalancing',[
            'areas' => TmArea::all(),
        ]);
    }

    public function getBackNumber(Request $request)
    {
        $area = $request->area;

        if($area == 'DC'){
            $part = TmPart::select('id','back_number')->where('status', 0)->get();
        }else if($area == 'MA'){
            $part = TmPart::select('id','back_number')->where('status', 1)->get();
        }else{
            $part = TmPart::select('id','back_number')->where('status', 2)->get();
        }

        return $part;
    }
    
    public function getCurrentStock(Request $request)
    {
        $area = $request->area;
        $back_number = $request->backNumber;

        // initialize model 
        $dcModel = new DcStock();
        $maModel = new MaStock();
        $assyModel = new AssyStock();
        
        if($area == 'DC'){
            $currentStock = $dcModel->select('current_stock')->where('id_part', $back_number)->first();
        }else if($area == 'MA'){
            $currentStock = $maModel->select('current_stock')->where('id_part', $back_number)->first();
        }else{
            $currentStock = $assyModel->select('current_stock')->where('id_part', $back_number)->first();
        }

        return $currentStock;
    }

    public function adjustStock(Request $request)
    {
        $actual = $request->actual_stock;
        $area = $request->area;
        $back_number = $request->back_number;

        // initialize model 
        $dcModel = new DcStock();
        $maModel = new MaStock();
        $assyModel = new AssyStock();
        
        
        if($area == 'DC'){
            try {
                DB::beginTransaction();
                $currentStock = $dcModel->where('id_part', $back_number)->update([
                    'current_stock' => $actual
                ]);
                DB::commit();
            } catch (\Throwable $th) {
                DB::rollBack();
                return [
                    'error' => $th->getMessage(),
                ];
            }
            
        }else if($area == 'MA'){
            $currentStock = $maModel->where('id_part', $back_number)->update([
                'current_stock' => $actual
            ]);
        }else{
            $currentStock = $assyModel->where('id_part', $back_number)->update([
                'current_stock' => $actual
            ]);
        }

        return redirect()->back()->with('success', 'Stock updated successfully');
    }
}