<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\MapUserFYPolicy;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

class ApiController extends Controller
{
    public $secret_key = 'x409z636R3vFRPttwT26jkdwbdewidJN1bncwi2gpT';
    public function getAllUsers(Request $request)
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
        ])->post('https://zoominsurancebrokers--dw.sandbox.my.salesforce.com/services/apexrest/getUpdates', [
            'ids' => ['001D600001jFw3IIAS'],
            'reqtype' => 'CLIENT_POLICY_SCHEMA',
        ]);

        return $response->json();
    }

    public function getUserEnrollmentData (Request $request) {
        if ($request->isMethod('get') && $request->has('authKey') && 
            $request->authKey == base64_encode(env('APP_API_SECRET_KEY') . '@' . date('d-m-Y'))) {
            $filters = ['output' => 'json','active' => true];
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
                $finalData['account'] = Account::where('id',$filters['accId'])
                ->whereOr('external_id',$filters['accId'])
                ->select('id','external_id','name','enrollment_start_date','enrollment_end_date')
                ->get()->toArray();
            }
            // enrollment/submission date filter
            $mapUserFYPolicyData = MapUserFYPolicy::with(['fyPolicy','user']);
            if (array_key_exists('colName', $filters)) {
                if(array_key_exists('sdate', $filters) && array_key_exists('edate', $filters)){
                    $mapUserFYPolicyData->whereBetween($request->has('colName'),[$filters['sdate'], $filters['edate']]);
                } else if (array_key_exists('sdate', $filters)){
                    $mapUserFYPolicyData->where($request->has('colName'), '>=',$filters['sdate']);
                } else if (array_key_exists('edate', $filters)){
                    $mapUserFYPolicyData->where($request->has('colName'), '<=',$filters['edate']);
                }
            }

            // record active status
            if($filters['active'] != '*'){
                $mapUserFYPolicyData->where('is_active',$filters['active']);
            }

            // user ids
            if (array_key_exists('empId', $filters) && $filters['empId'] != '*') {
                $mapUserFYPolicyData->whereIn('user_id_fk',[$filters['empId']]);
            }

            $submissionData = $mapUserFYPolicyData->get()->toArray();
            //dd($submissionData);
            if (count($submissionData)) {
                foreach($submissionData as $submissionRow) {
                    //dd($submissionRow);
                    // policy data
                    $policyData = $submissionRow['fy_policy']['policy'];
                    $finalData['user'][$submissionRow['user_id_fk']]['policy'][$submissionRow['id']]['policy_id'] = $policyData['id'];
                    $finalData['user'][$submissionRow['user_id_fk']]['policy'][$submissionRow['id']]['policy_name'] = $policyData['name'];
                    $finalData['user'][$submissionRow['user_id_fk']]['policy'][$submissionRow['id']]['external_id'] = $policyData['external_id'];
                    $finalData['user'][$submissionRow['user_id_fk']]['policy'][$submissionRow['id']]['points_used'] = $submissionRow['points_used'];
                    $finalData['user'][$submissionRow['user_id_fk']]['policy'][$submissionRow['id']]['selected_dependent'] = $submissionRow['selected_dependent'];
                    $finalData['user'][$submissionRow['user_id_fk']]['policy'][$submissionRow['id']]['encoded_summary'] = $submissionRow['encoded_summary'];

                    // user data
                    $userData = $submissionRow['user'];
                    $finalData['user'][$submissionRow['user_id_fk']]['id'] = $userData['id'];
                    $finalData['user'][$submissionRow['user_id_fk']]['external_id'] = $userData['external_id'];
                    $finalData['user'][$submissionRow['user_id_fk']]['fname'] = $userData['fname'];
                    $finalData['user'][$submissionRow['user_id_fk']]['lname'] = $userData['lname'];
                    $finalData['user'][$submissionRow['user_id_fk']]['hire_date'] = $userData['hire_date'];
                    $finalData['user'][$submissionRow['user_id_fk']]['points_used'] = $userData['points_used'];
                    $finalData['user'][$submissionRow['user_id_fk']]['points_available'] = $userData['points_available'];
                    $finalData['user'][$submissionRow['user_id_fk']]['gender'] = config('constant.gender')[$userData['gender']];
                    $finalData['user'][$submissionRow['user_id_fk']]['email'] = $userData['email'];

                    // dependent data
                    $dependentData = $submissionRow['user']['dependent'];
                    if (count($dependentData)) {
                        foreach ($dependentData as $depRow){
                            $finalData['user'][$submissionRow['user_id_fk']]['dependent'][$depRow['id']]['external_id'] = $depRow['external_id'];
                            $finalData['user'][$submissionRow['user_id_fk']]['dependent'][$depRow['id']]['dependent_name'] = $depRow['dependent_name'];
                            $finalData['user'][$submissionRow['user_id_fk']]['dependent'][$depRow['id']]['dependent_code'] = config('constant.dependent_code_ui')[$depRow['dependent_code']];
                            $finalData['user'][$submissionRow['user_id_fk']]['dependent'][$depRow['id']]['dob'] = $depRow['dob'];
                            $finalData['user'][$submissionRow['user_id_fk']]['dependent'][$depRow['id']]['gender'] = config('constant.gender')[$depRow['gender']];
                            $finalData['user'][$submissionRow['user_id_fk']]['dependent'][$depRow['id']]['nominee_percentage'] = $depRow['nominee_percentage'];
                            $finalData['user'][$submissionRow['user_id_fk']]['dependent'][$depRow['id']]['relationship_type'] = config('constant.relationship_type')[$depRow['relationship_type']];
                            $finalData['user'][$submissionRow['user_id_fk']]['dependent'][$depRow['id']]['approval_status'] = config('constant.approval_status')[$depRow['approval_status']];
                            $finalData['user'][$submissionRow['user_id_fk']]['dependent'][$depRow['id']]['is_deceased'] = $depRow['is_deceased'];
                            $finalData['user'][$submissionRow['user_id_fk']]['dependent'][$depRow['id']]['is_active'] = $depRow['is_active'];
                        }
                    }
                }
            }
            //dd($finalData);
            switch($filters['output']){
                case 'json' : {
                    return (json_encode($finalData));
                    break;
                }
                case 'html': {
                }
            }
        } else {
            return json_encode(['status'=> false, 'Message'=> 'Invalid Request']);
        }
    }
}
