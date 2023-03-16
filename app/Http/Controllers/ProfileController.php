<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('layouts.auth.profile');
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
    public function update(Request $request)
    {
        $rules = [
            'current-password' => 'required',
            'new-password' => 'required|min:6|confirmed',
        ];

        if($request->email != auth()->user()->email){
            $rules['email'] = 'required|email:dns|unique:users|max:255';    
        }
        
        if(!Hash::check($request->get('current-password'), Auth::user()->password)) {
            //if current pass doesnt match the record in database
            return redirect()->back()->with('error', 'Your current password does not match with our record');
        }

        if(strcmp($request->get('current-password'), $request->get('new-password')) == 0 ){
            //strcmp is used to compare string and return integer
            //if the result == 0 || both string are same
            return redirect()->back()->with('error', 'New Password cannot be same as your current password!');
        }

        $validatedData = $request->validate($rules);

        // update password in database based on username
        $userId = auth()->user()->id;

        DB::table('users')
            ->where('id' , $userId )
            ->update([
                'password' => Hash::make($validatedData['new-password']),
                'email' => $request->get('email')
            ]);
        
        return redirect()->back()->with("updated", "Your data has been updated!");
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
