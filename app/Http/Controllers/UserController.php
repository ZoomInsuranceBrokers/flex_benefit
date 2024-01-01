<?php

namespace App\Http\Controllers;

use DateTime;
use App\Models\User;
use App\Models\Account;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\PasswordResetMail;
use App\Models\MapUserFYPolicy;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rules\Password;



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
        $accountData = Account::all()->toArray();
        $todayDate       = new DateTime(); // Today
        $enrollmentDateBegin = new DateTime($accountData[0]['enrollment_start_date']);
        $enrollmentDateEnd = new DateTime($accountData[0]['enrollment_end_date']);
        session(['is_enrollment_window' => false]);
        if ($todayDate >= $enrollmentDateBegin && $todayDate < $enrollmentDateEnd) {
            session(['is_enrollment_window' => true]);
        }

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
            $policy_number = "323700/34/22/04/00000027";
            $emp_id = "10447";

            $curl = curl_init();

            $data = json_encode(
                array(
                    "USERNAME" => "ZOOM-ADMIN",
                    "PASSWORD" => "ADMIN-USER@389",
                    "POLICY_NUMBER" => $policy_number,
                    "EMPLOYEE_NUMBER" => $emp_id,
                )
            );

            curl_setopt_array(
                $curl,
                array(
                    CURLOPT_URL => 'https://webintegrations.paramounttpa.com/ZoomBrokerAPI/Service1.svc/GetFamilyECard',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_HTTPHEADER => array('Content-Type:application/json'),
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => $data,
                )
            );

            $response = curl_exec($curl);

            curl_close($curl);

            $apilog = array(
                'tpa_company' => 'phs',
                'request' => $data,
                'response' => $response
            );

            $response = json_decode($response);

            if ($response->GetFamilyECardResult[0]->STATUS == 'SUCCESS') {

                $url = $response->GetFamilyECardResult[0]->E_Card;
                $headers = [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="E_Card.pdf"',
                ];
                return Redirect::to($url, 302, $headers);
            } else {
                echo 'Something Went Wrong! Kindly Try again Later';
                exit;
            }
            // $arr = [
            //     "username" => "AGSW4",
            //     "password" => "AGSW@#4",
            //     "policyno" => "P0023100023/6115/100051",
            //     "employeecode" => "EL-0676"
            // ];
            // $response = Http::withBody(json_encode($arr), 'text/json')
            //     ->post('http://brokerapi.safewaytpa.in/api/EcardEmpMember')->json();
            // if ($response['Status'] == 1) {
            //     //return '<script>window.open("' . $response['E_Card'] . '", "")</script>';
            //     return redirect($response['E_Card']);
            // }
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

        DB::table('users')->where('email', $email)->update([
            'remember_token' => $token,
            'updated_at' => now()
        ]);

        Mail::to($email)->send(new PasswordResetMail($token, $user));

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
            ->update(['password' => Hash::make($password),
            'remember_token' => null,
            'updated_at' => now()]);

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

    public function passworReset()
    {

        return view('auth.reset-password-auth');
    }

    public function updatePassword(Request $request)
    {

        $request->validate([
            'old-password' => 'required',
            'password' => ['required', 'confirmed', Password::defaults(), 'min:6'],
            'password_confirmation' => 'required',
        ], [
            'password.min' => 'The password must be at least 6 characters.',
            'password.confirmed' => 'The password confirmation does not match.',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->input('old-password'), $user->password)) {
            return redirect()->back()->withErrors(['old-password' => 'The provided old password does not match your current password.']);
        }

        $user->update([
            'password' => Hash::make($request->input('password')),
            'updated_at' => now()
        ]);

        return redirect()->route('previous.form.route')->with('status', 'Password updated successfully.');
    }

    public function viewSummary()
    {
        if (Auth::check()) {

            $mapUserFYPolicyData = MapUserFYPolicy::where('is_active', true)
                ->with(['fyPolicy'])
                ->get()->toArray();

            dd($mapUserFYPolicyData);
            return view('view-summary',['mapUserFYPolicyData' => $mapUserFYPolicyData ]);
        } else {
            return view('auth.forgot-password');
        }
    }
}
