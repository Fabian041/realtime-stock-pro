<?php

namespace App\Imports;

use App\Models\TmMaterial;
use App\Models\TtMaterial;
use App\Models\TmTransaction;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TtMaterialImport implements ToCollection, WithHeadingRow, WithStartRow
{
    public function collection(Collection $rows)
    {
        // transaction id
        $code = 111;
        $transaction = TmTransaction::select('id')->where('code', $code)->first();
        $code_id = $transaction->id;

        // area id
        $area = \App\Models\TmArea::select('id')->where('name', 'PPIC')->first();
        $area_id = $area->id;

        foreach($rows as $row)
        {
            // check each row in tm material based on tm material id
            $materials = TmMaterial::select('id','part_number', 'part_name', 'supplier', 'source')->get();
            
            // get id of the same row
            foreach( $materials as $material){
                // check data
                if ($row['part_no'] == $material->part_number && $row['part_name'] == $material->part_name && $row['supplier'] == $material->supplier && $row['source'] == $material->source){
                    
                    // insert in tt material
                    TtMaterial::create([
                        'id_material' => $material->id,
                        'qty' => $row['qty'],
                        'id_transaction' => $code_id,
                        'id_area' => $area_id,
                        'date' => date('Y-m-d H:i:s')
                    ]);
                }
            }

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