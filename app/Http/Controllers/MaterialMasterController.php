<?php

namespace App\Http\Controllers;

use Pusher\Pusher;
use App\Models\TtStock;
use App\Models\TmMaterial;
use Illuminate\Http\Request;
use App\Imports\ImportTtStock;
use App\Imports\TmMaterialImport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class MaterialMasterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('layouts.master.material-master',[
            'materials' => TmMaterial::all()
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
    public function update(Request $request, TmMaterial $material)
    {
        $rules = [];

        if($request->part_number !== $material->part_number){
            $rules['part_number'] = 'required';
        }else if($request->part_name !== $material->part_name){
            $rules['part_name'] ='required';
        }else if($request->source !== $material->source){
            $rules['source'] ='required';
        }else if($request->supplier !== $material->supplier){
            $rules['supplier'] ='required';
        }else if($request->limit_qty !== $material->limit_qty){
            $rules['limit_qty'] ='required';
        }

        try {
            DB::beginTransaction();
            $validatedData = $request->validate($rules);

            TmMaterial::where('id', $material->id)->update($validatedData);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update' . '[' . $th->getMessage() . ']');

        }

        return redirect()->back()->with('success', 'Part has been updated successfully');
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

    /**
     * Import Function
     * 
     */
    public function import(Request $request)
    {
        Excel::import(new TmMaterialImport, $request->file('file')->store('files'));

        return redirect()->back()->with('success', 'Success mastering material data!');
    }

    /**
     * getData Function
     * 
     */
    public function getData()
    {
        $input = TmMaterial::all();
        return DataTables::of($input)
                ->addColumn('edit', function($row) use ($input){

                    $btn = '<button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#edit-'. $row->id .'"><span class="d-none d-sm-inline-block">Edit</span></button>';

                    return $btn;

                })
                ->rawColumns(['edit'])
                ->toJson();
    }
}