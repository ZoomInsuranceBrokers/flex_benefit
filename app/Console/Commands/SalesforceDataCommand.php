<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DateTime;
use Exception;
use App\Models\User;
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

class SalesforceDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'salesforce:data';

    protected $description = 'Retrieve and process Salesforce data';


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            
            session(['confirmUpdate' => true]);
            $accessToken = $this->getAccessToken();

            $salesforceData = $this->getSalesforceDependentDataUsingToken($accessToken);
            } catch (\Exception $e) {
            // Log any errors
            \Log::error('ProcessSalesforceData error: ' . $e->getMessage());
        }
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
                            $email = $formattedData['user'][$jsonRow['Details']['Id']]['email'];
                            $users = $formattedData['user'][$jsonRow['Details']['Id']]['fname'];

                            Mail::to($email)->send(new NewJoiningCredentials($users));
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
}
