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
        // get id transaction
        $transaction_id = TmTransaction::select('id')->where('name', 'Planning Unboxing')->first();

        return view('layouts.entry-oh-material',[
            'area' => TmArea::all(),
            'materials' => TmMaterial::all(),
            'checkouts' => TtMaterial::where('id_transaction',$transaction_id->id)->get()
        ]);
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

    public function getMaterial()
    {
        // get id area
        $wh = TmArea::select('id')->where('name', 'Warehouse')->first();

        $dataCkd = DB::table('tm_materials')->join('tt_materials','tm_materials.id', '=', 'tt_materialss.id_material')
                ->select(DB::raw('SUM(qty) where') ,'tm_materials.limit_qty','tm_materials.part_number' ,'tm_materials.part_name' ,'tm_materials.source')
                ->where('source', 'like', '%CKD%')
                ->where('id_area', $wh->id)
                ->groupBy('tm_materials.part_number')
                ->get();

        $dataImport = DB::table('tm_materials')->join('tt_materials','tm_materials.id', '=', 'tt_materialss.id_material')
                ->select('tm_materials.limit_qty','tm_materials.part_number' ,'tm_materials.part_name' ,'tm_materials.source', 'tt_materials.qty')
                ->where('source', 'like', '%IMPORT%')
                ->where('id_area', $wh->id)
                ->groupBy('tm_materials.part_number')
                ->get();

        $dataLocal= DB::table('tm_materials')->join('tt_materials','tm_materials.id', '=', 'tt_materialss.id_material')
                ->select('tm_materials.limit_qty','tm_materials.part_number' ,'tm_materials.part_name' ,'tm_materials.source', 'tt_materials.qty')
                ->where('source', 'like', '%LOCAL%')
                ->where('id_area', $wh->id)
                ->groupBy('tm_materials.part_number')
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
            'area' => TmArea::all(),
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
        $ppic_id = TmArea::select('id')->where('name', 'Warehouse')->first();

        // check first , is that any stock in OH Store? (calculate the quantity per item in Oh store)

        // after checkout add transaction of ppic area
        $ppic = TtMaterial::create([
            'id_material' => $request->id_material,
            'qty' => - $request->qty, // minus in wh area
            'id_area' => $ppic_id->id,
            'id_transaction' => $code_id,
            'date' => date('Y-m-d H:i:s')
        ]);

        // add transaction based on area too
        $production = TtMaterial::create([
            'id_material' => $request->id_material,
            'qty' => $request->qty, //plus in prod area
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

    public function entryOhStore(Request $request)
    {
        // OH pov
        $oh_transaction = TmTransaction::select('id')->where('name', 'Planning Unboxing')->first();

        // WH pov
        $wh_transaction = TmTransaction::select('id')->where('name', 'Planning Unboxing (R)')->first();
        
        // get id area
        $oh_id = TmArea::select('id')->where('name', 'OH Store')->first();
        $wh_id = TmArea::select('id')->where('name', 'Warehouse')->first();

        // check first , is that any stock in WH? (calculate the quantity per item in warehouse)

        // after checkout add transaction of ppic area
        $oh = TtMaterial::create([
            'id_material' => $request->id_material,
            'qty' => $request->qty, //plus un oh area
            'id_area' => $oh_id->id,
            'id_transaction' => $oh_transaction->id,
            'date' => date('Y-m-d H:i:s')
        ]);

        // add transaction based on area too
        $wh = TtMaterial::create([
            'id_material' => $request->id_material,
            'qty' => - $request->qty, //minus in wh area
            'id_area' => $wh_id->id,
            'id_transaction' => $wh_transaction->id,
            'date' => date('Y-m-d H:i:s')
        ]);

        // get material name, area, and transaction name
        $material = DB::table('tt_materials')
        ->join('tm_materials', 'tt_materials.id_material', '=', 'tm_materials.id')
        ->join('tm_areas', 'tt_materials.id_area', '=', 'tm_areas.id')
        ->join('tm_transactions', 'tt_materials.id_transaction', '=', 'tm_transactions.id')
        ->select('tm_materials.part_name','tm_areas.name','tm_materials.date')
        ->where('tt_materials.id', $oh->id)
        ->first();
        
        return redirect()->route('entry-oh.index')->with('success', 'Success move ' . $material->part_name . ' to ' .  $material->name . ' area');
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
    
    // initial stock from supplier
    public function getDataWh()
    {
        // get id transaction
        $transaction_id = TmTransaction::select('id')->where('name', 'Supply Material')->first();

        $input = DB::table('tt_materials')
                    ->join('tm_materials', 'tt_materials.id_material', '=', 'tm_materials.id')
                    ->select('tm_materials.part_name', 'tm_materials.part_number', 'tm_materials.supplier','tm_materials.source' ,'tm_materials.pic','tm_materials.date','tt_materials.qty')
                    ->where('id_transaction', $transaction_id->id)
                    ->get();

        return DataTables::of($input)
                ->toJson();
    }

    // stock in planning unboxing today
    public function getDataOh()
    {
        // get id transaction
        $transaction_id = TmTransaction::select('id')->where('name', 'Planning Unboxing')->first();

        $input =  DB::table('tt_materials')
                ->join('tm_materials', 'tt_materials.id_material', '=', 'tm_materials.id')
                ->join('tm_areas', 'tt_materials.id_area', '=', 'tm_areas.id')
                ->join('tm_transactions', 'tt_materials.id_transaction', '=', 'tm_transactions.id')
                ->select('tt_materials.id','tm_materials.part_name','tm_areas.name','tm_materials.part_number', 'tt_materials.qty', 'tm_transactions.name as detail')
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