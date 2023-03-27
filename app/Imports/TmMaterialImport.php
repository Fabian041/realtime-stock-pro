<?php

namespace App\Imports;

use App\Models\TmMaterial;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;

class TmMaterialImport implements ToCollection, WithHeadingRow, WithStartRow
{
    public function collection(Collection $rows)
    {
        foreach($rows as $row)
        {
            TmMaterial::updateOrCreate([
                'part_name' => $row['part_name'],
                'part_number' => $row['part_no'],
                'pic' => auth()->user()->username,
                'date' => date('Y-m-d'),
                'time' => date('H:i:s'),
                'supplier' => $row['supplier'],
                'source' => $row['source'],
                'limit_qty' => $row['limit']
            ]);
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
