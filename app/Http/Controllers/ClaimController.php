<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class ClaimController extends Controller
{
    public function loadNetworkHospital()
    {
        // dd($request->method());
        if (Auth::check()) {
            $currentDate = now();

            $policy_details = DB::table('policy_master')
                ->whereDate('policy_start_date', '<=', $currentDate)
                ->whereDate('policy_end_date', '>=', $currentDate)
                ->first();

            switch ($policy_details->tpa_id) {
                case 62:
                    return view('tpa/phs/networkHostpital', compact('policy_details'));
                    break;
                default:
                    echo "TPA INTEGRATION IS IN PROCESS";
                    exit;
                    break;
            }
        } else {
            redirect('/');
        }
    }

    public function searchNetworkHospital(Request $request)
    {
        //dd($request->query('jtPageSize'));

        if (Auth::check()) {

            switch ($request->tpa) {
                case 62:
                    $this->phs_network_hospital($request);
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


    public function loadClaimIntimation()
    {

        if (Auth::check()) {
            $currentDate = now(); // or \Carbon\Carbon::now() for more control

            $policy_details = DB::table('policy_master')
                ->whereDate('policy_start_date', '<=', $currentDate)
                ->whereDate('policy_end_date', '>=', $currentDate)
                ->first();

            switch ($policy_details->tpa_id) {
                case 62:
                    return view('tpa.phs.claimIntimation', compact('policy_details'));
                    break;
                default:
                    echo "TPA INTEGRATION IS IN PROCESS";
                    exit;
                    break;
            }
        } else {
            redirect('/');
        }
    }

    public function saveClaimIntimation(Request $request)
    {

        if (Auth::check()) {
            switch ($request->tpa_id) {
                case 62:
                    $this->phs_save_claim_intimation($request);
                    break;
                default:
                    echo "TPA INTEGRATION IS IN PROCESS";
                    exit;
                    break;
            }
        } else {
            redirect('/');
        }
    }


    public function trackClaimStatus()
    {
        if (Auth::check()) {
            $currentDate = now(); // or \Carbon\Carbon::now() for more control

            $policy_details = DB::table('policy_master')
                ->whereDate('policy_start_date', '<=', $currentDate)
                ->whereDate('policy_end_date', '>=', $currentDate)
                ->first();

            switch ($policy_details->tpa_id) {
                case 62:
                    $claims = $this->phs_claim_details($policy_details);

                    return view('tpa.phs.claim-status', compact('claims'));
                default:
                    echo "TPA INTEGRATION IS IN PROCESS";
                    exit;
                    break;
            }
        } else {
            redirect('/');
        }
    }

    ////////////////////////////////////////////////////////////////////////////
    ////////////////////////// PHS Tpa Integration //////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////

    public function phs_save_claim_intimation($request)
    {
        $validator = Validator::make(request()->all(), [
            'policy_no' => 'required|max:100',
            'dependent_relation' => 'required|max:100',
            'dependent_name' => 'required|max:100',
            'claim_type' => 'required|max:100',
            'claim_disease' => 'required|max:100',
            'claim_amt' => 'required|max:100',
            'claim_date_of_admission' => 'required|max:100',
            'claim_name_of_hospital' => 'required|max:100',
            'claim_name_of_doctor' => 'required|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }



        $data2 = [
            "USERNAME" => "ZOOM-ADMIN",
            "PASSWORD" => "ADMIN-USER@389",
        ];


        $response = Http::post('https://webintegrations.paramounttpa.com/ZoomBrokerAPI/Service1.svc/INTIMATE_CLAIM', $data2);

        $responseBody = $response->json();

        if (isset($responseBody['INTIMATE_CLAIMResult'][0]['CLAIM_INTIMATION_NUMBER'])) {
            $claimReferenceNo = $responseBody['INTIMATE_CLAIMResult'][0]['CLAIM_INTIMATION_NUMBER'];

            $this->email_model->sendClaimSubmissionToRm($employee, $claimReferenceNo);

            return redirect()->back()->with('success', 'Claim Intimated Successfully! Claim Intimation No: ' . $claimReferenceNo);
        } else {
            return redirect()->back()->with('error', 'Error in response data');
        }
    }

    public function phs_network_hospital($request)
    {
        $pageSize = $request->query('jtPageSize');
        $startIndex = $request->query('jtStartIndex');
        $pincode = $request->pincode;

        $policy_no = $request->policy_no;

        $curl = curl_init();

        $data2 = json_encode(
            array(
                "USERNAME" => "ZOOM-ADMIN",
                "PASSWORD" => "ADMIN-USER@389",
                "PIN_CODE" => $pincode,
                "POLICY_NO" => $policy_no,
            )
        );


        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => 'https://webintegrations.paramounttpa.com/ZoomBrokerAPI/Service1.svc/GetHospitalList',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_HTTPHEADER => array('Content-Type:application/json'),
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $data2,
            )
        );

        $response = curl_exec($curl);

        $responseData = json_decode($response, true); // Decode the JSON response

        if (isset($responseData['GetHospitalListResult'])) {
            $hospitalList = $responseData['GetHospitalListResult'];

            // Add the location link to each record
            foreach ($hospitalList as $hospital) {
                $hospital['location'] = '<a href="https://maps.google.com/?q=' . $hospital['LATITUDE'] . ',' . $hospital['LONGITUDE'] . '">Locate</a>';
            }

            // Prepare the modified response
            $jTableResult = [
                'Result' => 'OK',
                'TotalRecordCount' => count($hospitalList),
                'Records' => $hospitalList,
            ];

            echo json_encode($jTableResult);
        }
    }


    public function phs_claim_details($data)
    {

        $gcld = 0;
        gcld:

        $curl = curl_init();

        $data2 = json_encode(array(
            'USERNAME' => 'ZOOM-ADMIN',
            'PASSWORD' => 'ADMIN-USER@389',
            'POLICY_NO' => $data->policy_number,
            'EMPLOYEE_NO' => Auth::user()->employee_id,
            'CLAIM_NO' => ""
        ));

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://webintegrations.paramounttpa.com/ZoomBrokerAPI/Service1.svc/GetClaimStatus',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_HTTPHEADER => array('Content-Type:application/json'),
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $data2,
        ));


        $response = curl_exec($curl);

        curl_close($curl);

        $response = json_decode($response);


        if ((empty($response) || (gettype($response) == 'string')) || (trim($response->GetClaimStatusResult[0]->MESSAGE) == 'Invalid Policy Number')) {

            $gcld++;

            if ($gcld < 10) {
                goto gcld;
            } else {
                echo 'Looks Like an Error Occured! Kindly Refresh Page';
                exit;
            }
        } else {

            if ((trim($response->GetClaimStatusResult[0]->MESSAGE) != 'Invalid Policy Number') && (trim($response->GetClaimStatusResult[0]->MESSAGE) != 'No data Found')) {

                $claims = [];
                foreach ($response->GetClaimStatusResult as $phs_claim) {


                    $claim = array(
                        'policy' => $data->policy_number,
                        'insurance_company' => "",
                        'tpa_company' => 'PHS',
                        'policy_number' => $data->policy_number,
                        'policy_name' => $data->policy_name,
                        'employee_name' =>  Auth::user()->fname,
                        'employee_id' =>  Auth::user()->employee_id,
                        'patient_name' => $phs_claim->MEMBER_NAME,
                        'patient_relation' => $phs_claim->RELATION,
                        'date_of_birth' => '',
                        'hospital_name' => $phs_claim->hospital_name ?? '',
                        'ailment' => '',
                        // 'date_of_admission' => date('M d, Y', strtotime($phs_claim->date_of_admission)) ?? '',
                        'date_of_discharge' => '',
                        'claim_amount' => $phs_claim->APPROVED_AMT ?? '',
                        'message' => $phs_claim->MESSAGE ?? ''
                    );

                    // switch ($phs_claim->TYPE_OF_CLAIM) {
                    // case 'REIMBURSEMENT':
                    $claim['last_query_reason'] = '';
                    $claim['query_letter'] = '';
                    $claim['paid_amt'] = '';
                    $claim['deduction_reasons'] = '';
                    $claim['settlment_letter'] = '';
                    $claim['tpa_claim_id'] = $phs_claim->UNIQUE_CLAIM_NO ?? '';
                    $claim['claim_intimation_no'] = '';
                    $claim['type_of_claim'] = $phs_claim->TYPE_OF_CLAIM ?? '';
                    $claim['claim_mode'] = $phs_claim->TYPE_OF_CLAIM  ?? '';
                    $claim['rejection_date'] = '';
                    $claim['rejection_reason'] = '';
                    $claim['claim_status'] = $phs_claim->CLAIM_STATUS;
                    // break;
                    // }
                    // Push the claim only if UNIQUE_CLAIM_NO is not null
                    if ($claim['tpa_claim_id'] !== null) {
                        array_push($claims, $claim);
                    }
                }
            }
        }

        return $claims;
    }
}
