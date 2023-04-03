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
    public function indexWh()
    {
        return view('layouts.wh-material');
    }
    /**
     * Display DC dashboard
     *
     * 
     */
    public function scanWh(Request $request)
    {
        $barcode = $request->barcode;

        if($barcode){
            $arr = preg_split('/ +/', $barcode);
            $back_number = $arr[6];
            $part_number = substr($arr[3], 9, 20);
            $supplier = substr($arr[3], 0,9);
            $qty = substr($arr[7], 4, 3);
            $seri = substr($arr[8], 9, 13);

            // get id area
            $wh = TmArea::select('id')->where('name', 'Warehouse')->first();

            // check in tm material table, if it exists
            $material = TmMaterial::where('part_number', $part_number)->first();

            if(!$material){
                return [
                    'status' => 'error',
                    'message' => 'Part atau komponen tidak ditemukan'
                ];
            }

            // get id transaction
            $transaction = TmTransaction::select('id')->where('name', 'STO')->first();
            
            try {
                DB::beginTransaction();

                if($material){
                    TtMaterial::create([
                        'id_material' => $material->id,
                        'qty' => $qty,
                        'id_area' => $wh->id,
                        'id_transaction' => $transaction->id,
                        'pic' => auth()->user()->username,
                        'date' => date('Y-m-d H:i:s')
                    ]); 
                }

                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                return [
                    'status' => 'error',
                    'message' => $e->getMessage()
                ];
            }
            return [
                'status' => 'success',
                'back_number' => $back_number,
            ];
        }
    }
    /**
     * Display DC dashboard
     *
     * 
     */
    public function indexOh()
    {
        // get id transaction
        $transaction_id = TmTransaction::select('id')->where('name', 'Unboxing')->first();

        return view('layouts.oh-material',[
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
    // Scan unboxing
    public function scanOh(Request $request)
    {
        $barcode = $request->barcode;

        if($barcode){
            $arr = preg_split('/ +/', $barcode);
            $back_number = $arr[6];
            $part_number = substr($arr[3], 9, 20);
            $supplier = substr($arr[3], 0,9);
            $qty = substr($arr[7], 4, 3);

            // get id area
            $oh = TmArea::select('id')->where('name', 'OH Store')->first();
            $wh = TmArea::select('id')->where('name', 'Warehouse')->first();

            // check stock in warehouse
            $material = DB::table('tt_materials')
                        ->join('tm_materials', 'tm_materials.id', '=', 'tt_materials.id_material')
                        ->join('tm_transactions', 'tm_transactions.id', '=', 'tt_materials.id_transaction')
                        ->select('tm_materials.id','tm_materials.part_number','tm_materials.back_number', DB::raw('SUM(CASE WHEN tm_transactions.type = "supply" THEN qty ELSE -qty END) AS current_stock'))
                        ->where('tt_materials.id_area', $wh->id)
                        ->where('tm_materials.part_number', $part_number)
                        ->groupBy('tm_materials.part_number')
                        ->first();

            if($material == null  || !$material || $material == []){
                return [
                    'status' => 'error',
                    'message' => 'Part atau komponen tidak ditemukan'
                ];
            }elseif ($material->current_stock == 0) {
                return [
                    'status' => 'error',
                    'message' => 'Komponen habis atau tidak ditemukan'
                ];
            }

            // get id transaction
            $transaction = TmTransaction::select('id')->where('name', 'Unboxing')->first();
            $reversalTransaction= TmTransaction::select('id')->where('name', 'Unboxing (R)')->first();
            
            try {
                DB::beginTransaction();

                if($material){

                    // supply OH area
                    TtMaterial::create([
                        'id_material' => $material->id,
                        'qty' => $qty,
                        'id_area' => $oh->id,
                        'id_transaction' => $transaction->id,
                        'pic' => auth()->user()->username,
                        'date' => date('Y-m-d H:i:s')
                    ]); 

                    // checkout WH area
                    TtMaterial::create([
                        'id_material' => $material->id,
                        'qty' => $qty,
                        'id_area' => $wh->id,
                        'id_transaction' => $reversalTransaction->id,
                        'pic' => auth()->user()->username,
                        'date' => date('Y-m-d H:i:s')
                    ]); 
                }

                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                return [
                    'status' => 'error',
                    'message' => $e->getMessage()
                ];
            }
            return [
                'status' => 'success',
                'back_number' => $back_number,
            ];
        }
    }
    /**
     * Update OH
     *
     * 
     */
    // Manual unboxing 
    public function unboxOh(Request $request)
    {
        // get id area
        $oh = TmArea::select('id')->where('name', 'OH Store')->first();
        $wh = TmArea::select('id')->where('name', 'Warehouse')->first();

        // get id transaction
        $transaction = TmTransaction::select('id')->where('name', 'Unboxing')->first();
        $reversalTransaction= TmTransaction::select('id')->where('name', 'Unboxing (R)')->first();

        // check first , is that any stock in WH
        $material = DB::table('tt_materials')
                    ->join('tm_materials', 'tm_materials.id', '=', 'tt_materials.id_material')
                    ->join('tm_transactions', 'tm_transactions.id', '=', 'tt_materials.id_transaction')
                    ->select('tt_materials.id_material','tm_materials.part_number','tm_materials.back_number', DB::raw('SUM(CASE WHEN tm_transactions.type = "supply" THEN qty ELSE -qty END) AS current_stock'))
                    ->where('tt_materials.id_area', $wh->id)
                    ->where('tt_materials.id_material', $request->id_material)
                    ->groupBy('tm_materials.part_number')
                    ->first();

        if($material == null  || !$material || $material == []){
            return redirect()->back()->with('error', 'Part tidak ditemukan');
        }elseif($material->current_stock == 0) {
            return redirect()->back()->with('error', 'Part' . $material->part_number . 'habis atau tidak ditemukan');
        }

        try {
            DB::beginTransaction();
            
            if($material){
                
                // supply OH area
                TtMaterial::create([
                    'id_material' => $material->id,
                    'qty' => $request->qty * $request->pcs,
                    'id_area' => $oh->id,
                    'id_transaction' => $transaction->id,
                    'pic' => auth()->user()->username,
                    'date' => date('Y-m-d H:i:s')
                ]); 
                
                // checkout WH area
                TtMaterial::create([
                    'id_material' => $material->id,
                    'qty' => $request->qty * $request->pcs,
                    'id_area' => $wh->id,
                    'id_transaction' => $reversalTransaction->id,
                    'pic' => auth()->user()->username,
                    'date' => date('Y-m-d H:i:s')
                ]); 
            }
            
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error',$e->getMessage());
        }
        
        return redirect()->back()->with('success', 'Part' . $material->part_name. ' siap diunboxing');
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

    public function getWhMaterial()
    {
        // get id area
        $wh = TmArea::select('id')->where('name', 'Warehouse')->first();

        // source
        $ckd = 'CKD';
        $import = 'IMPORT';
        $local = 'LOCAL';

        
        function getWhMaterial($area,$source){
            $result = DB::table('material_stocks')
                ->join('tm_materials', 'tm_materials.id', '=', 'material_stocks.id_material')
                ->join('tm_areas', 'tm_areas.id', '=', 'material_stocks.id_area')
                ->select('material_stocks.current_stock', 'tm_materials.limit_qty','tm_materials.part_number' ,'tm_materials.part_name' ,'tm_materials.source')
                ->where('tm_areas.id', $area)
                ->where('tm_materials.source', 'like', '%' . $source . '%')
                ->groupBy('tm_materials.part_number')
                ->get();

            return $result;
        }

        try {
            // stock material WH
            $dataCkd = getWhMaterial($wh->id,$ckd);
            $dataImport = getWhMaterial($wh->id, $import);
            $dataLocal = getWhMaterial($wh->id, $local);

            return response()->json([
                'dataCkd' => $dataCkd,
                'dataImport' => $dataImport,
                'dataLocal' => $dataLocal,
            ],200);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ],404);
        }


    }
    public function getOhMaterial()
    {
        // get id area
        $oh = TmArea::select('id')->where('name', 'OH Store')->first();

        // source
        $ckd = 'CKD';
        $import = 'IMPORT';
        $local = 'LOCAL';

        function getOhMaterial($area,$source){
            $result = DB::table('material_stocks')
                ->join('tm_materials', 'tm_materials.id', '=', 'material_stocks.id_material')
                ->join('tm_areas', 'tm_areas.id', '=', 'material_stocks.id_area')
                ->select('material_stocks.current_stock', 'tm_materials.limit_qty','tm_materials.part_number' ,'tm_materials.part_name' ,'tm_materials.source')
                ->where('tm_areas.id', $area)
                ->where('tm_materials.source', 'like', '%' . $source . '%')
                ->groupBy('tm_materials.part_number')
                ->get();

            return $result;
        }

        try {
            // stock material WH
            $dataCkd = getOhMaterial($oh->id,$ckd);
            $dataImport = getOhMaterial($oh->id, $import);
            $dataLocal = getOhMaterial($oh->id, $local);

            return response()->json([
                'dataCkd' => $dataCkd,
                'dataImport' => $dataImport,
                'dataLocal' => $dataLocal,
            ],200);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ],404);
        }

    }
    
    public function getDcMaterial()
    {
        // get id area
        $dc = TmArea::select('id')->where('name', 'DC')->first();

        // source
        $ckd = 'CKD';
        $import = 'IMPORT';
        $local = 'LOCAL';

        function getDcMaterial($area,$source){
            $result = DB::table('material_stocks')
                ->join('tm_materials', 'tm_materials.id', '=', 'material_stocks.id_material')
                ->join('tm_areas', 'tm_areas.id', '=', 'material_stocks.id_area')
                ->select('material_stocks.current_stock', 'tm_materials.limit_qty','tm_materials.part_number' ,'tm_materials.part_name' ,'tm_materials.source')
                ->where('tm_areas.id', $area)
                ->where('tm_materials.source', 'like', '%' . $source . '%')
                ->groupBy('tm_materials.part_number')
                ->get();

            return $result;
        }

        try {
            // stock material WH
            $dataCkd = getDcMaterial($dc->id,$ckd);
            $dataImport = getDcMaterial($dc->id, $import);
            $dataLocal = getDcMaterial($dc->id, $local);

            return response()->json([
                'dataCkd' => $dataCkd,
                'dataImport' => $dataImport,
                'dataLocal' => $dataLocal,
            ],200);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ],404);
        }

    }

    public function getMaMaterial()
    {
        // get id area
        $ma = TmArea::select('id')->where('name', 'MA')->first();

        // source
        $ckd = 'CKD';
        $import = 'IMPORT';
        $local = 'LOCAL';

        function getMaMaterial($area,$source){
            $result = DB::table('material_stocks')
                ->join('tm_materials', 'tm_materials.id', '=', 'material_stocks.id_material')
                ->join('tm_areas', 'tm_areas.id', '=', 'material_stocks.id_area')
                ->select('material_stocks.current_stock', 'tm_materials.limit_qty','tm_materials.part_number' ,'tm_materials.part_name' ,'tm_materials.source')
                ->where('tm_areas.id', $area)
                ->where('tm_materials.source', 'like', '%' . $source . '%')
                ->groupBy('tm_materials.part_number')
                ->get();

            return $result;
        }

        try {
            // stock material WH
            $dataCkd = getMaMaterial($ma->id,$ckd);
            $dataImport = getMaMaterial($ma->id, $import);
            $dataLocal = getMaMaterial($ma->id, $local);

            return response()->json([
                'dataCkd' => $dataCkd,
                'dataImport' => $dataImport,
                'dataLocal' => $dataLocal,
            ],200);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ],404);
        }

    }

    public function checkout()
    {
        // get id transaction
        $transaction_id = TmTransaction::select('id')->where('name', 'Pulling Production')->first();

        return view('layouts.checkout-material',[
            'area' => TmArea::all(),
            'materials' => TmMaterial::all(),
            'checkouts' => TtMaterial::where('id_transaction',$transaction_id->id)->get()
        ]);
    }

    public function scanProd(Request $request)
    {
        $barcode = $request->barcode;

        if($barcode){
            $arr = preg_split('/ +/', $barcode);
            $back_number = $arr[6];
            $part_number = substr($arr[3], 9, 20);
            $supplier = substr($arr[3], 0,9);
            $qty = substr($arr[7], 4, 3);

            // get id area
            $oh = TmArea::select('id')->where('name', 'OH Store')->first();
            $wh = TmArea::select('id')->where('name', 'Warehouse')->first();

            // get id prod area, from authenticated user
            $area = auth()->user()->department;
            $prod = TmArea::select('id')->where('name', $area)->first();

            // check stock in OH store
            $material = DB::table('tt_materials')
                        ->join('tm_materials', 'tm_materials.id', '=', 'tt_materials.id_material')
                        ->join('tm_transactions', 'tm_transactions.id', '=', 'tt_materials.id_transaction')
                        ->select('tm_materials.id','tm_materials.part_number','tm_materials.back_number', DB::raw('SUM(CASE WHEN tm_transactions.type = "supply" THEN qty ELSE -qty END) AS current_stock'))
                        ->where('tt_materials.id_area', $oh->id)
                        ->where('tm_materials.part_number', $part_number)
                        ->groupBy('tm_materials.part_number')
                        ->first();

            if($material == null  || !$material || $material == []){
                return [
                    'status' => 'error',
                    'message' => 'Part atau komponen tidak ditemukan'
                ];
            }elseif ($material->current_stock == 0) {
                return [
                    'status' => 'error',
                    'message' => 'Komponen habis, unboxing dulu!'
                ];
            }

            // get id transaction
            $transaction = TmTransaction::select('id')->where('name', 'Pulling Production')->first();
            $reversalTransaction= TmTransaction::select('id')->where('name', 'Pulling Production (R)')->first();
            
            try {
                DB::beginTransaction();

                if($material){

                    // supply Prod area
                    TtMaterial::create([
                        'id_material' => $material->id,
                        'qty' => $qty,
                        'id_area' => $prod->id,
                        'id_transaction' => $transaction->id,
                        'pic' => auth()->user()->username,
                        'date' => date('Y-m-d H:i:s')
                    ]); 

                    // checkout OH area
                    TtMaterial::create([
                        'id_material' => $material->id,
                        'qty' => $qty,
                        'id_area' => $oh->id,
                        'id_transaction' => $reversalTransaction->id,
                        'pic' => auth()->user()->username,
                        'date' => date('Y-m-d H:i:s')
                    ]); 
                }

                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                return [
                    'status' => 'error',
                    'message' => $e->getMessage()
                ];
            }
            return [
                'status' => 'success',
                'back_number' => $back_number,
            ];
        }
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
        ->select('tm_materials.part_name','tm_areas.name','tt_materials.date')
        ->where('tt_materials.id', $oh->id)
        ->first();
        
        return redirect()->route('entry-oh.index')->with('success', 'Success move ' . $material->part_name . ' to ' .  $material->name . ' area');
    }

    public function getDataCheckout()
    {
        // get id transaction
        $transaction_id = TmTransaction::select('id')->where('name', 'Pulling Production')->first();

        $input =  DB::table('tt_materials')
                ->join('tm_materials', 'tt_materials.id_material', '=', 'tm_materials.id')
                ->join('tm_areas', 'tt_materials.id_area', '=', 'tm_areas.id')
                ->join('tm_transactions', 'tt_materials.id_transaction', '=', 'tm_transactions.id')
                ->select('tt_materials.id','tm_materials.part_name','tm_areas.name', 'tt_materials.qty', 'tt_materials.date', 'tt_materials.pic')
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
        $transaction_id = TmTransaction::select('id')->where('name', 'STO')->first();

        $input = DB::table('tt_materials')
                    ->join('tm_materials', 'tt_materials.id_material', '=', 'tm_materials.id')
                    ->select('tm_materials.part_name', 'tm_materials.part_number', 'tm_materials.supplier','tm_materials.source' ,'tt_materials.pic','tm_materials.date','tt_materials.qty')
                    ->where('id_transaction', $transaction_id->id)
                    ->get();

        return DataTables::of($input)
                ->toJson();
    }

    // stock in planning unboxing today
    public function getDataOh()
    {
        // get id transaction
        $transaction_id = TmTransaction::select('id')->where('name', 'Unboxing')->first();

        $input =   DB::table('tt_materials')
                    ->join('tm_materials', 'tt_materials.id_material', '=', 'tm_materials.id')
                    ->select('tm_materials.part_name', 'tm_materials.part_number', 'tm_materials.supplier','tm_materials.source' ,'tt_materials.pic','tm_materials.date','tt_materials.qty')
                    ->where('id_transaction', $transaction_id->id)
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