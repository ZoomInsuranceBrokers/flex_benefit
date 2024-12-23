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
use App\Models\FinancialYear;
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


    public function getUsedPoints()
    {


        $mapUserFYPolicyData = MapUserFYPolicy::where('user_id_fk', '=', Auth::user()->id)->with(['fyPolicy']);

        $fy_id = FinancialYear::where('is_active',1)->first();

        $mapUserFYPolicyData->whereRelation('fyPolicy.financialYears', 'id', $fy_id->id);

        $mapUserFYPolicyData = $mapUserFYPolicyData->get()->toArray();

        return response()->json(['mapUserFYPolicyData' =>  $mapUserFYPolicyData]);
    }
    public function home(Request $request)
    {
        //echo bcrypt('1234567890');
        //dd(Crypt::decryptString('$2y$10$2OhRE\/zTnRX3OJIfUcBrAuySK375QJf0F2WarzkB3bRor7TYWRdj2'));
        session(['is_enrollment_window' => false]);


        if (Auth::check()) {
            $accountData = Account::where('is_active', true)->get()->toArray();
            $todayDate       = new DateTime(); // Today
            $enrollmentDateBegin = new DateTime($accountData[0]['enrollment_start_date']);
            $enrollmentDateEnd = new DateTime($accountData[0]['enrollment_end_date']);

            // user level window opening
            if (!is_null(Auth::user()->enrollment_start_date) && !is_null(Auth::user()->enrollment_end_date)) {
                $uenrollmentDateBegin = new DateTime(Auth::user()->enrollment_start_date);
                $uenrollmentDateEnd = new DateTime(Auth::user()->enrollment_end_date);
                if ($uenrollmentDateBegin >= $enrollmentDateBegin) {    // user enrollment is after account enrollment date
                    $enrollmentDateBegin = $uenrollmentDateBegin;
                    $enrollmentDateEnd = $uenrollmentDateEnd;
                }
            }
            if ($todayDate >= $enrollmentDateBegin && $todayDate < $enrollmentDateEnd) {
                session(['is_enrollment_window' => true]);
            }
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

    public function createUsers(Request $request)
    {
        // if (
        //     $request->isMethod('get') && $request->has('authKey') &&
        //     $request->authKey == base64_encode(env('APP_API_SECRET_KEY') . '@' . date('d-m-Y'))
        // ) 

        {
            if ($request->has('confirmUpdate') && $request->confirmUpdate) {
                session(['confirmUpdate' => true]);
            } else {
                session(['confirmUpdate' => false]);
            }
            $testJson = '{
                "status": "SUCCESS",
                "details": "{\"003UN000004qwvJYAQ\":{\"Details\":{\"Id\":\"003UN000004qwvJYAQ\",\"LastName\":\"Sharma\",\"FirstName\":\"Pallavi\",\"Name\":\"Pallavi Sharma\",\"MailingCountry\":\"India\",\"MailingCountryCode\":\"IN\",\"Phone\":\"9818034215\",\"Email\":\"pallavi.sharma@zoominsurancebrokers.com\",\"Birthdate\":\"1991-12-01\",\"CreatedDate\":\"2024-03-03T11:00:07.000Z\",\"CreatedById\":\"005Hs00000CbkOnIAJ\",\"LastModifiedDate\":\"2024-03-03T11:00:07.000Z\",\"LastModifiedById\":\"005Hs00000CbkOnIAJ\",\"Annual_CTC__c\":\"0zfCkFgwm3vISBGIODD3AQ==\",\"Employee_Id__c\":\"10476\",\"Hire_Date__c\":\"2024-02-05\",\"Gradec\":\"NA\",\"Nominee_Percentagec\":0,\"Designation__c\":\"Manager\",\"Gender__c\":\"Female\",\"Points_Allotted__c\":4315,\"Is_Active__c\":true,\"FB_Window_Start_Date__c\":\"2024-02-05\",\"FB_Window_End_Date__c\":\"2024-02-26\"},\"Dependants\":[{\"Id\":\"a0DUN000000uMcD2AU\",\"Name\":\"D-00446\",\"LastModifiedDate\":\"2024-03-03 11:00:07\",\"Employeec\":\"003UN000004qwvJYAQ\",\"Name__c\":\"Pallavi  Sharma\",\"Relationship_Type__c\":\"Self\",\"Date_of_Birth__c\":\"1991-12-01 00:00:00\",\"Deceased__c\":\"No\",\"Approval_Status__c\":\"Approved\",\"Gender__c\":\"Female\",\"Unique_External_Idc\":\"003UN000004qwvJYAQ\",\"Dependant_Key_c\":\"001UN000001lfNPYAY_003UN000004qwvJYAQ_PallaviSharma\"}]},\"003UN000004qwvIYAQ\":{\"Details\":{\"Id\":\"003UN000004qwvIYAQ\",\"LastName\":\"Beena\",\"FirstName\":\"Beena\",\"Name\":\"Beena\",\"MailingCountry\":\"India\",\"MailingCountryCode\":\"IN\",\"Phone\":\"7838284799\",\"Email\":\"beena.johar@zoominsurancebrokers.com\",\"Birthdate\":\"1998-10-10\",\"CreatedDate\":\"2024-03-03T11:00:07.000Z\",\"CreatedById\":\"005Hs00000CbkOnIAJ\",\"LastModifiedDate\":\"2024-03-03T11:00:07.000Z\",\"LastModifiedById\":\"005Hs00000CbkOnIAJ\",\"Annual_CTC__c\":\"QMML+f15ftWRi+IJhw8Xuw==\",\"Employee_Id__c\":\"10475\",\"Hire_Date__c\":\"2024-02-01\",\"Gradec\":\"NA\",\"Nominee_Percentagec\":0,\"Designation__c\":\"Executive\",\"Gender__c\":\"Female\",\"Points_Allotted__c\":4370,\"Is_Active__c\":true,\"FB_Window_Start_Date__c\":\"2024-02-01\",\"FB_Window_End_Date__c\":\"2024-02-22\"},\"Dependants\":[{\"Id\":\"a0DUN000000uMcC2AU\",\"Name\":\"D-00445\",\"LastModifiedDate\":\"2024-03-03 11:00:07\",\"Employeec\":\"003UN000004qwvIYAQ\",\"Name__c\":\"Beena\",\"Relationship_Type__c\":\"Self\",\"Date_of_Birth__c\":\"1998-10-10 00:00:00\",\"Deceased__c\":\"No\",\"Approval_Status__c\":\"Approved\",\"Gender__c\":\"Female\",\"Unique_External_Idc\":\"003UN000004qwvIYAQ\",\"Dependant_Key_c\":\"001UN000001lfNPYAY_003UN000004qwvIYAQ_Beena\"}]},\"003UN000004qwvHYAQ\":{\"Details\":{\"Id\":\"003UN000004qwvHYAQ\",\"LastName\":\"Rathore\",\"FirstName\":\"Anjali\",\"Name\":\"Anjali Rathore\",\"MailingCountry\":\"India\",\"MailingCountryCode\":\"IN\",\"Phone\":\"9582572558\",\"Email\":\"anjali.rathore@zoominsurancebrokers.com\",\"Birthdate\":\"1997-07-05\",\"CreatedDate\":\"2024-03-03T11:00:07.000Z\",\"CreatedById\":\"005Hs00000CbkOnIAJ\",\"LastModifiedDate\":\"2024-03-03T11:00:07.000Z\",\"LastModifiedById\":\"005Hs00000CbkOnIAJ\",\"Annual_CTC__c\":\"/XOptKQirMpiPe09TVJQUw==\",\"Employee_Id__c\":\"10479\",\"Hire_Date__c\":\"2024-02-12\",\"Gradec\":\"NA\",\"Nominee_Percentagec\":0,\"Designation__c\":\"Deputy Manager\",\"Gender__c\":\"Female\",\"Points_Allotted__c\":4219,\"Is_Active__c\":true,\"FB_Window_Start_Date__c\":\"2024-02-12\",\"FB_Window_End_Date__c\":\"2024-03-04\"},\"Dependants\":[{\"Id\":\"a0DUN000000uMcB2AU\",\"Name\":\"D-00444\",\"LastModifiedDate\":\"2024-03-03 11:00:07\",\"Employeec\":\"003UN000004qwvHYAQ\",\"Name__c\":\"Anjali  Rathore\",\"Relationship_Type__c\":\"Self\",\"Date_of_Birth__c\":\"1997-07-05 00:00:00\",\"Deceased__c\":\"No\",\"Approval_Status__c\":\"Approved\",\"Gender__c\":\"Female\",\"Unique_External_Idc\":\"003UN000004qwvHYAQ\",\"Dependant_Key_c\":\"001UN000001lfNPYAY_003UN000004qwvHYAQ_AnjaliRathore\"}]},\"003UN000004qwvGYAQ\":{\"Details\":{\"Id\":\"003UN000004qwvGYAQ\",\"LastName\":\"Lovely\",\"FirstName\":\"Lovely\",\"Name\":\"Lovely\",\"MailingCountry\":\"India\",\"MailingCountryCode\":\"IN\",\"Phone\":\"7838255050\",\"Email\":\"lovely@zoominsurancebrokers.com\",\"Birthdate\":\"1991-06-03\",\"CreatedDate\":\"2024-03-03T11:00:07.000Z\",\"CreatedById\":\"005Hs00000CbkOnIAJ\",\"LastModifiedDate\":\"2024-03-03T11:00:07.000Z\",\"LastModifiedById\":\"005Hs00000CbkOnIAJ\",\"Annual_CTC__c\":\"kB9lioNQHtBnFWyAZznS6g==\",\"Employee_Id__c\":\"10478\",\"Hire_Date__c\":\"2024-02-12\",\"Gradec\":\"NA\",\"Nominee_Percentagec\":0,\"Designation__c\":\"Asst. Manager\",\"Gender__c\":\"Male\",\"Points_Allotted__c\":4219,\"Is_Active__c\":true,\"FB_Window_Start_Date__c\":\"2024-02-12\",\"FB_Window_End_Date__c\":\"2024-03-04\"},\"Dependants\":[{\"Id\":\"a0DUN000000uMcA2AU\",\"Name\":\"D-00443\",\"LastModifiedDate\":\"2024-03-03 11:00:07\",\"Employeec\":\"003UN000004qwvGYAQ\",\"Name__c\":\"Lovely\",\"Relationship_Type__c\":\"Self\",\"Date_of_Birth__c\":\"1991-06-03 00:00:00\",\"Deceased__c\":\"No\",\"Approval_Status__c\":\"Approved\",\"Gender__c\":\"Male\",\"Unique_External_Idc\":\"003UN000004qwvGYAQ\",\"Dependant_Key_c\":\"001UN000001lfNPYAY_003UN000004qwvGYAQ_Lovely\"}]},\"003UN000004qwvFYAQ\":{\"Details\":{\"Id\":\"003UN000004qwvFYAQ\",\"LastName\":\"Yadav\",\"FirstName\":\"Saurabh\",\"Name\":\"Saurabh Yadav\",\"MailingCountry\":\"India\",\"MailingCountryCode\":\"IN\",\"Phone\":\"9810933373\",\"Email\":\"saurabh.yadav@zoominsurancebrokers.com\",\"Birthdate\":\"1991-07-09\",\"CreatedDate\":\"2024-03-03T11:00:07.000Z\",\"CreatedById\":\"005Hs00000CbkOnIAJ\",\"LastModifiedDate\":\"2024-03-03T11:00:07.000Z\",\"LastModifiedById\":\"005Hs00000CbkOnIAJ\",\"Annual_CTC__c\":\"TmbL4Xz60oWMxrm6o59DWQ==\",\"Employee_Id__c\":\"10477\",\"Hire_Date__c\":\"2024-02-09\",\"Gradec\":\"NA\",\"Nominee_Percentagec\":0,\"Designation__c\":\"Manager\",\"Gender__c\":\"Male\",\"Points_Allotted__c\":4260.5,\"Is_Active__c\":true,\"FB_Window_Start_Date__c\":\"2024-02-09\",\"FB_Window_End_Date__c\":\"2024-03-01\"},\"Dependants\":[{\"Id\":\"a0DUN000000uMc92AE\",\"Name\":\"D-00442\",\"LastModifiedDate\":\"2024-03-03 11:00:07\",\"Employeec\":\"003UN000004qwvFYAQ\",\"Name__c\":\"Saurabh  Yadav\",\"Relationship_Type__c\":\"Self\",\"Date_of_Birth__c\":\"1991-07-09 00:00:00\",\"Deceased__c\":\"No\",\"Approval_Status__c\":\"Approved\",\"Gender__c\":\"Male\",\"Unique_External_Idc\":\"003UN000004qwvFYAQ\",\"Dependant_Key__c\":\"001UN000001lfNPYAY_003UN000004qwvFYAQ_SaurabhYadav\"}]}}"
            }';
            //$userJsonData = json_decode(base64_decode($request->uDJson));
            //dd(json_decode(json_decode($testJson, true)['details'], true));
            $jsonData = json_decode(json_decode($testJson, true)['details'], true);

            //dd($jsonData);
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
                // dd($userInsertData , $jsonData);
                $this->_saveDependantData($userInsertData, $jsonData);
            } else {
                echo 'Empty User Json Data, No User Created!!!';
            }
        }
    }

    private function _getGenderId($genderText, $rowData, $isDependant = false)
    {
        if (strlen($genderText)) {
            $genderCode = config('constant.$_GENDER_OTHER');
            switch (strtolower($genderText)) {
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
            $entryType = 'user';
            $entryName = $rowData['FirstName'] . ' ' . $rowData['LastName'] . '[' . $rowData['Employee_Id_c'] . ']';
            if ($isDependant) {
                $entryType = 'dependant';
                $entryName = $rowData['Name__c'] . '[EMPID:' . $rowData['Employee__c'] . ', DEPID:' . $rowData['Id'] . ']';
            }
            die(__FUNCTION__ . ':ERROR:Invalid Gender for ' . $entryType . ' record: ' . $entryName);
        }
    }

    private function _getGradeArray($existingGrades)
    {
        $gradeData = [];
        if (count($existingGrades)) {
            foreach ($existingGrades as $gData) {
                $gradeData[$gData['id']] = strtolower($gData['grade_name']);
            }
            return $gradeData;
        } else {
            die(__FUNCTION__ . ':ERROR: No grade data found!!');
        }
    }

    private function _saveUserData($jsonData)
    {
        $formattedData = [];
        $fyData = FinancialYear::where('is_active', 1)->get()->toArray();
        if (count($jsonData)) {
            // user data
            foreach ($jsonData as $jsonRow) {
                $formattedData['user'][$jsonRow['Details']['Id']]['external_id'] = htmlspecialchars($jsonRow['Details']['Id']);
                $formattedData['user'][$jsonRow['Details']['Id']]['fname'] = htmlspecialchars(strlen($jsonRow['Details']['FirstName']) ?
                    $jsonRow['Details']['FirstName'] : $jsonRow['Details']['LastName']);
                $formattedData['user'][$jsonRow['Details']['Id']]['lname'] = htmlspecialchars($jsonRow['Details']['LastName']);

                array_key_exists('MiddleName', $jsonRow['Details']) ?
                    $formattedData['user'][$jsonRow['Details']['Id']]['mname'] = htmlspecialchars($jsonRow['Details']['MiddleName']) : '';

                $employeeId = $jsonRow['Details']['Employee_Id__c'];
                $formattedData['user'][$jsonRow['Details']['Id']]['employee_id'] = htmlspecialchars($employeeId);

                // GRADE
                $gradeId = array_search($jsonRow['Details']['Designation__c'], session('grades'));
                $formattedData['user'][$jsonRow['Details']['Id']]['grade_id_fk'] = $gradeId ? $gradeId : array_search('na', session('grades'));

                $formattedData['user'][$jsonRow['Details']['Id']]['dob'] = htmlspecialchars($jsonRow['Details']['Birthdate']);
                $formattedData['user'][$jsonRow['Details']['Id']]['hire_date'] = htmlspecialchars($jsonRow['Details']['Hire_Date__c']);
                $formattedData['user'][$jsonRow['Details']['Id']]['salary'] = htmlspecialchars($jsonRow['Details']['Annual_CTC__c']);
                $formattedData['user'][$jsonRow['Details']['Id']]['points_used'] = 0;
                $formattedData['user'][$jsonRow['Details']['Id']]['points_available'] = (int)$jsonRow['Details']['Points_Allotted__c'];
                $formattedData['user'][$jsonRow['Details']['Id']]['mobile_number'] = array_key_exists('Phone', $jsonRow['Details']) ? htmlspecialchars($jsonRow['Details']['Phone']) : '';
                // $formattedData['user'][$jsonRow['Details']['Id']]['title'] = array_key_exists('Title', $jsonRow['Details']) ? htmlspecialchars($jsonRow['Details']['Title']) : null;
                // $formattedData['user'][$jsonRow['Details']['Id']]['suffix'] =array_key_exists('Suffix', $jsonRow['Details']) ? htmlspecialchars($jsonRow['Details']['Suffix']) : null;
                $formattedData['user'][$jsonRow['Details']['Id']]['gender'] = $this->_getGenderId(htmlspecialchars($jsonRow['Details']['Gender__c']), $jsonRow['Details']);
                $enrollmentData = $this->_getEnrollmentData(
                    $jsonRow['Details'],
                    $fyData,
                    $jsonRow['Details']['Hire_Date__c'],
                    $jsonRow['Details']['FB_Window_Start_Date__c'],
                    $jsonRow['Details']['FB_Window_End_Date__c']
                );

                $formattedData['user'][$jsonRow['Details']['Id']]['enrollment_start_date'] = $enrollmentData['userEnrollmentStartDate'];
                $formattedData['user'][$jsonRow['Details']['Id']]['enrollment_end_date'] = $enrollmentData['userEnrollmentEndDate'];

                if ($enrollmentData['autoSubmit']) {
                    $formattedData['user'][$jsonRow['Details']['Id']]['is_enrollment_submitted'] = true;
                    $formattedData['user'][$jsonRow['Details']['Id']]['enrollment_submit_date'] = $jsonRow['Details']['Hire_Date__c'];
                    $formattedData['user'][$jsonRow['Details']['Id']]['submission_by'] = 0; // admin
                }

                $formattedData['user'][$jsonRow['Details']['Id']]['email'] = htmlspecialchars($jsonRow['Details']['Email']);
                $timestamp = strtotime($jsonRow['Details']['Birthdate']);
                $formattedData['user'][$jsonRow['Details']['Id']]['password'] = bcrypt(htmlspecialchars($employeeId) . '@' . (date('dmY', $timestamp)));
                $formattedData['user'][$jsonRow['Details']['Id']]['country_id_fk'] = CountryCurrency::where(DB::raw('UPPER(name)'), strtoupper($jsonRow['Details']['MailingCountry']))->select('id')->first()->toArray()['id'];
                $formattedData['user'][$jsonRow['Details']['Id']]['created_by'] = 0;    // admin
                $formattedData['user'][$jsonRow['Details']['Id']]['modified_by'] = 0;    // admin
                $formattedData['user'][$jsonRow['Details']['Id']]['created_at'] = now();
                $formattedData['user'][$jsonRow['Details']['Id']]['updated_at'] = now();
                $formattedData['user'][$jsonRow['Details']['Id']]['is_active'] = $jsonRow['Details']['Is_Active__c'];

                if (1
                    //&& $formattedData['user'][$jsonRow['Details']['Id']]['external_id'] == '003UN000001Ov2UYAS'
                ) {
                    $user = User::where(
                        [
                            'external_id' => $formattedData['user'][$jsonRow['Details']['Id']]['external_id'],
                            //'email' => $formattedData['user'][$jsonRow['Details']['Id']]['email'],
                            //'employee_id' => $formattedData['user'][$jsonRow['Details']['Id']]['employee_id'],
                        ]
                    )->get()->toArray();
                    if (!count($user)) {
                        $userId = NULL;
                        // save new user data
                        if (session('confirmUpdate')) {
                            $userId = User::insertGetId($formattedData['user'][$jsonRow['Details']['Id']]);
                            //$formattedData['user'][$jsonRow['Details']['Id']]['id'] = $userId;
                            echo '<br>' . __FUNCTION__ . ':INFO:New user(' . implode(' ', [
                                $formattedData['user'][$jsonRow['Details']['Id']]['fname'],
                                $formattedData['user'][$jsonRow['Details']['Id']]['lname'],
                                $formattedData['user'][$jsonRow['Details']['Id']]['employee_id']
                            ]) . ') added with Id:' . $userId;

                            // create default policy entries for new user
                            $this->generateBaseDefaultPolicyMapping(
                                [['id' => $userId]],
                                $enrollmentData['autoSubmit'],
                                session('confirmUpdate')
                            );
                        } else {
                            echo '<br>' . __FUNCTION__ . ':INFO:User Data(' . implode(' ', [
                                $formattedData['user'][$jsonRow['Details']['Id']]['fname'],
                                $formattedData['user'][$jsonRow['Details']['Id']]['lname'],
                                $formattedData['user'][$jsonRow['Details']['Id']]['employee_id']
                            ]) . ') will be added';
                        }
                    } else {
                        // update user details only
                        unset($formattedData['user'][$jsonRow['Details']['Id']]['created_at']); // only modified date will be changed
                        unset($formattedData['user'][$jsonRow['Details']['Id']]['created_by']); // only updated by  will be changed
                        if (session('confirmUpdate')) {
                            $userUpdateStatus = User::where('id', $user[0]['id'])->update($formattedData['user'][$jsonRow['Details']['Id']]);
                            if ($userUpdateStatus) {
                                echo '<br>' . __FUNCTION__ . ':INFO:User(' . implode(' ', [
                                    $formattedData['user'][$jsonRow['Details']['Id']]['fname'],
                                    $formattedData['user'][$jsonRow['Details']['Id']]['lname'],
                                    $formattedData['user'][$jsonRow['Details']['Id']]['employee_id']
                                ]) . ') data updated with id:' . $user[0]['id'];
                            }
                        } else {
                            echo '<br>' . __FUNCTION__ . ':INFO:User Data(' . implode(' ', [
                                $formattedData['user'][$jsonRow['Details']['Id']]['fname'],
                                $formattedData['user'][$jsonRow['Details']['Id']]['lname'],
                                $formattedData['user'][$jsonRow['Details']['Id']]['employee_id']
                            ]) . ') to be updated with id:' . $user[0]['id'];
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

    private function _getEnrollmentData($userData, $fyData, $hireDate, $startDate, $endDate)
    {
        $enrollmentData = [];
        if (!is_null($hireDate) && trim($hireDate) != '') {
            $hireDate = new DateTime($hireDate);
            $fyLastEnrollmentDate = new DateTime($fyData[0]['last_enrollment_date']);

            if ($hireDate > $fyLastEnrollmentDate) {    // auto submission true and enrollment window should not open
                $enrollmentData['autoSubmit'] = true;
                $enrollmentData['userEnrollmentStartDate'] = null;
                $enrollmentData['userEnrollmentEndDate'] = null;
            } else {    // window will open based on date calculations and auto submit will not happen
                $enrollmentData['autoSubmit'] = false;
                $enrollmentData['userEnrollmentStartDate'] = $startDate; // default case; setting date to received date
                $enrollmentData['userEnrollmentEndDate'] = $endDate;    // default case; setting date to received date
                // for end date calculations
                $extEndDate = new DateTime($endDate);
                if ($extEndDate > $fyLastEnrollmentDate) {  // case when user end date is crossing last enrollment date 
                    $enrollmentData['userEnrollmentEndDate'] = date('Y-m-d', $fyData[0]['last_enrollment_date']);
                }
            }
            return $enrollmentData;
        } else {
            die(__FUNCTION__ . ':ERROR: EMPTY/INVALID HIRE DATA FOR USER:' . implode(' ', [
                $userData['FirstName'],
                $userData['LastName'],
                $userData['Employee_Id__c']
            ]));
        }
    }

    private function _saveDependantData($userInsertData, $jsonData)
    {
        if (count($userInsertData) && count($jsonData)) {
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
                        $depData['dependent_name'] = htmlspecialchars($depRow['Name__c']);
                        $depData['dob'] = date('Y-m-d', strtotime($depRow['Date_of_Birth__c']));
                        $depData['gender'] = $this->_getGenderId(htmlspecialchars($depRow['Gender__c']), $depRow, true);
                        $depData['nominee_percentage'] = array_key_exists('Nominee_Percentage__c', $depRow) ? $depRow['Nominee_Percentage__c'] : 0;
                        $depData['relationship_type'] = array_search(strtolower($depRow['Relationship_Type__c']), $lowerCaseRltnNames);
                        $depData['approval_status'] = array_search(strtolower($depRow['Approval_Status__c']), $lowerCaseApprovalStatus);
                        $depData['is_active'] = config('constant.$_YES');
                        $depData['is_deceased'] = array_search(strtolower($depRow['Deceased__c']), $lowerCaseBoolean);
                        echo $this->validatedUpsertDependant($depData, $userInsertData['user'][$userExtId], $depRow);
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
