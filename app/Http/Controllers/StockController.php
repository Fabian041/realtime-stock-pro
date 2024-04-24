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
use App\Models\PeriodStock;
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
    
    public function stock_control($line, $code, $qty, $codepart = null)
    {
        try {
            DB::beginTransaction();
            
            $part = $this->getPart($code);
            $area = $this->getArea($line);

            $transaction = TmTransaction::where('name', 'Traceability')->firstOrFail();
            $reversalTransaction = $this->getReversalTransaction($line);

            if ($line !== 'PULL') {
                $wh = TmArea::where('name', 'Warehouse')->firstOrFail();
                $this->processBomMaterials($area->id, $part->id, $wh->id, $reversalTransaction->id);
            } else {
                $this->processPullLineTransaction($code, $part->id, $reversalTransaction->id, $qty, $codepart);
            }

            $this->processLineTransaction($line, $part->id, $transaction->id, $reversalTransaction->id, $qty, $codepart);
            $wipData = $this->getWipData();
            WebSocketPushJob::dispatch('wip', $wipData);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    private function getPart($code)
    {
        return TmPart::select('id')->where('back_number', $code)->firstOrFail();
    }

    private function getArea($line)
    {
        return TmArea::select('id')->where('name', 'LIKE', "%{$line}%")->firstOrFail();
    }

    private function getReversalTransaction($line)
    {
        $transactionName = $line === 'PULL' ? 'Pulling Delivery (R)' : 'Traceability (R)';
        return TmTransaction::select('id')->where('name', $transactionName)->firstOrFail();
    }

    private function processBomMaterials($areaId, $partId, $warehouseId, $transactionId)
    {
        $boms = TmBom::where('id_area', $areaId)->where('id_part', $partId)->get();
        $wh = TmArea::select('id')->where('name', 'Warehouse')->first();
        
        foreach ($boms as $bom) {
            TtMaterial::create([
                'id_material' => $bom->id_material,
                'qty' => $bom->qty_use * 1,
                'id_area' => $warehouseId,
                'id_transaction' => $transactionId,
                'pic' => 'avicenna user',
                'date' => now(),
            ]);

            TtOutput::create([
                'id_bom' => $bom->id,
                'date' => now(),
            ]);

            $result = $this->getCurrentMaterialStock($wh->id);
    
            // $this->pushData('wh',$result);
            WebSocketPushJob::dispatch('wh', $result);
        }
    }

    private function processLineTransaction($line, $partId, $transactionId, $reversalTransactionId, $qty, $codepart)
    {
        $model = $this->getModelByLine($line);
        if (!$model) {
            return;
        }

        $this->createPartTransaction($model, $partId, $transactionId, $qty, $codepart);

        if ($line !== 'PULL' && $line !== 'DC') {
            
            // if part id (back number) is CI17 and it scan at ASSY it will reduce qty of CI18 at MA
            if($partId == '16'){
                $partId = '10';    
            }
            
            $this->createPartTransaction($this->getPreviousLineModel($line), $partId, $reversalTransactionId, $qty, $codepart);
        }
    }

    private function processPullLineTransaction($code, $partId, $reversalTransactionId, $qty, $codepart)
    {
        $modelMapping = [
            'DI01' => new TtDc(),
            'DI02' => new TtDc(),
            'EI11' => new TtMa(),
            'EI12' => new TtMa(),
            'EI13' => new TtMa(),
            'EI14' => new TtMa(),
        ];

        $model = null;
        foreach ($modelMapping as $codePrefix => $modelCandidate) {
            if (strpos($code, $codePrefix) === 0) {
                $model = $modelCandidate;
                break;
            }
        }

        // If no specific model has been identified by the code, default to ASSY stock
        if (!$model) {
            $model = new TtAssy();
        }

        $this->createPartTransaction($model, $partId, $reversalTransactionId, $qty, $codepart);
    }

    private function getModelByLine($line)
    {
        switch ($line) {
            case 'DC': return new TtDc();
            case 'MA': return new TtMa();
            case 'AS': return new TtAssy();
            default: return null;
        }
    }

    private function getPreviousLineModel($line)
    {
        switch ($line) {
            case 'MA': return new TtDc();
            case 'AS': return new TtMa();
            default: return null;
        }
    }

    private function createPartTransaction($model, $partId, $transactionId, $qty, $codepart)
    {
        $model->create([
            'code' => $codepart,
            'id_part' => $partId,
            'id_transaction' => $transactionId,
            'pic' => 'avicenna user',
            'date' => Carbon::now()->format('Y-m-d H:i:s'),
            'qty' => $qty,
        ]);
    }

    private function getWipData()
    {
        $tcc = $this->getWipDc('TCC')->current_stock ?? 0;
        $opn = $this->getWipDc('OPN')->current_stock ?? 0;

        return ['TCC' => $tcc, 'OPN' => $opn];
    }

    private function getWipDc($model)
    {
        return DB::table('dc_stocks')
                ->join('tm_parts', 'tm_parts.id', '=', 'dc_stocks.id_part')
                ->select(DB::raw('SUM(current_stock) as current_stock'))
                ->where('tm_parts.status', '<>', 0)
                ->where('tm_parts.part_name', 'LIKE', "%{$model}%")
                ->first();
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
            $part = DB::table('dc_stocks')
                        ->join('tm_parts', 'dc_stocks.id_part', '=', 'tm_parts.id')
                        ->select('tm_parts.back_number', 'tm_parts.id')
                        ->get();
        }else if($area == 'MA'){
            $part = DB::table('ma_stocks')
                        ->join('tm_parts', 'ma_stocks.id_part', '=', 'tm_parts.id')
                        ->select('tm_parts.back_number', 'tm_parts.id')
                        ->get();
        }else{
            $part = DB::table('assy_stocks')
                        ->join('tm_parts', 'assy_stocks.id_part', '=', 'tm_parts.id')
                        ->select('tm_parts.back_number', 'tm_parts.id')
                        ->get();
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

    public function periodStock($area)
    {   
        $area = (int) $area;
            
        // Get the current time
        $currentTime = now();

        // Calculate the start and end times for the data
        $startTime = $currentTime->copy()->subHours(6);

        // If the start time is before today, adjust it to yesterday
        if ($startTime->isBefore(now()->startOfDay())) {
            $startTime->subDay();
        }

        // Retrieve data from your database based on the time range
        $data = PeriodStock::join('tm_parts', 'period_stocks.id_part', '=', 'tm_parts.id')
                        ->whereBetween('captured_at', [$startTime, $currentTime]);

        if ($area === 4) {
            $data->where('id_area', $area);
        } elseif ($area === 3) {
            $data->where('id_area', $area)->where('tm_parts.status', 1);
        }   elseif ($area === 2) {
            $data->where('id_area', $area)->where('tm_parts.status', 0);
        }

        $data = $data->orderBy('captured_at')
            ->get();

        // Pass the data to your view
        return response()->json([
            'status' => 'success',
            'data' => $data 
        ]);
    }
}