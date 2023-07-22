<?php

namespace App\Http\Controllers;

use App\Models\TtDc;
use App\Models\TtMa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class WipController extends Controller
{
    public function getWipDcStock(){
        
        function getWipStock($model){
            $result = DB::table('dc_stocks')
                    ->join('tm_parts', 'tm_parts.id', '=', 'dc_stocks.id_part')
                    ->select('tm_parts.qty_limit','tm_parts.part_name', 'tm_parts.part_number', 'tm_parts.back_number',DB::raw('SUM(current_stock) as current_stock'))
                    ->where('tm_parts.status', '<>' ,0)
                    ->where('tm_parts.part_name', 'LIKE', '%' . $model . '%')
                    ->groupBy('tm_parts.part_name')
                    ->get();

            return $result;
        }

        $tccStock = getWipStock('TCC');
        $opnStock = getWipStock('OPN');
        
        return response()->json([
            'tccStock'  => $tccStock,
            'opnStock' => $opnStock,
        ],200);
    }
    /**
     * Display DC dashboard
     *
     * 
     */
    public function wipDc()
    {
        function getWip($model){
            $result = DB::table('dc_stocks')
                    ->join('tm_parts', 'tm_parts.id', '=', 'dc_stocks.id_part')
                    ->select(DB::raw('SUM(current_stock) as current_stock'))
                    ->where('tm_parts.status', '<>' ,0)
                    ->where('tm_parts.part_name', 'LIKE', '%' . $model . '%')
                    ->groupBy('tm_parts.part_name')
                    ->first();

            return $result;
        }

        $tcc = getWip('TCC');
        $opn = getWip('OPN');

        $tcc = ($tcc) ? $tcc->current_stock : 0;
        $opn = ($opn) ? $opn->current_stock : 0;

        return view('layouts.wip.wip-dc',[
            'tcc' => $tcc,
            'opn' => $opn,
        ]);
    }

    public function getWipMaStock(){
        
        $result = DB::table('ma_stocks')
                ->join('tm_parts', 'tm_parts.id', '=', 'ma_stocks.id_part')
                ->select('tm_parts.qty_limit','tm_parts.part_name', 'tm_parts.part_number', 'tm_parts.back_number',DB::raw('SUM(current_stock) as current_stock'))
                ->where('tm_parts.status', '<>' ,1)
                ->groupBy('tm_parts.part_name')
                ->get();
        
        return $result;
    }

    public function wipMaGetTransaction()
    {
        try {
            $query = DB::table('tt_assy')
                ->join('tm_parts', 'tm_parts.id', '=', 'tt_assy.id_part')
                ->join('tm_transactions', 'tm_transactions.id', '=', 'tt_assy.id_transaction')
                ->select('tm_parts.part_name', 'tm_parts.part_number', 'tm_transactions.name', 'tm_transactions.type' ,'tt_assy.pic', 'tt_assy.date', 'tt_assy.qty')
                ->where('tm_parts.status', 2);

            // Get the total number of records before applying pagination or any filters
            $recordsTotal = $query->count();

            // Use DataTables' "of" method to handle pagination and filtering
            $input = DataTables::of($query)
                ->toJson();

            // Extract the total number of records after applying any filters
            $recordsFiltered = $input->original['recordsFiltered'] ?? $recordsTotal;

            // Add the 'recordsTotal' and 'recordsFiltered' properties to the JSON response
            $input->original['recordsTotal'] = $recordsTotal;
            $input->original['recordsFiltered'] = $recordsFiltered;

            return $input;
        } catch (\Exception $e) {
            // Return a JSON response indicating an error (optional)
            return response()->json(['error' => 'An error occurred.']);
        }
    }
    /**
     * Display MA dashboard
     *
     * 
     */
    public function wipMa()
    {
        return view('layouts.wip.wip-ma');
    }
    /**
     * Display ASSY dashboard
     *
     * 
     */
    public function wipAssy()
    {
        return view('layouts.wip.wip-assy');
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
}