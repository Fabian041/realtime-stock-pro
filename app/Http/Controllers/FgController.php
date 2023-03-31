<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FgController extends Controller
{
    
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
