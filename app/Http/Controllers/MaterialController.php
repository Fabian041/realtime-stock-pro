<?php

namespace App\Http\Controllers;

use App\Models\TtStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ckd = TtStock::where('source', 'like', '%CKD%')->sum('qty');
        $import = TtStock::where('source', 'like', '%IMPORT%')->sum('qty');
        $local = TtStock::where('source', 'like', '%LOCAL%')->sum('qty');

        return view('layouts.material-dashboard',[
            'ckd' => $ckd,
            'import' => $import,
            'local' => $local,
        ]);
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

    public function getCkdMaterial(){

        $data = DB::table('tm_parts')->join('tt_stocks','tm_parts.id', '=', 'tt_stocks.id_part')
                ->select('part_name','qty_limit','source', 'qty')
                ->where('source', 'like', '%CKD%')
                ->groupBy('id_part')
                ->get();

        return response()->json($data);

    }
}
