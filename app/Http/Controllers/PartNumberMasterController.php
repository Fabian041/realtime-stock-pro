<?php

namespace App\Http\Controllers;

use App\Models\TmPart;
use App\Models\TmPartNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PartNumberMasterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('layouts.master.partNumber-master',[
            'parts' => TmPart::all(),
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

        $validatedData = $request->validate([
            'part_name' => 'required',
            'part_number' => 'required|unique:tm_parts|min:12|max:12',
            'back_number' => 'required|unique:tm_parts|min:4|max:4',
            'status' => 'required',
            'qty_limit' => 'required'
        ]);

        TmPart::create($validatedData);
        
        return redirect()->back()->with('success', 'Part created successfully.');
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
    public function update(Request $request, TmPart $part)
    {
        $rules = [];

        if($request->part_name !== $part->part_name){
            $rules['part_name'] = 'required';
        }else if($request->back_number !== $part->back_number){
            $rules['back_number'] ='required';
        }else if($request->part_number !== $part->part_number){
            $rules['part_number'] ='required';
        }else if($request->qty_limit !== $part->qty_limit){
            $rules['qty_limit'] ='required';
        }else if($request->status !== $part->status){
            $rules['status'] ='required';
        }

        try {
            DB::beginTransaction();
            $validatedData = $request->validate($rules);

            TmPart::where('id', $part->id)->update($validatedData);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update' . '[' . $th->getMessage() . ']');

        }

        return redirect()->back()->with('success', 'Part Information has been updated successfully');
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
    
    public function getData()
    {
        $input = TmPart::all();
        return DataTables::of($input)
                ->addColumn('edit', function($row) use ($input){

                    $btn = '<button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#edit-'. $row->id .'"><span class="d-none d-sm-inline-block">Edit</span></button>';

                    return $btn;

                })
                ->rawColumns(['edit'])
                ->toJson();
    }
}