<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TmTransaction;
use Yajra\DataTables\Facades\DataTables;

class TransactionMasterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('layouts.master.transaction-master');
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
            'name' => 'required',
            'code' => 'required|unique:tm_transactions,code',
            // 'status' => 'required'
        ]);

        TmTransaction::create([
            'name' => $validatedData['name'],
            'code' => $validatedData['code'],
            'status' => 'plus'
        ]);

        TmTransaction::create([
            'name' => $validatedData['name'] . ' (R)',
            'code' => $validatedData['code'] + 1,
            'status' => 'minus'
        ]);

        return redirect()->back()->with('success', 'Transaction created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TmTransaction  $tmTransaction
     * @return \Illuminate\Http\Response
     */
    public function show(TmTransaction $tmTransaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TmTransaction  $tmTransaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TmTransaction $tmTransaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TmTransaction  $tmTransaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(TmTransaction $tmTransaction)
    {
        //
    }

    public function getData()
    {
        $input = TmTransaction::all();
        return DataTables::of($input)
                ->toJson();
    }
}
