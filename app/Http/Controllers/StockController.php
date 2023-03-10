<?php

namespace App\Http\Controllers;

use App\Models\TtDc;
use App\Models\TtMa;
use App\Models\TmBom;
use App\Models\TmArea;
use App\Models\TmPart;
use App\Models\TtAssy;
use App\Models\TtStock;
use App\Models\TtOutput;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function stock_control($line , $code)
    {
        //search code part in tm part number table
        $idPart = TmPart::select('id')->where('part_number',$code)->first();

        //search area in tm area table
        $idArea = TmArea::select('id')->where('name', $line)->first();

        //search bom of the part number based on line in tm bom table
        $boms = TmBom::select('id','id_partBom','qty_use')
                    ->where('id_area', $idArea->id)
                    ->where('id_part', $idPart->id)
                    ->get();

        // get current stock quantity
        foreach ($boms as $bom) {
            $currStock = TtStock::select('qty')
                            ->where('id_part',$bom->id_partBom)
                            ->first();
            //modify quantiy of material in tt stock table
            $updateStock = TtStock::where('id_part',$bom->id_partBom)
                            ->update([
                                'qty' => $currStock->qty - $bom->qty_use
                            ]);

            // insert id bom inside tt output table
            TtOutput::create([
                'id_bom' => $bom->id,
                'date' => date('Y-m-d'),
            ]);
        }



        // get current quantity
        $currentDcStock = TtDc::select('qty')->where('part_number',$code)->first();
        $currentMaStock = TtMa::select('qty')->where('part_number',$code)->first();
        $currentAsStock = TtAssy::select('qty')->where('part_number',$code)->first();

        //insert part number in line table (tt dc/tt ma) based on line
        if($line == 'DC'){
            // DC Line
            if($currentDcStock === null){
                TtDc::where('part_number', $code)->create([
                    'part_number' => $code,
                    'qty' => 1
                ]);
            }else{
                TtDc::where('part_number', $code)->update([
                    'part_number' => $code,
                    'qty' => $currentDcStock->qty + 1
                ]);
            }
            
        }elseif($line == 'MA'){

            if($currentMaStock === null){
                // MA Line
                TtMa::where('part_number', $code)->create([
                    'part_number' => $code,
                    'qty' => 1
                ]);
            }

            TtMa::where('part_number', $code)->update([
                'part_number' => $code,
                'qty' => $currentMaStock->qty + 1
            ]);

            // modify DC stock
            TtDc::where('part_number', $code)->update([
                'part_number' => $code,
                'qty' => $currentDcStock->qty - 1
            ]);

        }elseif($line == 'AS'){
            if($currentAsStock === null){
                // AS Line
                TtAssy::where('part_number', $code)->create([
                    'part_number' => $code,
                    'qty' => 1
                ]);
            }

            TtAssy::where('part_number', $code)->update([
                'part_number' => $code,
                'qty' => $currentAsStock->qty + 1
            ]);

            // modify MA stock
            TtMa::where('part_number', $code)->update([
                'part_number' => $code,
                'qty' => $currentMaStock->qty - 1
            ]);
        }


        return response()->json([
            'message' => 'success'
        ],200);
    }
}
