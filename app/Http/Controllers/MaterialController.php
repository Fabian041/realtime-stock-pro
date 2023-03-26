<?php

namespace App\Http\Controllers;

use Pusher\Pusher;
use App\Models\TtDc;
use App\Models\TtMa;
use App\Models\TmArea;
use App\Models\TmPart;
use App\Models\TtAssy;
use App\Models\TtStock;
use App\Models\TmMaterial;
use App\Models\TtCheckout;
use Illuminate\Http\Request;
use App\Events\StockDataUpdated;
use Illuminate\Support\Facades\DB;
use Illuminate\Broadcasting\Channel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Broadcast;

class MaterialController extends Controller
{
    /**
     * Display DC dashboard
     *
     * 
     */
    public function materialPpic()
    {
        return view('layouts.material.material-ppic');
    }
    /**
     * Display DC dashboard
     *
     * 
     */
    public function materialDc()
    {
        return view('layouts.material.material-dc');
    }
    /**
     * Display DC dashboard
     *
     * 
     */
    public function materialMa()
    {
        return view('layouts.material.material-ma');
    }
    /**
     * Display DC dashboard
     *
     * 
     */
    public function materialAssy()
    {
        return view('layouts.material.material-assy');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //    
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getMaterial(){

        $dataCkd = DB::table('tm_parts')->join('tt_stocks','tm_parts.id', '=', 'tt_stocks.id_part')
                ->select('part_name','qty_limit','source', 'qty')
                ->where('source', 'like', '%CKD%')
                ->groupBy('id_part')
                ->get();

        $dataImport = DB::table('tm_parts')->join('tt_stocks','tm_parts.id', '=', 'tt_stocks.id_part')
                ->select('part_name','qty_limit','source', 'qty')
                ->where('source', 'like', '%IMPORT%')
                ->groupBy('id_part')
                ->get();

        $dataLocal= DB::table('tm_parts')->join('tt_stocks','tm_parts.id', '=', 'tt_stocks.id_part')
                ->select('part_name','qty_limit','source', 'qty')
                ->where('source', 'like', '%LOCAL%')
                ->groupBy('id_part')
                ->get();
                
        return response()->json([
            'dataCkd' => $dataCkd,
            'dataImport' => $dataImport,
            'dataLocal' => $dataLocal,
        ]);

    }

    public function checkout()
    {
        return view('layouts.checkout-material',[
            'area' => TmArea::all(),
            'materials' => TmMaterial::all(),
        ]);
    }

    public function checkoutStore(Request $request)
    {
        $validatedData = $request->validate([
            'id_area' => 'required', //get id
            'id_material' => 'required', //get id
            'qty' => 'required'
        ]);

        TtCheckout::create($validatedData);

        // get part name current stock
        $currStock = TtStock::select('qty')
                    ->where('id_part', $validatedData['id_part'])
                    ->first();

        // modify stock in table tt stock based on part used
        TtStock::where('id_part', $validatedData['id_part'])
                ->update([
                    'qty' => $currStock->qty - $validatedData['qty']
                ]);
                
        
        // get current stock each area
        $currentDcStock = TtDc::select('qty')->where('id_part',$validatedData['id_part'])->first();
        $currentMaStock = TtMa::select('qty')->where('id_part',$validatedData['id_part'])->first();
        $currentAsStock = TtAssy::select('qty')->where('id_part',$validatedData['id_part'])->first();

        // modify material stock in each area
        if($validatedData['id_area'] == 3)
        {
            if($currentDcStock !== null){

                TtDc::where('id_part', $validatedData['id_part'])
                    ->update([
                        'qty' => $currentDcStock->qty + $validatedData['qty']
                    ]);
            }

            TtDc::create([
                'id_part' => $validatedData['id_part'],
                'qty' => $validatedData['qty']
            ]);

        }elseif($validatedData['id_area'] == 1){

            if($currentMaStock !== null){

                TtMa::where('id_part', $validatedData['id_part'])
                    ->update([
                        'qty' => $currentMaStock->qty + $validatedData['qty']
                    ]);
            }

            TtMa::create([
                'id_part' => $validatedData['id_part'],
                'qty' => $validatedData['qty']
            ]);

        }elseif($validatedData['id_area'] == 5){

            if($currentAsStock !== null){

                TtAssy::where('id_part', $validatedData['id_part'])
                    ->update([
                        'qty' => $currentAsStock->qty + $validatedData['qty']
                    ]);
            }

            TtAssy::create([
                'id_part' => $validatedData['id_part'],
                'qty' => $validatedData['qty']
            ]);

        }

        
        return redirect()->route('checkout.index')->with('success', 'Success checkout item id-' . $validatedData['id_part']);
    }

    public function getDataCheckout()
    {
        $input = DB::table('tt_checkouts')
                    ->join('tm_parts', 'tt_checkouts.id_part', '=', 'tm_parts.id')
                    ->join('tm_areas', 'tt_checkouts.id_area', '=', 'tm_areas.id')
                    ->select('tm_parts.part_name', 'tm_areas.name' , 'tt_checkouts.qty')
                    ->get();

        return DataTables::of($input)
                ->toJson();
    }
}