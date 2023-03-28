<?php

namespace App\Imports;

use App\Models\TtMaterial;
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
        foreach($rows as $row)
        {
            // check each row in tm material based on tm material id
            
            // get id of the same row

            // insert in tt material
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