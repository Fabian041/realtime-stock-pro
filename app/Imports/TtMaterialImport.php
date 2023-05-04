<?php

namespace App\Imports;

use Carbon\Carbon;
use Pusher\Pusher;
use App\Models\TmArea;
use App\Models\TmMaterial;
use App\Models\TtMaterial;
use App\Models\TmTransaction;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TtMaterialImport implements ToCollection, WithHeadingRow, WithStartRow
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
    
    public function collection(Collection $rows)
    {
        // transaction id
        $transaction = TmTransaction::select('id')->where('name', 'STO')->first();

        // area id
        $area = \App\Models\TmArea::select('id')->where('name', 'Warehouse')->first();
        $area_id = $area->id;

        try {
            DB::beginTransaction();

            // get id area
            $wh = TmArea::select('id')->where('name', 'Warehouse')->first();

            $quantities = [];

            foreach($rows as $row)
            {
                // check each row in tm material based on tm material id
                $materials = TmMaterial::select('id','part_number', 'part_name', 'supplier', 'source', 'back_number')->get();
                
                // get id of the same row
                foreach( $materials as $material){

                    // this condition will check imported data with master material data, if the imported data is exist in master material it will insert it into tt material table
                    if ($row['part_no'] == $material->part_number && $row['back_no'] == $material->back_number && $row['part_name'] == $material->part_name && $row['supplier'] == $material->supplier && $row['source'] == $material->source){

                        // if same part number it will sum the quantity
                        // if (!isset($quantities[$material->part_number])) {
                        //     $quantities[$material->part_number] = $row['qty'];
                        // } else {
                        //     $quantities[$material->part_number] += $row['qty'];
                        // }
                        
                        // insert in tt material
                        TtMaterial::create([
                            'id_material' => $material->id,
                            'qty' => $quantities,
                            'id_area' => $area_id,
                            'id_transaction' => $transaction->id,
                            'pic' => auth()->user()->npk,
                            'date' => Carbon::now()->format('Y-m-d H:i:s')
                        ]);
                        
                    }
                }
            }     
            
            // get current stock after import tt material
            $result = $this->getCurrentMaterialStock($wh->id);

            // push to websocket
            $this->pushData('wh',$result);
            
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            return [
                'status' => 'error',
                'message' => $th->getMessage(),
            ];
        }
    }

    public function onFailure(Failure ...$failures)
    {
        // 
    }


    public function startRow(): int
    {
        return 4; // skip the first three rows
    }

    public function rules(): array
    {
        return [
            '*.qty' => 'required',
        ];
    }
}