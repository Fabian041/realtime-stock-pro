<?php

namespace App\Http\Controllers;

use App\Models\TmPart;
use App\Models\TmPartNumber;
use Illuminate\Http\Request;
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
        return view('layouts.master.partNumber-master');
        
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
    
    public function getData()
    {
        $input = TmPart::all();
        return DataTables::of($input)
                ->toJson();
    }
}