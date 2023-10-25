<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    public function login(Request $request) {
        //dd($request);
        $validated = $request->validate([
            'username' => 'required',
            'password' => 'required|min:8'
        ]);

        $user_data = array(
            'email'  => $request->get('username'),
            'password' => $request->get('password')
        );

        //dd($user_data);
        dd(Auth::attempt($user_data));

        if (Auth::attempt($user_data)) {
            // return redirect()->intended('dashboard')
            //             ->withSuccess('Signed in');
            echo 'user login success';
        }
    
        
        //return back()->withErrors(['invalid credentials']);
    }

}

