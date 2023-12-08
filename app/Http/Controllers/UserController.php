<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;

class UserController extends Controller
{
    // public function login(Request $request) {
    //     //dd($request);
    //     $validated = $request->validate([
    //         'username' => 'required',
    //         'password' => 'required|min:8'
    //     ]);
    //     return response()->json(['success'=>'Successfully']);
    // }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required',
            'password' => 'required|min:8'
        ]);

        $user = User::where('email', $request->get('username'))->first();

        if (!$user) {
            return response()->json(['error' => 'Email Not Found!'], 401);
        }
        $user_data = array(
            'email' => $request->get('username'),
            'password' => $request->get('password')
        );

        if (Auth::attempt($user_data)) {
            echo json_encode(array('url' => '/'));
        } else {
            return response()->json(['error' => 'Invalid credentials!'], 401);
        }
    }


    public function home(Request $request)
    {
        //echo bcrypt('1234567890');
        //dd(Crypt::decryptString('$2y$10$2OhRE\/zTnRX3OJIfUcBrAuySK375QJf0F2WarzkB3bRor7TYWRdj2'));

        if (Auth::check()) {
            return view('home')->with('user', Auth::user());
        } else {
            return view('home');
        }
    }

    public function logout()
    {
        return redirect('/')->with(Auth::logout());
    }

    public function downloadEcard()
    {
        if (Auth::check()) {
            $arr = [
                "username" => "AGSW4",
                "password" => "AGSW@#4",
                "policyno" => "P0023100023/6115/100051",
                "employeecode" => "EL-0676"
            ];
            $response = Http::withBody(json_encode($arr), 'text/json')
                ->post('http://brokerapi.safewaytpa.in/api/EcardEmpMember')->json();
            if ($response['Status'] == 1) {
                //return '<script>window.open("' . $response['E_Card'] . '", "")</script>';
                return redirect($response['E_Card']);
            }
        } else {
            return redirect('/');
        }
    }
}
