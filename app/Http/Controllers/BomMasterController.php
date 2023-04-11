<?php

namespace App\Http\Controllers;

use Pusher\Pusher;
use App\Models\TmBom;
use App\Models\TmArea;
use App\Models\TmPart;
use App\Models\TmMaterial;
use App\Imports\TmBomImport;
use Illuminate\Http\Request;
use App\Imports\TmMaterialImport;
use Illuminate\Support\Facades\DB;
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
            'boms'  => TmBom::all(),
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
        try {
            $validatedData = $request->validate([
                'id_part' => 'required',
                'id_area' => 'required',
                'id_material' => 'required',
                'qty_use' => 'required',
                'uom' => 'required'
            ]);
    
            TmBom::create($validatedData);
            
            return redirect()->back()->with('success', 'Part created successfully.');

        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage()); 
        }
        
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
    public function update(Request $request, TmBom $bom)
    {
        $rules = [];

        if($request->id_part !== $bom->id_part){
            $rules['id_part'] = 'required';
        }else if($request->id_material !== $bom->id_material){
            $rules['id_material'] ='required';
        }else if($request->id_area !== $bom->id_area){
            $rules['id_area'] ='required';
        }else if($request->qty_use !== $bom->qty_use){
            $rules['qty_use'] ='required';
        }else if($request->uom !== $bom->uom){
            $rules['uom'] ='required';
        }

        try {
            DB::beginTransaction();
            $validatedData = $request->validate($rules);

            TmBom::where('id', $bom->id)->update($validatedData);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update' . '[' . $th->getMessage() . ']');

        }

        return redirect()->back()->with('success', 'BOM has been updated successfully');
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

        try {
            Excel::import(new TmBomImport, $request->file('file')->store('files'));

            return redirect()->back()->with('success', 'Berhasil menambah stock');
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    /**
     * getData Function
     * 
     */
    public function getData()
    {
        $input =  DB::table('tm_boms')
                ->join('tm_materials', 'tm_boms.id_material', '=', 'tm_materials.id')
                ->join('tm_areas', 'tm_boms.id_area', '=', 'tm_areas.id')
                ->join('tm_parts', 'tm_boms.id_part', '=', 'tm_parts.id')
                ->select('tm_boms.id','tm_parts.part_name','tm_areas.name','tm_parts.part_number', 'tm_materials.part_number as material_number' , 'tm_boms.qty_use', 'tm_materials.part_name as material_name')
                ->get();

        return DataTables::of($input)
                ->addColumn('edit', function($row) use ($input){

                    $btn = '<button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#edit-'. $row->id .'"><span class="d-none d-sm-inline-block">Edit</span></button>';

                    return $btn;

                })
                ->rawColumns(['edit'])
                ->toJson();
    }
}