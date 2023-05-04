<?php

namespace App\Imports;

use App\Models\TmMaterial;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TmMaterialImport implements ToCollection, WithHeadingRow, WithStartRow
{
    public function collection(Collection $rows)
    {
        try {
            foreach($rows as $row)
            {
                TmMaterial::updateOrCreate(
                    [               
                        'part_number' => $row['part_no'],
                        'supplier' => $row['supplier'],
                    ],
                    [
                        'back_number' => $row['back_no'],
                        'part_name' => $row['part_name'],
                        'date' => date('Y-m-d'),
                        'time' => date('H:i:s'),
                        'source' => $row['source'],
                        'limit_qty' => $row['minimum_qty'],
                    ]
                );
            }

            return ['status' => 'success'];

        } catch (\Throwable $th) {
            return [
                'status' => 'error',
                'message' => $th->getMessage()
            ];
        }
    }

    public function onFailure(Failure ...$failures)
    {
        $partNumbers = [];

        foreach ($failures as $failure) {
            $partNumber = $failure->values()['part_no'];
            if (!isset($partNumbers[$partNumber])) {
                // this part number has not been processed yet, find all products with this part number
                $materials = TmMaterial::where('part_no', $partNumber)->get();

                // update all products with the new data from the Excel file
                foreach ($materials as $material) {
                    $material->part_name = $failure->values()['part_name'];
                    $material->source = $failure->values()['source'];
                    $material->supplier = $failure->values()['supplier'];
                    $material->limit = $failure->values()['limit'];

                    // save
                    $material->save();
                }

                $partNumbers[$partNumber] = true; // mark this part number as processed
            }
        }
    }


    public function startRow(): int
    {
        return 4; // skip the first three rows
    }

    public function rules(): array
    {
        return [
            '*.part_name' => 'required',
            '*.part_number' => 'required',
            '*.pic' => 'required',
            '*.date' => 'required',
            '*.time' => 'required',
            '*.supplier' => 'required',
            '*.source' => 'required',
            '*.limit_qty' => 'required',
        ];
    }
}
