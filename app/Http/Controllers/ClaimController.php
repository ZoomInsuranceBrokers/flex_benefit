<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ClaimController extends Controller
{
    public function loadNetworkHospital()
    {
        // dd($request->method());
        if (Auth::check()) {
            return view('networkHospital');
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
            if($request->method() == "POST"){
                $arr = ["StateID"=>"RAJASTHAN",
                    "UWcode"=>"UNITED INDIA INSURANCE COMPANY",
                    "HospCity"=>"",
                    "HospitalName"=>""];
                $response = Http::withBody(json_encode($arr), 'text/json')
                        ->post('http://brokerapi.safewaytpa.in/api/Hospitalsearch')->json();
                $recordCount = count($response['HospitalList1']);
                $page = $startIndex/$pageSize;
                $responseChunks = array_chunk($response['HospitalList1'], $pageSize);
                if($recordCount) {
                    foreach($responseChunks[$page] as $k => $resItem) {
                        $responseChunks[$page][$k]['location'] = '<a href="https://maps.google.com/?q= ' 
                            . $resItem['latitude'] . ',' . $resItem['longitude'] . '">Locate</a>';
                    }
                }
                //dd($responseChunks);
                if ($response['Status'] == 1) {
                    $jTableResult['Result'] = "OK";
                    $jTableResult['TotalRecordCount'] = $recordCount;
                    $jTableResult['Records'] = $responseChunks[$page];
                    return json_encode($jTableResult);
                }
            }     
            
        } else {
            return redirect('/');
        }
    }

    public function loadClaimIntimation(){

    }

    public function saveClaimIntimation(){

    }

    public function loadReimbursement(){

    }

    public function saveClaimReimbursement(){

    }

    public function submitClaimReimbursement(){
        
    }


}
