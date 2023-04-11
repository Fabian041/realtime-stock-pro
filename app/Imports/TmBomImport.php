<?php

namespace App\Imports;

use App\Models\TmArea;
use App\Models\TmBom;
use App\Models\TmPart;
use App\Models\TmMaterial;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TmBomImport implements ToCollection, WithHeadingRow, WithStartRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection $rows)
    {
        try {
            DB::beginTransaction();

            // get part
            $parts = TmPart::select('id', 'part_name', 'part_number', 'back_number')->get();

            // get material
            $materials = TmMaterial::select('id', 'part_name', 'part_number', 'back_number', 'supplier', 'source')->get();

            // get area
            $areas = TmArea::all();

            // check all row because is depend with another tables
            foreach ($rows as $row){

                // check part column (part number and part name)
                foreach ($parts as $part){
                    if($row['product'] == $part->part_name && $row['part_number'] == $part->part_number && $row['back_number'] == $part->back_number){

                        // check area column
                        foreach ($areas as $area){
                            if($row['area'] == $area->name){

                                // check material column
                                foreach ($materials as $material){
                                    if($row['component'] == $material->part_name){

                                        TmBom::create([
                                            'id_part' => $part->id,
                                            'id_area' => $area->id,
                                            'id_material' => $material->id,
                                            'qty_use' => $row['qty'],
                                            'uom' => $row['satuan'],
                                        ]);

                                    }
                                }

                            }
                        }

                    }
                }
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            return [
                'status' => 'error',
                'message' => $th->getMessage(),
            ];
        }
    }

    public function startRow(): int
    {
        return 3; // skip the first three rows
    }
}
