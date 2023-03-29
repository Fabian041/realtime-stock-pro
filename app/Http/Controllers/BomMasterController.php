<?php

namespace App\Http\Controllers;

use App\Models\TmBom;
use App\Models\TmArea;
use App\Models\TmPart;
use App\Models\TmMaterial;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class BomMasterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('layouts.master.bom-master',[
            'parts' => TmPart::all(),
            'materials' => TmMaterial::all(),
            'areas' => TmArea::all(),
        ]);
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
     * @param  \App\Models\TmBom  $tmBom
     * @return \Illuminate\Http\Response
     */
    public function show(TmBom $tmBom)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TmBom  $tmBom
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TmBom $tmBom)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TmBom  $tmBom
     * @return \Illuminate\Http\Response
     */
    public function destroy(TmBom $tmBom)
    {
        //
    }

    public function import(Request $request)
    {

        Excel::import(new TmMaterialImport, $request->file('file')->store('files'));

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

    /**
     * getData Function
     * 
     */
    public function getData()
    {
        $input = TmBom::all();
        return DataTables::of($input)
                ->toJson();
    }
}
