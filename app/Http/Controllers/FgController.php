<?php

namespace App\Http\Controllers;

use Pusher\Pusher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class FgController extends Controller
{

    public function getFgDcStock(){
        
        $result = DB::table('dc_stocks')
                ->join('tm_parts', 'tm_parts.id', '=', 'dc_stocks.id_part')
                ->select('tm_parts.qty_limit','tm_parts.part_name', 'tm_parts.part_number', 'tm_parts.back_number',DB::raw('SUM(current_stock) as current_stock'))
                ->where('tm_parts.status', 0)
                ->groupBy('tm_parts.part_name')
                ->get();
        
        return $result;
    }

    public function fgDcGetTransaction()
    {
        // get from ttdc
        $input = DB::table('tt_dcs')
                ->join('tm_parts', 'tm_parts.id', '=', 'tt_dcs.id_part')
                ->join('tm_transactions', 'tm_transactions.id', '=', 'tt_dcs.id_transaction')
                ->select('tm_parts.part_name', 'tm_parts.part_number', 'tm_transactions.name', 'tm_transactions.type' ,'tt_dcs.pic', 'tt_dcs.date', 'tt_dcs.qty')
                ->where('tm_parts.status', 0)
                ->get();

        return DataTables::of($input)
                ->toJson();
    }
    
    public function getFgMaStock(){
        
        $result = DB::table('ma_stocks')
                ->join('tm_parts', 'tm_parts.id', '=', 'ma_stocks.id_part')
                ->select('tm_parts.qty_limit','tm_parts.part_name', 'tm_parts.part_number', 'tm_parts.back_number',DB::raw('SUM(current_stock) as current_stock'))
                ->where('tm_parts.status', 1)
                ->groupBy('tm_parts.part_name')
                ->get();
        
        return $result;
    }

    public function fgMaGetTransaction()
    {
        // get from ttma
        $input = DB::table('tt_mas')
                ->join('tm_parts', 'tm_parts.id', '=', 'tt_mas.id_part')
                ->join('tm_transactions', 'tm_transactions.id', '=', 'tt_mas.id_transaction')
                ->select('tm_parts.part_name', 'tm_parts.part_number', 'tm_transactions.name','tm_transactions.type' , 'tt_mas.pic', 'tt_mas.date', 'tt_mas.qty')
                ->where('tm_parts.status', 1)
                ->get();

        return DataTables::of($input)
                ->toJson();
    }
    
    public function getFgAssyStock(){
        
        $result = DB::table('assy_stocks')
                ->join('tm_parts', 'tm_parts.id', '=', 'assy_stocks.id_part')
                ->select('tm_parts.qty_limit','tm_parts.part_name', 'tm_parts.part_number', 'tm_parts.back_number',DB::raw('SUM(current_stock) as current_stock'))
                ->where('tm_parts.status', 2)
                ->groupBy('tm_parts.part_name')
                ->get();
        
        return $result;
    }

    public function fgAssyGetTransaction()
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

            dd($recordsFiltered);

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
     * Display DC dashboard
     *
     * 
     */
    public function fgDc()
    {
        return view('layouts.fg.fg-dc');
    }

    public function getPartMa()
    {
        // get all partnum each model
    }
    /**
     * Display MA dashboard
     *
     * 
     */
    public function fgMa()
    {
        return view('layouts.fg.fg-ma');
    }
    /**
     * Display ASSY dashboard
     *
     * 
     */
    public function fgAssy()
    {
        return view('layouts.fg.fg-assy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('layouts.fg-dashboard');
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