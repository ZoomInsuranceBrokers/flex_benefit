<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;


class ClaimController extends Controller
{
    public function loadNetworkHospital($tpa)
    {
        // dd($request->method());
        if (Auth::check()) {
            if ($tpa == "phs") {
                return view('tpa/phs/networkHostpital');
            } else {
                echo "tpa integration is in process";
                exit;
            }
            // return view('networkHospital');
        } else {
            redirect('/');
        }
    }

    public function searchNetworkHospital(Request $request)
    {
        //dd($request->query('jtPageSize'));
        $pageSize = $request->query('jtPageSize');
        $startIndex = $request->query('jtStartIndex');
        if (Auth::check()) {

            if ($request->tpa == "phs") {
                $pincode = $request->pincode;

                $policy_no = "323700/34/22/04/00000027";

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

                $apilog = array(
                    'tpa_company' => 'phs',
                    'request' => $data2,
                    'response' => $response
                );

                $responseData = json_decode($response, true); // Decode the JSON response

                if (isset($responseData['GetHospitalListResult'])) {
                    $hospitalList = $responseData['GetHospitalListResult'];

                    // Add the location link to each record
                    foreach ($hospitalList as &$hospital) {
                        $hospital['location'] = '<a href="https://maps.google.com/?q=' . $hospital['LATITUDE'] . ',' . $hospital['LONGITUDE'] . '">Locate</a>';
                    }

                    // Prepare the modified response
                    $jTableResult = [
                        'Result' => 'OK',
                        'TotalRecordCount' => count($hospitalList),
                        'Records' => $hospitalList,
                    ];

                    return json_encode($jTableResult);
                } else {
                    return json_encode(['Result' => 'Error', 'Message' => 'Invalid response structure']);
                }
            }
            // if ($request->method() == "POST") {
            //     $arr = [
            //         "StateID" => "RAJASTHAN",
            //         "UWcode" => "UNITED INDIA INSURANCE COMPANY",
            //         "HospCity" => "",
            //         "HospitalName" => ""
            //     ];
            //     $response = Http::withBody(json_encode($arr), 'text/json')
            //         ->post('http://brokerapi.safewaytpa.in/api/Hospitalsearch')->json();
            // $recordCount = count($response['HospitalList1']);
            // $page = $startIndex / $pageSize;
            // $responseChunks = array_chunk($response['HospitalList1'], $pageSize);
            // if ($recordCount) {
            //     foreach ($responseChunks[$page] as $k => $resItem) {
            //         $responseChunks[$page][$k]['location'] = '<a href="https://maps.google.com/?q= '
            //             . $resItem['latitude'] . ',' . $resItem['longitude'] . '">Locate</a>';
            //     }
            // }
            // //dd($responseChunks);
            // if ($response['Status'] == 1) {
            //     $jTableResult['Result'] = "OK";
            //     $jTableResult['TotalRecordCount'] = $recordCount;
            //     $jTableResult['Records'] = $responseChunks[$page];
            //     return json_encode($jTableResult);
            // }
            // }
        } else {
            return redirect('/');
        }
    }


    public function loadClaimIntimation()
    {
        $tpa = "phs";
        if (Auth::check()) {
            if ($tpa == "phs") {
                return view('tpa/phs/claimIntimation');
            } else {
                echo "tpa integration is in process";
                exit;
            }
            // return view('networkHospital');
        } else {
            redirect('/');
        }
    }

    public function saveClaimIntimation(Request $request)
    {
       
        $tpa = "phs";
        if (Auth::check()) {
            if ($tpa == "phs") {
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
                    // ... (rest of your data)
                ];
                print_r($data2);
                exit;

                $response = Http::post('https://webintegrations.paramounttpa.com/ZoomBrokerAPI/Service1.svc/INTIMATE_CLAIM', $data2);

                // Handle the response
                $responseBody = $response->json();

                if (isset($responseBody['INTIMATE_CLAIMResult'][0]['CLAIM_INTIMATION_NUMBER'])) {
                    $claimReferenceNo = $responseBody['INTIMATE_CLAIMResult'][0]['CLAIM_INTIMATION_NUMBER'];

                    // Send email
                    $this->email_model->sendClaimSubmissionToRm($employee, $claimReferenceNo);

                    return redirect()->back()->with('success', 'Claim Intimated Successfully! Claim Intimation No: ' . $claimReferenceNo);
                } else {
                    return redirect()->back()->with('error', 'Error in response data');
                }
            } else {
                echo "tpa integration is in process";
                exit;
            }
            // return view('networkHospital');
        } else {
            redirect('/');
        }
    }

    public function loadReimbursement()
    {
    }

    public function saveClaimReimbursement()
    {
    }

    public function submitClaimReimbursement()
    {
    }
}
