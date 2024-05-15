<?php

namespace App\Http\Controllers;

use DateTime;
use Exception;
use App\Models\User;
use App\Models\Dependant;
use App\Models\Account;
use Illuminate\Http\Request;
use App\Models\MapUserFYPolicy;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use App\Models\Grade;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewJoiningCredentials;
use App\Models\FinancialYear;
use Illuminate\View\View;
use Illuminate\Support\Str;
use App\Mail\PasswordResetMail;
use App\Models\CountryCurrency;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Crypt;
use App\Traits\EnrollmentTraitMethods;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rules\Password;
use App\Traits\dependantTraitMethods;
use Illuminate\Support\Facades\Log;


class ApiController extends Controller
{
    use EnrollmentTraitMethods;
    use dependantTraitMethods;

    public $secret_key = 'x409z636R3vFRPttwT26jkdwbdewidJN1bncwi2gpT';
    public function getAllUsers(Request $request)
    {

        if (empty($api_key)) {
            $response = [
                'status' => 'not found',
                'response' => 'Api Key not found',
            ];

            return response()->json($response);
        }

        if ($api_key != $this->secret_key) {
            $response = [
                'status' => 'invalid',
                'response' => 'Api Key is invalid',
            ];

            return response()->json($response);
        }

        $users = User::all();

        $response = [
            'status' => 'ok',
            'response' => 'ok',
            'data' => $users,
        ];

        return response()->json($response);
    }

    public function getSalesforceData(Request $request)
    {
        $api_key = trim($request->api_key);

        if (empty($api_key)) {
            $response = [
                'status' => 'not found',
                'response' => 'Api Key not found',
            ];

            return response()->json($response);
        }

        if ($api_key != $this->secret_key) {
            $response = [
                'status' => 'invalid',
                'response' => 'Api Key is invalid',
            ];

            return response()->json($response);
        }
        $accessToken = $this->getAccessToken();

        $salesforceData = $this->getSalesforceDataUsingToken($accessToken);

        return response()->json($salesforceData);
    }

    public function getdependentSalesforceData(Request $request)
    {
        $api_key = trim($request->api_key);

        if ($request->has('confirmUpdate') && $request->confirmUpdate) {
            session(['confirmUpdate' => true]);
        } else {
            session(['confirmUpdate' => false]);
        }

        if (empty($api_key)) {
            $response = [
                'status' => 'not found',
                'response' => 'Api Key not found',
            ];

            return response()->json($response);
        }

        if ($api_key != $this->secret_key) {
            $response = [
                'status' => 'invalid',
                'response' => 'Api Key is invalid',
            ];

            return response()->json($response);
        }
        $accessToken = $this->getAccessToken();

        $salesforceData = $this->getSalesforceDependentDataUsingToken($accessToken);

        return response()->json($salesforceData);
    }

    private function getAccessToken()
    {
        $response = Http::asForm()->post(config('salesforce.login_url') . '/services/oauth2/token', [
            'grant_type' => 'password',
            'client_id' => config('salesforce.client_id'),
            'client_secret' => config('salesforce.client_secret'),
            'username' => config('salesforce.username'),
            'password' => config('salesforce.password'),
        ]);

        $responseData = $response->json();

        return $responseData['access_token'];
    }

    private function getSalesforceDataUsingToken($accessToken)
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $accessToken,
        ])->post('https://zoominsurancebrokers--pc.sandbox.my.salesforce.com/services/apexrest/getUpdates', [
            'ids' => ['001UN000001lfNPYAY'],
            'reqtype' => 'CLIENT_POLICY_SCHEMA',
        ]);

        return $response->json();
    }

    private function getSalesforceDependentDataUsingToken($accessToken)
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $accessToken,
        ])->post('https://zoominsurancebrokers--pc.sandbox.my.salesforce.com/services/apexrest/getUpdates', [
            'clientids' => ['001UN000001lfNPYAY'],
            'reqtype' => 'EMP_DEPENDANT_SCHEMA',
        ]);

        $response = $response->json();
        $testJson = json_encode($response);

        if ($response['status'] == "SUCCESS") {
            $jsonData = json_decode(json_decode($testJson, true)['details'], true);
            if (count($jsonData)) {
                if (array_key_exists('account', $jsonData)) {
                }
                if (array_key_exists('grades', $jsonData)) {
                }
                $grades = Grade::where('is_active', true)->select('id', 'grade_name')->get()->toArray();

                session(['grades' => $this->_getGradeArray($grades)]);

                $userInsertData = $this->_saveUserData($jsonData);

                $this->_saveDependantData($userInsertData, $jsonData);
            } else {
                echo 'Empty User Json Data, No User Created!!!';
            }
        } else {
            return  $response;
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
                if(isset($jsonRow['Details']['FirstName'])){
                    $formattedData['user'][$jsonRow['Details']['Id']]['fname'] = htmlspecialchars($jsonRow['Details']['FirstName']);
                }else{
                    $formattedData['user'][$jsonRow['Details']['Id']]['fname'] = htmlspecialchars($jsonRow['Details']['LastName']);
                }
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
                // $formattedData['user'][$jsonRow['Details']['Id']]['is_active'] = $jsonRow['Details']['Is_Active__c'];



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
                            $email = $formattedData['user'][$jsonRow['Details']['Id']]['email'];
                            $users = $formattedData['user'][$jsonRow['Details']['Id']]['fname'];

                            Mail::to($email)->send(new NewJoiningCredentials($users));
                            echo '<br>' . __FUNCTION__ . ':INFO:New user(' . implode(' ', [
                                $formattedData['user'][$jsonRow['Details']['Id']]['fname'],
                                $formattedData['user'][$jsonRow['Details']['Id']]['lname'],
                                $formattedData['user'][$jsonRow['Details']['Id']]['employee_id']
                            ]) . ') added with Id:' . $userId;

                           
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
    private function _saveDependantData ($userInsertData, $jsonData) {
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
                        if(!isset($depRow['Date_of_Birth__c'])){
                            dd($depRow);
                        }
                        $depData['dob'] = date('Y-m-d', strtotime($depRow['Date_of_Birth__c']));
                        $depData['gender'] = $this->_getGenderId(htmlspecialchars($depRow['Gender__c']), $depRow, true);
                        $depData['nominee_percentage'] = array_key_exists('Nominee_Percentage__c', $depRow) ? $depRow['Nominee_Percentage__c'] : 0;
                        $depData['relationship_type'] = array_search(strtolower($depRow['Relationship_Type__c']),$lowerCaseRltnNames);
                        $depData['approval_status'] = array_search(strtolower($depRow['Approval_Status__c']),$lowerCaseApprovalStatus);      
                        $depData['is_active'] = config('constant.$_YES');
                        $depData['is_deceased'] = array_search(strtolower($depRow['Deceased__c']),$lowerCaseBoolean);
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
    
    public function getUserEnrollmentData(Request $request)
    {
        // dd(base64_encode('a9#Bc2$eDfGhIjK4LmNpQr6StUvWxYz' . date('d-m-Y')));
        // dd($request->authKey);
        Log::info('Request received for getUserEnrollmentData', ['request' => $request->all()]);

        if (
             $request->isMethod('get') && $request->has('authKey') &&
             $request->authKey == base64_encode(env('APP_API_SECRET_KEY') . '@' . date('d-m-Y'))
         )
        // if (1)
        {
            $filters = ['output' => 'json', 'active' => true];
            $request->has('sdte') ? $filters['sdate'] = $request->sdte : '';
            $request->has('edte') ? $filters['edate'] = $request->edte : '';
            $request->has('colName') ? $filters['colName'] = $request->colName : '';
            $request->has('eid') ? $filters['empId'] = $request->eid : '';
            $request->has('extAcI') ? $filters['accId'] = $request->extAcI : '';
            $request->has('output') ? $filters['output'] = $request->output : '';
            $request->has('active') ? $filters['active'] = $request->active : '';

            $finalData = [];
            // Account Info
            if (array_key_exists('accId', $filters)) {
                $finalData['account'] = Account::where('id', $filters['accId'])
                    ->whereOr('external_id', $filters['accId'])
                    ->select('id', 'external_id', 'name', 'enrollment_start_date', 'enrollment_end_date')
                    ->get()->toArray();
            }
            // enrollment/submission date filter
            $mapUserFYPolicyData = MapUserFYPolicy::with(['fyPolicy', 'user']);
            if (array_key_exists('colName', $filters)) {
                if (array_key_exists('sdate', $filters) && array_key_exists('edate', $filters)) {
                    $mapUserFYPolicyData->where($filters['colName'], '>=', $filters['sdate']);
                    $mapUserFYPolicyData->where($filters['colName'], '<=', $filters['edate']);
                } else if (array_key_exists('sdate', $filters)) {
                    $mapUserFYPolicyData->where($filters['colName'], '>=', $filters['sdate']);
                } else if (array_key_exists('edate', $filters)) {
                    $mapUserFYPolicyData->where($filters['colName'], '<=', $filters['edate']);
                }
            }

            // record active status
            if ($filters['active'] != '*') {
                $mapUserFYPolicyData->where('is_active', $filters['active']);
            }

            // user ids
            if (array_key_exists('empId', $filters) && $filters['empId'] != '*') {
                $mapUserFYPolicyData->whereIn('user_id_fk', explode(',', $filters['empId']));
            }

            //dd($mapUserFYPolicyData->toSql());

            $submissionData = $mapUserFYPolicyData->get()->toArray();
            //dd($submissionData);
            if (count($submissionData)) {
                foreach ($submissionData as $submissionRow) {
                    // dd($submissionRow);
                    // policy data
                    $policyData = $submissionRow['fy_policy']['policy'];
                    $finalData['user'][$submissionRow['user_id_fk']]['policy'][$submissionRow['id']]['policy_id'] = $policyData['id'];
                    $finalData['user'][$submissionRow['user_id_fk']]['policy'][$submissionRow['id']]['policy_name'] = $policyData['name'];
                    $finalData['user'][$submissionRow['user_id_fk']]['policy'][$submissionRow['id']]['external_id'] = $policyData['external_id'];
                    $finalData['user'][$submissionRow['user_id_fk']]['policy'][$submissionRow['id']]['points_used'] = $submissionRow['points_used'];
                    $finalData['user'][$submissionRow['user_id_fk']]['policy'][$submissionRow['id']]['selected_dependent'] = $submissionRow['selected_dependent'];
                    $finalData['user'][$submissionRow['user_id_fk']]['policy'][$submissionRow['id']]['encoded_summary'] = $submissionRow['encoded_summary'];
                    $finalData['user'][$submissionRow['user_id_fk']]['policy'][$submissionRow['id']]['is_submitted'] = $submissionRow['is_submitted'];

                    // user data
                    $userData = $submissionRow['user'];
                    // dd($userData);
                    $finalData['user'][$submissionRow['user_id_fk']]['id'] = $userData['id'];
                    $finalData['user'][$submissionRow['user_id_fk']]['external_id'] = $userData['external_id'];
                    $finalData['user'][$submissionRow['user_id_fk']]['fname'] = $userData['fname'] !== "" ? $userData['fname'] : null;
                    $finalData['user'][$submissionRow['user_id_fk']]['lname'] = $userData['lname'];
                    $finalData['user'][$submissionRow['user_id_fk']]['hire_date'] = $userData['hire_date'];
                    $finalData['user'][$submissionRow['user_id_fk']]['points_used'] = $userData['points_used'];
                    $finalData['user'][$submissionRow['user_id_fk']]['points_available'] = $userData['points_available'];
                    $finalData['user'][$submissionRow['user_id_fk']]['gender'] = config('constant.gender')[$userData['gender']];
                    $finalData['user'][$submissionRow['user_id_fk']]['enrollment_submit_date'] = $userData['enrollment_submit_date'];
                    $finalData['user'][$submissionRow['user_id_fk']]['is_enrollment_submitted'] = $userData['is_enrollment_submitted'];
                    $finalData['user'][$submissionRow['user_id_fk']]['submission_by'] = $userData['submission_by'] != null ? 
                        ($userData['submission_by'] == 0 ? 
                            'Auto-Submitted' : 'Submitted' ) : 
                        'Open';

                    // dependant data
                    /*$dependantData = $submissionRow['user']['dependant'];
                    if (count($dependantData)) {
                        foreach ($dependantData as $depRow){
                            $finalData['user'][$submissionRow['user_id_fk']]['dependant'][$depRow['id']]['external_id'] = $depRow['external_id'];
                            $finalData['user'][$submissionRow['user_id_fk']]['dependant'][$depRow['id']]['dependent_name'] = $depRow['dependent_name'];
                            $finalData['user'][$submissionRow['user_id_fk']]['dependant'][$depRow['id']]['dependent_code'] = config('constant.dependent_code_ui')[$depRow['dependent_code']];
                            $finalData['user'][$submissionRow['user_id_fk']]['dependant'][$depRow['id']]['dob'] = $depRow['dob'];
                            $finalData['user'][$submissionRow['user_id_fk']]['dependant'][$depRow['id']]['gender'] = config('constant.gender')[$depRow['gender']];
                            $finalData['user'][$submissionRow['user_id_fk']]['dependant'][$depRow['id']]['nominee_percentage'] = $depRow['nominee_percentage'];
                            $finalData['user'][$submissionRow['user_id_fk']]['dependant'][$depRow['id']]['relationship_type'] = config('constant.relationship_type')[$depRow['relationship_type']];
                            $finalData['user'][$submissionRow['user_id_fk']]['dependant'][$depRow['id']]['approval_status'] = config('constant.approval_status')[$depRow['approval_status']];
                            $finalData['user'][$submissionRow['user_id_fk']]['dependant'][$depRow['id']]['is_deceased'] = $depRow['is_deceased'];
                            $finalData['user'][$submissionRow['user_id_fk']]['dependant'][$depRow['id']]['is_active'] = $depRow['is_active'];

                        }
                    }*/
                }
            }

            // enrollment/submission date filter
            $depData = Dependant::select('*')->with('user');
            if (array_key_exists('colName', $filters)) {//dd('here');
                if (array_key_exists('sdate', $filters) && array_key_exists('edate', $filters)) {
                    $depData->where($filters['colName'], '>=', $filters['sdate']);
                    $depData->where($filters['colName'], '<=', $filters['edate']);
                } else if (array_key_exists('sdate', $filters)) {
                    $depData->where($request->has('colName'), '>=', $filters['sdate']);
                } else if (array_key_exists('edate', $filters)) {
                    $depData->where($request->has('colName'), '<=', $filters['edate']);
                }
            }
            // user ids
            if (array_key_exists('empId', $filters) && $filters['empId'] != '*') {
                $depData->whereIn('user_id_fk', explode(',', $filters['empId']));
            }
            //dd($depData->toSql());
            $dependantData = $depData->get()->toArray();
            //dd($dependantData);
            if (count($dependantData)) {
                foreach ($dependantData as $depRow){
                    $finalData['dependent'][$depRow['user_id_fk']][$depRow['id']]['user_ext_id'] = $depRow['user']['user_ext_id'];
                    $finalData['dependent'][$depRow['user_id_fk']][$depRow['id']]['external_id'] = $depRow['external_id'] !== "" ? $depRow['external_id'] : null;
                    $finalData['dependent'][$depRow['user_id_fk']][$depRow['id']]['user_id_fk'] = $depRow['user_id_fk'];
                    $finalData['dependent'][$depRow['user_id_fk']][$depRow['id']]['dependent_name'] = $depRow['dependent_name'];
                    $finalData['dependent'][$depRow['user_id_fk']][$depRow['id']]['dependent_code'] = config('constant.dependant_code_ui')[$depRow['dependent_code']];
                    $finalData['dependent'][$depRow['user_id_fk']][$depRow['id']]['dob'] = strlen(str_replace([0,'-',':', ' '], '', $depRow['dob'])) ? $depRow['dob'] : null;
                    $finalData['dependent'][$depRow['user_id_fk']][$depRow['id']]['doe'] = strlen(str_replace([0,'-',':', ' '], '', $depRow['doe'])) ? $depRow['doe'] : null;
                    $finalData['dependent'][$depRow['user_id_fk']][$depRow['id']]['gender'] = $depRow['gender'] ? config('constant.gender')[$depRow['gender']] : null;
                    $finalData['dependent'][$depRow['user_id_fk']][$depRow['id']]['nominee_percentage'] = $depRow['nominee_percentage'];
                    $finalData['dependent'][$depRow['user_id_fk']][$depRow['id']]['relationship_type'] = config('constant.relationship_type')[$depRow['relationship_type']];
                    $finalData['dependent'][$depRow['user_id_fk']][$depRow['id']]['approval_status'] = config('constant.approval_status')[$depRow['approval_status']];
                    $finalData['dependent'][$depRow['user_id_fk']][$depRow['id']]['is_deceased'] = $depRow['is_deceased'];
                    $finalData['dependent'][$depRow['user_id_fk']][$depRow['id']]['is_active'] = $depRow['is_active'];
                    $finalData['dependent'][$depRow['user_id_fk']][$depRow['id']]['is_life_event'] = $depRow['is_life_event'];
                }
            }
            //dd($finalData);
            switch ($filters['output']) {
                case 'json': {
                        // return (json_encode($finalData));
                        // break;
                        return response()->json($finalData, 200);
                    }
                case 'html': {
                    }
            }
        } else {
            return response()->json(['status' => false, 'Message' => 'Invalid Request'], );
        }
    }

    public function autoSubmitEnrollment(Request $request)
    {
        $todayDate = new DateTime(); // Today
        $dates = Account::select(['enrollment_end_date'])->get()->toArray();
        if ($todayDate > $dates[0]['enrollment_end_date']) {
            $nonSubmittedEnrollmentEntries = MapUserFYPolicy::where('is_active', true)
                ->where('is_submitted', false)
                ->select(['id as MapId', 'user_id_fk'])
                ->with(['user:id,fname,lname,employee_id,email']);

            // emp only submission
            $request->has('eid') && $request['eid'] > 0 ?
                $nonSubmittedEnrollmentEntries->where('user_id_fk', $request['eid']) : '';

            $nonSubmittedEnrollmentEntries = $nonSubmittedEnrollmentEntries->get()->toArray();
            //dd($nonSubmittedEnrollmentEntries);
            $ids = $userData =  [];       // ids which will be auto submitted in map_user_fypolicy table
            $userDataCount = 0;
            if ($nonSubmittedEnrollmentEntries) {
                foreach ($nonSubmittedEnrollmentEntries as $enrolRow) {
                    $ids[] = $enrolRow['MapId'];
                    if (!array_key_exists($enrolRow['user_id_fk'], $userData)) {
                        $userDataCount++;
                        if ($request->has('output') && $request['output'] == 'html') {
                            $userData[$enrolRow['user_id_fk']] = '<tr>
                            <td>' . $userDataCount . '</td>
                            <td>' . $enrolRow['user']['employee_id'] . '</td>
                            <td>' . $enrolRow['user']['fname']  . ' ' . $enrolRow['user']['lname'] . '</td>
                            <td>' . $enrolRow['user']['email'] . '</td>
                            </tr>';
                        } else {
                            $userData[$enrolRow['user_id_fk']] =  implode('###', [
                                $userDataCount, $enrolRow['user']['employee_id'],
                                $enrolRow['user']['fname'] . ' ' . $enrolRow['user']['lname'],
                                $enrolRow['user']['email']
                            ]);
                        }
                    }
                }
                if (count($ids)) {
                    $updateData = [
                        'is_submitted' => true,
                        'modified_by' => 0,
                        'updated_at' => now()
                    ];
                    $userUpdateData = [
                        'is_enrollment_submitted' => true,
                        'enrollment_submit_date' => now(),
                        'submission_by' => '0'
                    ];

                    if ($request->has('confirmUpdate') && $request['confirmUpdate'] == 1) {
                        MapUserFYPolicy::whereIn('id', $ids)->update($updateData);
                        User::whereIn('id', array_keys($userData))->update($userUpdateData);
                    }
                }
                if ($request->has('output') && $request['output'] == 'html') {
                    echo '<style>table th,tr,td {
                        border:1px solid #222;
                        padding:0 10px;
                    }</style><table style="border:1px solid #000;">
                        <thead>
                            <th>S. No.</th>
                            <th>Employee ID</th>
                            <th>Name</th>
                            <th>Email</th>
                        </thead>
                        <tbody>
                        ' . implode('', $userData) . '
                        </tbody>
                    </table>';
                } else {
                    return json_encode(['status' => true, 'message' => json_encode($userData)]);
                }
            } else {
                return json_encode(['message' => 'No entries present for auto submission!!']);
            }
        } else {
            return json_encode(['message' => 'Auto Submission not possible before enrollment window end date']);
        }
    }
}
