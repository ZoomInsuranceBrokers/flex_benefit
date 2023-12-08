<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Mail\PasswordResetMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;


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


    public function showForm()
    {
        if (Auth::check()) {
            return view('home')->with('user', Auth::user());
        } else {
            return view('auth.forgot-password');
        }
    }

    public function sendResetLinkEmail(Request $request)
    {

        $validated = $request->validate([
            'email' => 'required',
        ]);

        $email = $request->input('email');

        $user = DB::table('users')->where('email', $email)->first();

        if (!$user) {
            return redirect()->route('password.request')->with('message', 'Invalid email');
        }


        $token = Str::random(60);

        DB::table('users')->where('email', $email)->update(['remember_token' => $token]);

        // Mail::to($email)->send(new PasswordResetMail($token));

        return redirect()->route('password.request')->with('message', 'Password reset link sent!');
    }

    public function showResetForm($token)
    {
        $user = DB::table('users')->where('remember_token', $token)->first();

        if (!$user) {
            abort(404);
        }

        return view('auth.reset-password', ['token' => $token]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'password' => ['required', 'confirmed', Password::defaults(), 'min:6'],
            'password_confirmation' => 'required',
        ], [
            'password.min' => 'The password must be at least 6 characters.',
            'password.confirmed' => 'The password confirmation does not match.',
        ]);


        $token = $request->input('token');
        $password = $request->input('password');

        $user = DB::table('users')->where('remember_token', $token)->first();

        if (!$user) {
            return back()->with('message', 'Invalid token');
        }

        DB::table('users')
            ->where('remember_token', $token)
            ->update(['password' => Hash::make($password), 'remember_token' => null]);

        $user_data = array(
            'email' => $user->email,
            'password' => $request->input('password')
        );

        if (Auth::attempt($user_data)) {
            return redirect()->route('home');
        } else {
            return response()->json(['error' => 'Invalid credentials!'], 401);
        }
    }
}
