<?php

namespace App\Http\Controllers;

use DateTime;
use App\Models\User;
use App\Models\Grade;
use App\Models\Account;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\PasswordResetMail;
use App\Models\CountryCurrency;
use App\Models\MapUserFYPolicy;
use Illuminate\Support\Facades\DB;
use App\Mail\NewJoiningCredentials;
use App\Traits\dependantTraitMethods;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Crypt;
use App\Traits\EnrollmentTraitMethods;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rules\Password;


class UserController extends Controller
{
    use EnrollmentTraitMethods;
    use dependantTraitMethods;
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
            $currentDate = now();

            $policy_details = DB::table('policy_master')
                ->whereDate('policy_start_date', '<=', $currentDate)
                ->whereDate('policy_end_date', '>=', $currentDate)
                ->first();

            // $user = User::where('id',240)->first();

            // Mail::to($user->email)->send(new NewJoiningCredentials($user));


            switch ($policy_details->tpa_id) {
                case 62:
                    $policy_number = $policy_details->policy_number;
                    $emp_id = Auth::user()->employee_id;

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

                    $response = json_decode($response);
                   
                    if (isset($response->GetFamilyECardResult)  && $response->GetFamilyECardResult[0]->STATUS == 'SUCCESS') {
                        $url = $response->GetFamilyECardResult[0]->E_Card;

                        header('Content-Type: application/pdf');

                        // Output the file content directly
                        readfile($url);

                        echo "
                            <!DOCTYPE html>
                            <html lang='en'>
                            <head>
                                <meta charset='UTF-8'>
                                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                                <style>
                                    body {
                                        font-family: 'Arial', sans-serif;
                                        display: flex;
                                        align-items: center;
                                        justify-content: center;
                                        height: 100vh;
                                        margin: 0;
                                        background-color: #f4f4f4;
                                    }

                                    .message-box {
                                        background-color: #4CAF50;
                                        color: white;
                                        padding: 20px;
                                        text-align: center;
                                        border-radius: 8px;
                                        box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
                                    }
                                </style>
                            </head>
                            <body>
                                <div class='message-box'>
                                    <h1>E Card Download successful!</h1>
                                </div>
                            </body>
                            </html>
                        ";
                    } else {
                        echo 'Something Went Wrong! Kindly Try again Later';
                        exit;
                    }
                    break;
                default:
                    echo "TPA INTEGRATION IS IN PROCESS";
                    exit;
                    break;
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
            ->update([
                'password' => Hash::make($password),
                'remember_token' => null,
                'updated_at' => now()
            ]);

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

            $mapUserFYPolicyData = MapUserFYPolicy::where('user_id_fk', Auth::user()->id)
                ->where('is_active', true)
                ->with(['fyPolicy'])
                ->get()->toArray();

            return view('view-summary', ['mapUserFYPolicyData' => $mapUserFYPolicyData]);
        } else {
            return view('auth.forgot-password');
        }
    }

    public function createUsers(Request $request) {
        // if (
        //     $request->isMethod('get') && $request->has('authKey') &&
        //     $request->authKey == base64_encode(env('APP_API_SECRET_KEY') . '@' . date('d-m-Y'))
        // ) 
        {
            if ($request->has('confirmUpdate') && $request->confirmUpdate) {
                session(['confirmUpdate'=> true]);
            } else {
                session(['confirmUpdate' => false]);
            }
            $testJson = '[ { "Dependants": [ { "Approval_Status_c": "Approved", "Deceasedc": "No", "Date_of_Birthc": "1999-01-03 00:00:00", "Relationship_Typec": "Self", "Namec": "scsdca Garg", "Employeec": "003U8000002MqdnIAC", "LastModifiedDate": "2024-01-21 11:55:20", "Name": "D-02719", "Id": "a0DU8000000AjDaMAK" } ], "Details": { "Points_Allottedc": 5000, "Genderc": "Male", "Designationc": "Senior Executive", "Nominee_Percentagec": 0, "Gradec": "NA", "Hire_Datec": "2000-09-11", "Employee_Id_c": "23223", "LastModifiedById": "005Hs00000CbkOnIAJ", "LastModifiedDate": "2024-01-21T13:33:32.000Z", "CreatedById": "005Hs00000CbkOnIAJ", "CreatedDate": "2024-01-21T11:55:19.000Z", "Birthdate": "1987-01-03", "Email": "vivek.garg@343345345.com.dummy", "MailingCountryCode": "IN", "MailingCountry": "India", "Name": "2323 Garg", "FirstName": "2323", "LastName": "Garg", "Id": "003U8000002MqdnIAC" } }, 

            {"Dependants":[{"Approval_Status_c":"Approved","Deceasedc":"No","Date_of_Birthc":"1999-01-03 00:00:00","Relationship_Typec":"Self","Namec":"scsdca Garg","Employeec":"003U8000002MqdnIAC","LastModifiedDate":"2024-01-21 11:55:20","Name":"D-02719","Id":"a0DU8000000AjDaMAK"}],"Details":{"FB_Window_End_Datec":"2024-02-06","FB_Window_Start_Datec":"2024-01-23","Points_Allottedc":5000,"Genderc":"Male","Designationc":"Senior Executive","Nominee_Percentagec":0,"Gradec":"NA","Hire_Datec":"2000-09-11","Employee_Id_c":"23223","LastModifiedById":"005Hs00000CbkOnIAJ","LastModifiedDate":"2024-01-21T13:38:53.000Z","CreatedById":"005Hs00000CbkOnIAJ","CreatedDate":"2024-01-21T11:55:19.000Z","Birthdate":"1987-01-03","Email":"vivek.garg@343345345.com.dummy","MailingCountryCode":"IN","MailingCountry":"India","Name":"2323 Garg","FirstName":"2323","LastName":"Garg","Id":"003U8000002MqdnIAC"}}
            
            ]';
            $testJson = '{
                "status":"SUCCESS",
                "details":"{\"003UN000001Ov3wYAC\":{\"Details\":{\"Id\":\"003UN000001Ov3wYAC\",\"LastName\":\"Kabdal\",\"FirstName\":\"Naveen\",\"MiddleName\":\"Chand\",\"Name\":\"Naveen Chand Kabdal\",\"MailingCountry\":\"India\",\"MailingCountryCode\":\"IN\",\"Email\":\"payable@zoominsurancebrokers.com.dummy\",\"Birthdate\":\"1998-03-20\",\"CreatedDate\":\"2023-12-09T11:01:49.000Z\",\"CreatedById\":\"005Hs00000CbkOnIAJ\",\"LastModifiedDate\":\"2024-01-28T01:55:00.000Z\",\"LastModifiedById\":\"005Hs00000CbkOnIAJ\",\"Employee_Id_c\":\"10417\",\"Hire_Datec\":\"2023-05-01\",\"Gradec\":\"NA\",\"Nominee_Percentagec\":100,\"Designationc\":\"Senior Executive\",\"Genderc\":\"Male\",\"Points_Allottedc\":5000,\"FB_Window_Start_Datec\":\"2024-01-12\",\"FB_Window_End_Datec\":\"2024-02-07\"},\"Dependants\":[{\"Id\":\"a0DU80000009F0SMAU\",\"Name\":\"D-02220\",\"LastModifiedDate\":\"2024-01-21 09:19:42\",\"Employeec\":\"003UN000001Ov3wYAC\",\"Namec\":\"Basanti\",\"Relationship_Typec\":\"Mother\",\"Date_of_Birthc\":\"1976-07-10 00:00:00\",\"Nominee_Percentagec\":\"100\",\"Deceasedc\":\"Yes\",\"Approval_Statusc\":\"Approved\",\"Dependant_Codec\":\"Parents\"}]},\"003UN000001Ov2UYAS\":{\"Details\":{\"Id\":\"003UN000001Ov2UYAS\",\"LastName\":\"Aggarwal\",\"FirstName\":\"Ritesh\",\"Name\":\"Ritesh Aggarwal\",\"MailingCountry\":\"India\",\"MailingCountryCode\":\"IN\",\"Email\":\"ritesh.aggarwal@zoominsurancebrokers.com.dummy\",\"Birthdate\":\"1990-11-15\",\"CreatedDate\":\"2023-12-09T11:01:49.000Z\",\"CreatedById\":\"005Hs00000CbkOnIAJ\",\"LastModifiedDate\":\"2024-01-28T01:55:43.000Z\",\"LastModifiedById\":\"005Hs00000CbkOnIAJ\",\"Employee_Idc\":\"10347\",\"Hire_Datec\":\"2021-12-01\",\"Gradec\":\"NA\",\"Nominee_Percentagec\":200,\"Designationc\":\"Sr. Manager\",\"Genderc\":\"Male\",\"Points_Allottedc\":5000},\"Dependants\":[{\"Id\":\"a0DU80000009ExcMAE\",\"Name\":\"D-02044\",\"LastModifiedDate\":\"2024-01-21 09:19:41\",\"Employeec\":\"003UN000001Ov2UYAS\",\"Namec\":\"Shailja Pahariwal\",\"Relationship_Typec\":\"Spouse\",\"Date_of_Birthc\":\"1992-06-18 00:00:00\",\"Nominee_Percentagec\":\"100\",\"Deceasedc\":\"No\",\"Approval_Statusc\":\"Approved\",\"Dependant_Codec\":\"Spouse\"},{\"Id\":\"a0DU80000009ExdMAE\",\"Name\":\"D-02045\",\"LastModifiedDate\":\"2024-01-21 09:19:41\",\"Employeec\":\"003UN000001Ov2UYAS\",\"Namec\":\"Shailja Aggarwal\",\"Relationship_Typec\":\"Spouse\",\"Date_of_Birthc\":\"1992-06-19 00:00:00\",\"Nominee_Percentagec\":\"0\",\"Deceasedc\":\"Yes\",\"Approval_Statusc\":\"Approved\",\"Dependant_Codec\":\"Spouse\"},{\"Id\":\"a0DU80000009ExeMAE\",\"Name\":\"D-02046\",\"LastModifiedDate\":\"2024-01-21 09:19:41\",\"Employeec\":\"003UN000001Ov2UYAS\",\"Namec\":\"Mukesh Aggarwal\",\"Relationship_Typec\":\"Father\",\"Date_of_Birthc\":\"1955-11-01 00:00:00\",\"Nominee_Percentagec\":\"50\",\"Deceasedc\":\"Yes\",\"Approval_Statusc\":\"Approved\",\"Dependant_Codec\":\"Parents\"},{\"Id\":\"a0DU80000009ExfMAE\",\"Name\":\"D-02047\",\"LastModifiedDate\":\"2024-01-21 09:19:41\",\"Employeec\":\"003UN000001Ov2UYAS\",\"Namec\":\"Aruna Aggarwal\",\"Relationship_Typec\":\"Mother\",\"Date_of_Birthc\":\"1957-03-01 00:00:00\",\"Nominee_Percentagec\":\"50\",\"Deceasedc\":\"Yes\",\"Approval_Statusc\":\"Approved\",\"Dependant_Codec\":\"Parents\"}]},\"003U8000002Rvc9IAC\":{\"Details\":{\"Id\":\"003U8000002Rvc9IAC\",\"LastName\":\"Rai\",\"FirstName\":\"Sawani\",\"Name\":\"Sawani Rai\",\"MailingCountry\":\"India\",\"MailingCountryCode\":\"IN\",\"Email\":\"sawani.rai@zoominsurancebrokers.com.dummy\",\"Birthdate\":\"1995-07-01\",\"CreatedDate\":\"2024-01-24T12:53:02.000Z\",\"CreatedById\":\"005Hs00000CbkOnIAJ\",\"LastModifiedDate\":\"2024-01-24T13:00:12.000Z\",\"LastModifiedById\":\"005Hs00000CbkOnIAJ\",\"Employee_Idc\":\"MT0001\",\"Hire_Datec\":\"2024-01-03\",\"Gradec\":\"Management Trainee\",\"Nominee_Percentagec\":0,\"Designationc\":\"Management Trainee\",\"Genderc\":\"Female\",\"Points_Allottedc\":5000},\"Dependants\":[]},\"003U8000002Rt1RIAS\":{\"Details\":{\"Id\":\"003U8000002Rt1RIAS\",\"LastName\":\"Singh\",\"FirstName\":\"Praveen\",\"MiddleName\":\"Kumar\",\"Name\":\"Praveen Kumar Singh\",\"MailingCountry\":\"India\",\"MailingCountryCode\":\"IN\",\"Email\":\"praveen.singh@zoominsurancebrokers.com.dummy\",\"Birthdate\":\"1986-08-18\",\"CreatedDate\":\"2024-01-24T12:48:23.000Z\",\"CreatedById\":\"005Hs00000CbkOnIAJ\",\"LastModifiedDate\":\"2024-01-24T13:02:07.000Z\",\"LastModifiedById\":\"005Hs00000CbkOnIAJ\",\"Employee_Idc\":\"10473\",\"Hire_Datec\":\"2024-01-19\",\"Gradec\":\"NA\",\"Nominee_Percentagec\":0,\"Designationc\":\"AVP\",\"Genderc\":\"Male\",\"Points_Allottedc\":5000},\"Dependants\":[]},\"003U8000002Rt1QIAS\":{\"Details\":{\"Id\":\"003U8000002Rt1QIAS\",\"LastName\":\"Behal\",\"FirstName\":\"Parul\",\"Name\":\"Parul Behal\",\"MailingCountry\":\"India\",\"MailingCountryCode\":\"IN\",\"Email\":\"parul.behal@zoominsurancebrokers.com.dummy\",\"Birthdate\":\"1980-10-08\",\"CreatedDate\":\"2024-01-24T12:48:23.000Z\",\"CreatedById\":\"005Hs00000CbkOnIAJ\",\"LastModifiedDate\":\"2024-01-24T13:02:28.000Z\",\"LastModifiedById\":\"005Hs00000CbkOnIAJ\",\"Employee_Idc\":\"C0004\",\"Hire_Datec\":\"2024-01-15\",\"Gradec\":\"NA\",\"Nominee_Percentagec\":0,\"Designationc\":\"Head\",\"Genderc\":\"Male\",\"Points_Allottedc\":5000},\"Dependants\":[]},\"003U8000002Rt1PIAS\":{\"Details\":{\"Id\":\"003U8000002Rt1PIAS\",\"LastName\":\"Taank\",\"FirstName\":\"Ankit\",\"MiddleName\":\"Kumar\",\"Name\":\"Ankit Kumar Taank\",\"MailingCountry\":\"India\",\"MailingCountryCode\":\"IN\",\"Email\":\"ankit.kumar@zoominsurancebrokers.com.dummy\",\"Birthdate\":\"1992-06-20\",\"CreatedDate\":\"2024-01-24T12:48:23.000Z\",\"CreatedById\":\"005Hs00000CbkOnIAJ\",\"LastModifiedDate\":\"2024-01-28T01:53:46.000Z\",\"LastModifiedById\":\"005Hs00000CbkOnIAJ\",\"Employee_Idc\":\"10472\",\"Hire_Datec\":\"2024-01-16\",\"Gradec\":\"NA\",\"Nominee_Percentagec\":0,\"Designationc\":\"Asst. Manager\",\"Genderc\":\"Male\",\"Points_Allottedc\":5000},\"Dependants\":[]},\"003U8000002Rt1OIAS\":{\"Details\":{\"Id\":\"003U8000002Rt1OIAS\",\"LastName\":\"Garg\",\"FirstName\":\"Puja\",\"Name\":\"Puja Garg\",\"MailingCountry\":\"India\",\"MailingCountryCode\":\"IN\",\"Email\":\"10471dummy@dummy.com.dummy\",\"Birthdate\":\"1991-03-03\",\"CreatedDate\":\"2024-01-24T12:48:23.000Z\",\"CreatedById\":\"005Hs00000CbkOnIAJ\",\"LastModifiedDate\":\"2024-01-24T13:01:25.000Z\",\"LastModifiedById\":\"005Hs00000CbkOnIAJ\",\"Employee_Idc\":\"10471\",\"Hire_Datec\":\"2024-01-02\",\"Gradec\":\"NA\",\"Nominee_Percentagec\":0,\"Designationc\":\"Senior Executive\",\"Genderc\":\"Female\",\"Points_Allotted_c\":5000},\"Dependants\":[]}}"
             }';
            //$userJsonData = json_decode(base64_decode($request->uDJson));
            //dd(json_decode(json_decode($testJson, true)['details'], true));
            $jsonData = json_decode(json_decode($testJson, true)['details'], true);

            dd($jsonData);
            if (count($jsonData)) {
                if (array_key_exists('account', $jsonData)) {
                    // enter account entry
                }
                // GRADE SECTION
                if (array_key_exists('grades', $jsonData)) {
                    // enter grades entry
                }
                $grades = Grade::where('is_active', true)->select('id', 'grade_name')->get()->toArray();
                session(['grades' => $this->_getGradeArray($grades)]);
                // USER CREATION
                $userInsertData = $this->_saveUserData($jsonData);
                // Dependent Creation
                //dd($userInsertData , $jsonData);
                $this->_saveDependantData($userInsertData, $jsonData);
            } else {
                echo 'Empty User Json Data, No User Created!!!';
            }
        }
    }

    private function _getGenderId($genderText, $rowData) {
        if (strlen($genderText)) {
            $genderCode = config('constant.$_GENDER_OTHER');
            switch(strtolower($genderText)) {
                case 'm':
                case 'male': {
                    $genderCode = config('constant.$_GENDER_MALE');
                    break;
                }
                case 'f':
                case 'female': {
                    $genderCode = config('constant.$_GENDER_FEMALE');
                    break;
                }
            }
            return $genderCode;            
        } else {
            die(__FUNCTION__ . ':ERROR:Invalid Gender for user record: ' . $rowData['FirstName'] . ' ' . $rowData['LastName'] . '[' . $rowData['Employee_Id_c'] . ']');
        }
    }

    private function _getGradeArray($existingGrades) {
        $gradeData = [];
        if (count($existingGrades)) {
            foreach($existingGrades as $gData) {
                $gradeData[$gData['id']] = strtolower($gData['grade_name']);
            }
            return $gradeData;
        } else {
            die(__FUNCTION__ . ':ERROR: No grade data found!!');
        }
    }

    private function _saveUserData($jsonData){
        $formattedData = [];
        if (count($jsonData)) {
            // user data
            foreach ($jsonData as $jsonRow) {
                $formattedData['user'][$jsonRow['Details']['Id']]['external_id'] = htmlspecialchars($jsonRow['Details']['Id']); 
                $formattedData['user'][$jsonRow['Details']['Id']]['fname'] = htmlspecialchars(strlen($jsonRow['Details']['FirstName']) ? 
                $jsonRow['Details']['FirstName'] : $jsonRow['Details']['LastName']); 
                $formattedData['user'][$jsonRow['Details']['Id']]['lname'] = htmlspecialchars($jsonRow['Details']['LastName']); 
                
                array_key_exists('MiddleName',$jsonRow['Details']) ?
                    $formattedData['user'][$jsonRow['Details']['Id']]['mname'] = htmlspecialchars($jsonRow['Details']['MiddleName']) : ''; 

                $employeeId = array_key_exists('Employee_Id_c', $jsonRow['Details']) ? $jsonRow['Details']['Employee_Id_c'] : $jsonRow['Details']['Employee_Idc'];
                $formattedData['user'][$jsonRow['Details']['Id']]['employee_id'] = htmlspecialchars($employeeId); 
                
                // GRADE
                $gradeId = array_search($jsonRow['Details']['Designationc'], session('grades'));
                $formattedData['user'][$jsonRow['Details']['Id']]['grade_id_fk'] = $gradeId ? $gradeId : array_search('na', session('grades'));

                $formattedData['user'][$jsonRow['Details']['Id']]['dob'] = htmlspecialchars($jsonRow['Details']['Birthdate']); 
                $formattedData['user'][$jsonRow['Details']['Id']]['hire_date'] = htmlspecialchars($jsonRow['Details']['Hire_Datec']); 
                // $formattedData['user'][$jsonRow['Details']['Id']]['salary'] = htmlspecialchars($jsonRow['Details']['salary']); @todo
                $formattedData['user'][$jsonRow['Details']['Id']]['points_used'] = 0; 

                $pointsAlloted = array_key_exists('Points_Allottedc', $jsonRow['Details']) ? $jsonRow['Details']['Points_Allottedc'] : $jsonRow['Details']['Points_Allotted_c'];
                $formattedData['user'][$jsonRow['Details']['Id']]['points_available'] = (int)$pointsAlloted; 
                // $formattedData['user'][$jsonRow['Details']['Id']]['mobile_number'] = htmlspecialchars($jsonRow['Details']['mobile_number']);  @todo
                // $formattedData['user'][$jsonRow['Details']['Id']]['title'] = htmlspecialchars($jsonRow['Details']['title']);    //  @todo
                // $formattedData['user'][$jsonRow['Details']['Id']]['suffix'] = htmlspecialchars($jsonRow['Details']['suffix']);  //  @todo
                $formattedData['user'][$jsonRow['Details']['Id']]['gender'] = $this->_getGenderId(htmlspecialchars($jsonRow['Details']['Genderc']), $jsonRow['Details']);
                $formattedData['user'][$jsonRow['Details']['Id']]['enrollment_start_date'] = array_key_exists('FB_Window_Start_Datec',$jsonRow['Details']) ? htmlspecialchars($jsonRow['Details']['FB_Window_Start_Datec']) : NULL;
                $formattedData['user'][$jsonRow['Details']['Id']]['enrollment_end_date'] = array_key_exists('FB_Window_End_Datec',$jsonRow['Details']) ? htmlspecialchars($jsonRow['Details']['FB_Window_End_Datec']) : NULL;
                $formattedData['user'][$jsonRow['Details']['Id']]['email'] = htmlspecialchars($jsonRow['Details']['Email']);
                $formattedData['user'][$jsonRow['Details']['Id']]['password'] = bcrypt(htmlspecialchars($employeeId) . '@' . ($jsonRow['Details']['Birthdate']));
                $formattedData['user'][$jsonRow['Details']['Id']]['country_id_fk'] = CountryCurrency::where(DB::raw('UPPER(name)'),strtoupper($jsonRow['Details']['MailingCountry']))->select('id')->first()->toArray()['id'];
                $formattedData['user'][$jsonRow['Details']['Id']]['created_by'] = 0;    // admin
                $formattedData['user'][$jsonRow['Details']['Id']]['modified_by'] = 0;    // admin
                $formattedData['user'][$jsonRow['Details']['Id']]['created_at'] = now();
                $formattedData['user'][$jsonRow['Details']['Id']]['updated_at'] = now();
                
                if (1 
                    //&& $formattedData['user'][$jsonRow['Details']['Id']]['external_id'] == '003UN000001Ov2UYAS'
                ) {
                
                    $user = User::where(
                        [
                            'external_id' => $formattedData['user'][$jsonRow['Details']['Id']]['external_id'],
                            //'email' => $formattedData['user'][$jsonRow['Details']['Id']]['email'],
                            //'employee_id' => $formattedData['user'][$jsonRow['Details']['Id']]['employee_id'],
                        ])->get()->toArray();
                    if (!count($user)) {
                        // save new user data
                        if(session('confirmUpdate')) {
                            //dd($formattedData['user'][$jsonRow['Details']['Id']]);
                            $userId = User::insertGetId($formattedData['user'][$jsonRow['Details']['Id']]);
                            //$formattedData['user'][$jsonRow['Details']['Id']]['id'] = $userId;
                            echo '<br>' . __FUNCTION__ . ':INFO:New user(' . implode(' ', [
                                $formattedData['user'][$jsonRow['Details']['Id']]['fname'],
                                $formattedData['user'][$jsonRow['Details']['Id']]['lname'],
                                $formattedData['user'][$jsonRow['Details']['Id']]['employee_id']]) . ') added with Id:' . $userId;
                                
                            // create default policy entries for new user
                            $this->generateBaseDefaultPolicyMapping([['id' => $userId]],session('confirmUpdate') );
                        } else {                            
                            echo '<br>' . __FUNCTION__ . ':INFO:User Data(' . implode(' ', [
                                $formattedData['user'][$jsonRow['Details']['Id']]['fname'],
                                $formattedData['user'][$jsonRow['Details']['Id']]['lname'],
                                $formattedData['user'][$jsonRow['Details']['Id']]['employee_id']]) . ') will be added';
                        }
                    } else {
                        // update user details only
                        unset($formattedData['user'][$jsonRow['Details']['Id']]['created_at']); // only modified date will be changed
                        unset($formattedData['user'][$jsonRow['Details']['Id']]['created_by']); // only updated by  will be changed
                        if(session('confirmUpdate')) {
                            $userUpdateStatus = User::where('id',$user[0]['id'])->update($formattedData['user'][$jsonRow['Details']['Id']]);
                            if ($userUpdateStatus) {
                                echo '<br>' . __FUNCTION__ . ':INFO:User(' . implode(' ', [
                                    $formattedData['user'][$jsonRow['Details']['Id']]['fname'],
                                    $formattedData['user'][$jsonRow['Details']['Id']]['lname'],
                                    $formattedData['user'][$jsonRow['Details']['Id']]['employee_id']]) . ') data updated with id:' . $user[0]['id'];
                                }
                        } else {
                            echo '<br>' . __FUNCTION__ . ':INFO:User Data(' . implode(' ', [
                                $formattedData['user'][$jsonRow['Details']['Id']]['fname'],
                                $formattedData['user'][$jsonRow['Details']['Id']]['lname'],
                                $formattedData['user'][$jsonRow['Details']['Id']]['employee_id']]) . ') to be updated with id:' . $user[0]['id'];
                            }
                        }
                    // user id added to formatted data
                    $formattedData['user'][$jsonRow['Details']['Id']]['id'] = count($user) ? $user[0]['id'] : $userId;
                }
            }
            return $formattedData;
        } else {
            die(__FUNCTION__ . ':ERROR: EMPTY JSON DATA RECEIVED');
        }
    }

    private function _saveDependantData ($userInsertData, $jsonData) {
        if (count($userInsertData) && count($jsonData)) {
            //dd($userInsertData , $jsonData);
            foreach ($jsonData as $userExtId => $jsonRow) {
                if (
                    1
                    //&& $userExtId ==  '003UN000001Ov2UYAS'
                    && count($jsonRow['Dependants'])
                ) {
                    $lowerCaseRltnNames = array_map('strtolower', config('constant.relationship_type'));
                    $lowerCaseApprovalStatus = array_map('strtolower', config('constant.approval_status'));
                    $lowerCaseBoolean = array_map('strtolower', config('constant.booleanArr'));
                    foreach ($jsonRow['Dependants'] as $depRow) {
                        $depData = [];
                        $depData['external_id'] = $depRow['Id'];
                        $depData['dependent_name'] = htmlspecialchars($depRow['Namec']);
                        $depData['dob'] = date('Y-m-d', strtotime($depRow['Date_of_Birthc']));
                        //$depData['gender'] = $depRow['gender']; @todo
                        $depData['nominee_percentage'] = $depRow['Nominee_Percentagec'];
                        $depData['relationship_type'] = array_search(strtolower($depRow['Relationship_Typec']),$lowerCaseRltnNames);
                        $depData['approval_status'] = array_search(strtolower($depRow['Approval_Statusc']),$lowerCaseApprovalStatus);      
                        $depData['is_active'] = config('constant.$_YES');
                        $depData['is_deceased'] = array_search(strtolower($depRow['Deceasedc']),$lowerCaseBoolean);
                        //dd($depData);
                        echo $this->validatedUpsertDependant($depData, $userInsertData['user'][$userExtId]['id']);
                    }
                } else {
                    echo '<br>----------' . __FUNCTION__ . ':INFO:No dependant found for user ' . 
                    $jsonRow['Details']['Name'];
                }

            }
            
        } else {
            die(__FUNCTION__ . ':ERROR: EMPTY JSON OR USER DATA RECEIVED');
        }
    }


}
