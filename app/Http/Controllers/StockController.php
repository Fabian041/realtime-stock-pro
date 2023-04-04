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
use App\Models\MaterialStock;
use App\Models\TmTransaction;
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

        // get id area
        $oh = TmArea::select('id')->where('name', 'OH Store')->first();
        $wh = TmArea::select('id')->where('name', 'Warehouse')->first();

        // get id transaction
        $transaction = TmTransaction::select('id')->where('name', 'Traceability')->first();
        $reversalTransaction= TmTransaction::select('id')->where('name', 'Traceability (R)')->first();

        // FG / WIP transaction
        $dcModel = 'TtDC';
        $maModel = 'TtMa';
        $assyModel = 'TtAssy';

        // area
        $dc = 'DC';
        $ma = 'MA';
        $assy = 'ASSY';
        $warehouse = 'Warehouse';
        $ohStore = 'OH Store';

        // source
        $ckd = 'CKD';
        $import = 'IMPORT';
        $local = 'LOCAL';

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

                // insert to BOM table
                TtOutput::create([
                    'id_bom' => $bom->id,
                    'date' => date('Y-m-d H:i:s')
                ]);
            }

            function partTransaction($area, $part, $transaction, $qty){
                $result = $area->create([
                    'id_part' => $part,
                    'id_transaction' => $transaction,
                    'pic' => 'avicenna user',
                    'date' => date('Y-m-d H:i:s'),
                    'qty' => $qty
                ]);

                return $result;
            }

            if($line == 'DC'){

                partTransaction($dcModel, $part->id, $transaction->id, 1);

                // get current stock after scan
                $result = $this->getCurrentMaterialStock($area->id);

                // push to websocket
                $this->pushData('dc',$result);

            }elseif($line == 'MA'){

                // increase ma stock
                partTransaction($maModel, $part->id, $transaction->id, 1);

                // decrease dc stock
                partTransaction($dcModel, $part->id, $reversalTransaction->id, 1);

                // get current stock after scan
                $result = $this->getCurrentMaterialStock($area->id);

                 // push to websocket
                $this->pushData('ma',$result);

            }elseif($line == 'AS'){

                // increase assy stock
                partTransaction($assyModel, $part->id, $transaction->id, 1);

                // decrease ma stock
                partTransaction($maModel, $part->id, $reversalTransaction->id, 1);

                // get current stock after scan
                $result = $this->getCurrentMaterialStock($area->id);

                 // push to websocket
                $this->pushData('ma',$result);
            }

            function getMaterialArea($line,$source){
                DB::table('material_stocks')
                    ->join('tm_materials', 'tm_materials.id', '=', 'material_stocks.id_material')
                    ->join('tm_areas', 'tm_areas.id', '=', 'material_stocks.id_area')
                    ->where('tm_areas.name', $line)
                    ->where('tm_materials.source', 'like', '%' . $source . '%')
                    ->get();
            }

            // stock material DC area
            $dcCkd = getMaterialArea($dc,$ckd);
            $dcImport = getMaterialArea($dc, $import);
            $dcLocal = getMaterialArea($dc, $local);

            // stock material MA
            $maCkd = getMaterialArea($ma,$ckd);
            $maImport = getMaterialArea($ma, $import);
            $maLocal = getMaterialArea($ma, $local);

            // stock material Assy
            $assyCkd = getMaterialArea($assy,$ckd);
            $assyImport = getMaterialArea($assy, $import);
            $assyLocal = getMaterialArea($assy, $local);

            // stock material WH
            $warehouseCkd = getMaterialArea($warehouse,$ckd);
            $warehouseImport = getMaterialArea($warehouse, $import);
            $warehouseLocal = getMaterialArea($warehouse, $local);

            // stock material WH
            $ohStoreCkd = getMaterialArea($ohStore,$ckd);
            $ohStoreImport = getMaterialArea($ohStore, $import);
            $ohStoreLocal = getMaterialArea($ohStore, $local);

            $dcStock = [$dcCkd,$dcImport, $dcLocal];
            $maStock = [$maCkd,$maImport, $maLocal];
            $assyStock = [$assyCkd,$assyImport, $assyLocal];
            $warehouseStock = [$warehouseCkd,$warehouseImport, $warehouseLocal];
            $ohStoreStock = [$ohStoreCkd,$ohStoreImport, $ohStoreLocal];

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
            $pusher->trigger('stock-data', 'StockDataUpdated', [$dcStock, $maStock, $assyStock, $warehouseStock, $ohStoreStock]);

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
