<?php

namespace App\Http\Controllers;

use Pusher\Pusher;
use App\Models\TtDc;
use App\Models\TtMa;
use App\Models\TmBom;
use App\Models\TmArea;
use App\Models\TmPart;
use App\Models\TtAssy;
use App\Models\TtStock;
use App\Models\TtOutput;
use Illuminate\Http\Request;
use App\Events\StockDataUpdated;

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
        $currentDcStock = TtDc::select('qty')->where('id_part',$idPart)->first();
        $currentMaStock = TtMa::select('qty')->where('id_part',$idPart)->first();
        $currentAsStock = TtAssy::select('qty')->where('id_part',$idPart)->first();

        //insert part number in line table (tt dc/tt ma) based on line
        if($line == 'DC'){
            
            // DC Line
            if($currentDcStock === null){
                TtDc::where('id_part', $idPart)->create([
                    'id_part' => $idPart,
                    'qty' => 1
                ]);
            }else{
                TtDc::where('part_number', $idPart)->update([
                    'id_part' => $idPart,
                    'qty' => $currentDcStock->qty + 1
                ]);
            }
            
        }elseif($line == 'MA'){

            if($currentMaStock === null){
                // MA Line
                TtMa::where('id_part', $idPart)->create([
                    'id_part' => $idPart,
                    'qty' => 1
                ]);
            }

            TtMa::where('id_part', $idPart)->update([
                'id_part' => $idPart,
                'qty' => $currentMaStock->qty + 1
            ]);

            // modify DC stock
            TtDc::where('id_part', $idPart)->update([
                'id_part' => $idPart,
                'qty' => $currentDcStock->qty - 1
            ]);

        }elseif($line == 'AS'){
            if($currentAsStock === null){
                // AS Line
                TtAssy::where('id_part', $idPart)->create([
                    'id_part' => $idPart,
                    'qty' => 1
                ]);
            }

            TtAssy::where('id_part', $code)->update([
                'id_part' => $code,
                'qty' => $currentAsStock->qty + 1
            ]);

            // modify MA stock
            TtMa::where('id_part', $code)->update([
                'id_part' => $code,
                'qty' => $currentMaStock->qty - 1
            ]);
        }

        // get currnet stock quantity
        $ckd = TtStock::where('source', 'like', '%CKD%')->sum('qty');
        $import = TtStock::where('source', 'like', '%IMPORT%')->sum('qty');
        $local = TtStock::where('source', 'like', '%LOCAL%')->sum('qty');

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
        $pusher->trigger('stock-data', 'StockDataUpdated', [$ckd,$import,$local]);

        return response()->json([
            'message' => 'success'
        ],200);
    }
}
