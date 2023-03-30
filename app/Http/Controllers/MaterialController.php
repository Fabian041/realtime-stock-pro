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
use App\Models\TtMaterial;
use Illuminate\Http\Request;
use App\Models\TmTransaction;
use App\Events\StockDataUpdated;
use App\Imports\TtMaterialImport;
use Illuminate\Support\Facades\DB;
use Illuminate\Broadcasting\Channel;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Broadcast;

class MaterialController extends Controller
{
    /**
     * Display DC dashboard
     *
     * 
     */
    public function entryWh()
    {
        return view('layouts.entry-wh-material');
    }
    /**
     * Display DC dashboard
     *
     * 
     */
    public function entryOh()
    {
        return view('layouts.entry-oh-material');
    }
    /**
     * Display DC dashboard
     *
     * 
     */
    public function materialWh()
    {
        return view('layouts.material.material-wh');
    }
    /**
     * Display DC dashboard
     *
     * 
     */
    public function materialOh()
    {
        return view('layouts.material.material-oh');
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
        return view('layouts.checkin-material');    
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
        // get id transaction
        $transaction_id = TmTransaction::select('id')->where('name', 'Checkout Material')->first();

        return view('layouts.checkout-material',[
            'area' => TmArea::where('name', '<>', 'PPIC')->get(),
            'materials' => TmMaterial::all(),
            'checkouts' => TtMaterial::where('id_transaction',$transaction_id->id)->get()
        ]);
    }

    public function checkoutStore(Request $request)
    {
        // ppic pov
        $code = 112;
        $transaction = TmTransaction::select('id')->where('code', $code)->first();
        $code_id = $transaction->id;

        // production pov
        $prd_code = 211;
        $prd_transaction = TmTransaction::select('id')->where('code', $prd_code)->first();
        $prd_code_id = $prd_transaction->id;
        
        // get id area
        $ppic_id = TmArea::select('id')->where('name', 'PPIC')->first();

        // after checkout add transaction of ppic area
        $ppic = TtMaterial::create([
            'id_material' => $request->id_material,
            'qty' => $request->qty,
            'id_area' => $ppic_id->id,
            'id_transaction' => $code_id,
            'date' => date('Y-m-d H:i:s')
        ]);

        // add transaction based on area too
        $production = TtMaterial::create([
            'id_material' => $request->id_material,
            'qty' => $request->qty,
            'id_area' => $request->id_area,
            'id_transaction' => $prd_code_id,
            'date' => date('Y-m-d H:i:s')
        ]);

        // get material name, area, and transaction name
        $material = DB::table('tt_materials')
        ->join('tm_materials', 'tt_materials.id_material', '=', 'tm_materials.id')
        ->join('tm_areas', 'tt_materials.id_area', '=', 'tm_areas.id')
        ->join('tm_transactions', 'tt_materials.id_transaction', '=', 'tm_transactions.id')
        ->select('tm_materials.part_name','tm_areas.name','tm_materials.date')
        ->where('tt_materials.id', $production->id)
        ->first();
        
        return redirect()->route('checkout.index')->with('success', 'Success checkout item ' . $material->part_name . ' to ' .  $material->name . ' area');
    }

    public function getDataCheckout()
    {
        // get id transaction
        $transaction_id = TmTransaction::select('id')->where('name', 'Checkout Material')->first();

        $input =  DB::table('tt_materials')
                ->join('tm_materials', 'tt_materials.id_material', '=', 'tm_materials.id')
                ->join('tm_areas', 'tt_materials.id_area', '=', 'tm_areas.id')
                ->join('tm_transactions', 'tt_materials.id_transaction', '=', 'tm_transactions.id')
                ->select('tt_materials.id','tm_materials.part_name','tm_areas.name','tm_materials.date', 'tt_materials.qty', 'tm_transactions.name as detail')
                ->where('tt_materials.id_transaction',$transaction_id->id)
                ->get();

        return DataTables::of($input)
                ->addColumn('edit', function($row) use ($input){

                    $btn = '<button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#edit-'. $row->id .'"><span class="d-none d-sm-inline-block">Edit</span></button>';

                    return $btn;

                })
                ->rawColumns(['edit'])
                ->toJson();
    }
    
    public function getDataCheckin()
    {
        $input = DB::table('tt_materials')
                    ->join('tm_materials', 'tt_materials.id_material', '=', 'tm_materials.id')
                    ->select('tm_materials.part_name', 'tm_materials.part_number', 'tm_materials.supplier','tm_materials.source' ,'tm_materials.pic','tm_materials.date','tt_materials.qty')
                    ->get();

        return DataTables::of($input)
                ->toJson();
    }

    public function import(Request $request)
    {
        Excel::import(new TtMaterialImport, $request->file('file')->store('files'));

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
        $pusher->trigger('stock-data', 'StockDataUpdated', []);

        return redirect()->back();
    }
}