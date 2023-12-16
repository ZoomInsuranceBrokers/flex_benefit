<?php

namespace App\Http\Controllers;

use DateTime;
use Dompdf\Dompdf;
use App\Models\User;
use App\Models\Account;
use App\Models\Dependent;
use App\Models\MapFYPolicy;
use Illuminate\Http\Request;
use App\Models\InsurancePolicy;
use App\Models\MapUserFYPolicy;
use App\Models\MapGradeCategory;
use App\Models\InsuranceCategory;
use Illuminate\Support\Facades\DB;
use App\Models\InsuranceSubCategory;
use Illuminate\Support\Facades\Auth;
use Facade\FlareClient\Http\Response;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Client\Response as ClientResponse;
use App\Mail\SubmitEnrollment;
use Illuminate\Support\Facades\Mail;

class EnrollmentController extends Controller
{
    public function home()
    {
        // function encryptData($data, $key, $iv) {
        //     $cipher = "aes-256-cbc";
        //     $options = 0;
        //     $encryptedData = openssl_encrypt($data, $cipher, $key, $options, $iv);
        //     return base64_encode($encryptedData);
        // }
        
        // function decryptData($encryptedData, $key, $iv) {
        //     $cipher = "aes-256-cbc";
        //     $options = 0;
        //     $decryptedData = openssl_decrypt(base64_decode($encryptedData), $cipher, $key, $options, $iv);
        //     return $decryptedData;
        // }
        
        // // Example usage:
        // $key = "your_secret_key"; // Replace with a secure key
        // $key = "QCsmMqMwEE+Iqfv0IIXDjAqrK4SOSp3tZfCadq1KlI4="; // Replace with a secure key
        // $iv = openssl_random_pseudo_bytes(16); // Initialization Vector
        // $iv = 'G4bfDHjL3gXiq5NCFFGnqQ==';
        // $dataToEncrypt = "Hello, Salesforce!";
        // $encryptedData = encryptData($dataToEncrypt, $key, $iv);
        
        // echo "Encrypted Data: " . $encryptedData . PHP_EOL;
        // $encryptedData = 'x0BhTs/2d4TR7NaEaCTJXG8hI1+jJ+OSg29ueuWZC/g=';
        // $decryptedData = decryptData($encryptedData, $key, $iv);
        // echo "Decrypted Data: " . $decryptedData . PHP_EOL;
        // exit;
        // get enrollment window and if it is open then only extract further data from db
        $accountData = Account::all()->toArray();
        $todayDate       = new DateTime(); // Today
        $enrollmentDateBegin = new DateTime($accountData[0]['enrollment_start_date']);
        $enrollmentDateEnd = new DateTime($accountData[0]['enrollment_end_date']);

        // check if data already final submission made
        $is_submitted = MapUserFYPolicy::where('user_id_fk', Auth::user()->id)->where('is_submitted', true)->get();
        if ($is_submitted->count()) {
            $is_submitted = TRUE;
        } else{
            $is_submitted = FALSE;
        }
        session(['is_submitted' => $is_submitted]);

        if ($todayDate >= $enrollmentDateBegin && $todayDate < $enrollmentDateEnd) {
            // is in between
                // category data
            $category = InsuranceCategory::where('is_active', true)->orderBy('sequence')->get();

            // sub-category data
            $data = DB::table('insurance_category as ic')
                        ->leftJoin('insurance_subcategory as isc' ,'isc.ins_category_id_fk', '=', 'ic.id')
                        ->where('ic.is_active', '=', true)
                        ->where('isc.is_active', '=', true)
                        ->select('ic.id as ic_id','ic.name as category', 'sequence', 'tagline','isc.*')
                        ->get();

            // get logged in user saved/selected policies
            $fypmapData = MapUserFYPolicy::where('is_active', true)
            ->with(['fyPolicy'])
            ->where('user_id_fk', '=', Auth::user()->id)
            //->whereRelation('policy', 'ins_subcategory_id_fk',$request->subCatId)
            ->get()->toArray();

            $currentSelectedData = $basePlan = [];
            if (count($fypmapData)) {
                foreach ($fypmapData as $fypRow) {
                    if (!$fypRow['fy_policy']['policy']['is_base_plan']) {
                        $currentSelectedData[$fypRow['fy_policy']['policy']['ins_subcategory_id_fk']][] = [
                            'polName' => $fypRow['fy_policy']['policy']['name'], 'points' => $fypRow['points_used']];
                    }
                }
            }

            $basePlan = InsurancePolicy::where('is_base_plan', 1)
                    ->orWhere('is_default_selection', 1)
                    ->where('is_active',1)
                    ->with('subcategory')
                    ->get()->toArray();
            //dd($basePlan);
            

            // mappedgrade data
            $mappedGradeData = User::where('id', Auth::user()->id)
                    ->with(['grade'])
                    ->whereRelation('grade', 'id', Auth::user()->grade_id_fk)
                    ->get()->toArray();
            $gradeData = [];
            if(count($mappedGradeData)) {
                foreach($mappedGradeData[0]['grade']['category_mapping'] as $gradeCatData) {
                    $gradeData[$gradeCatData['category_id_fk']] = $gradeCatData['amount'];
                }
            }

            session(['gradeData' => $gradeData]);

            // dependent
            $dependents = Dependent::where('is_active', config('constant.$_YES'))
                                    //->where('is_deceased',config('constant.$_NO'))
                                    ->where('user_id_fk',Auth::user()->id)
                                    ->where('is_deceased',config('constant.$_NO'))
                                    ->get();

            $viewArray = ['sub_categories_data' => $data->toArray(), 
                    'category' => $category->toArray(),
                    'currentSelectedData' => $currentSelectedData,
                    'basePlan' => $basePlan,
                    'gradeAmtData' => $gradeData,
                    'dependent' => $dependents->toArray(),
                    'is_enrollment_window' => true
                ];
        } else {
            $viewArray = ['is_enrollment_window' => false];
        }
        return view('enrollment')->with('data', $viewArray);
    }

    public function getInsuranceListBySubCategory(Request $request)
    {
        $userPolData = DB::table('map_user_fypolicy as mufyp')
        ->leftJoin('map_financial_year_policy as mfyp' ,'mufyp.fypolicy_id_fk', '=', 'mfyp.id')
        ->leftJoin('financial_years as fy' ,'fy.id', '=', 'mfyp.fy_id_fk')
        ->leftJoin('insurance_policy as ip' ,'ip.id', '=', 'mfyp.ins_policy_id_fk')
        ->where('mufyp.user_id_fk', '=', Auth::user()->id)
        ->where('ip.ins_subcategory_id_fk', '=', $request->subCatId)
        ->where('ip.is_base_plan', '<>', config('constant.$_YES'))
        ->where('mufyp.is_active', '=', config('constant.$_YES'))
        ->where('mfyp.is_active', '=', config('constant.$_YES'))
        ->where('fy.is_active', '=', config('constant.$_YES'))
        ->where('ip.is_active', '=', config('constant.$_YES'))
        ->select('mufyp.id as mufypId','mfyp.id as mfypId','mufyp.points_used','fy.name as fy_name','fy.start_date','fy.end_date', 'ip.id as ip_id')
        ->get()->toArray();

        //if(count($userPolData)) {            
            // $activePolicyForSubCategory = InsurancePolicy::orderBy('name')
            //     ->where('is_active', true)
            //     ->with(['currency','map_fy_policies'])
            //     ->where('ins_subcategory_id_fk',$request->subCatId)
            //     ->get()
            //     ->toArray();
            // dd($activePolicyForSubCategory);
            $activePolicyForSubCategoryFY = MapFYPolicy::where('is_active', true)
                        ->with(['financialYears','policy'])
                        ->whereRelation('policy', 'ins_subcategory_id_fk',$request->subCatId)
                        ->get()->toArray();
            //dd($activePolicyForSubCategoryFY);

            

            // $activePolicyForSubCategory = DB::table('insurance_policy as ip')
            //     ->leftJoin('map_financial_year_policy as mfyp' ,'ip.id', '=', 'mfyp.id')
            //     ->leftJoin('financial_years as fy' ,'fy.id', '=', 'mfyp.fy_id_fk')
            //     ->leftJoin('insurance_policy as ip' ,'ip.id', '=', 'mfyp.ins_policy_id_fk')
            //     ->where('mufyp.user_id_fk', '=', 1) // @todo: change logic to acutal logged in user
            //     ->where('mufyp.is_active', '=', true)
            //     ->where('mfyp.is_active', '=', true)
            //     ->where('fy.is_active', '=', true)
            //     ->where('ins_subcategory_id_fk',$request->subCatId)
            //     ->get()->toArray();

            //get subcategory and category for user grade
            $gradeData = session('gradeData');
            $gradeAmount = 0;
            $subCatData = null;
            if(count($gradeData)) {
                foreach ($gradeData as $gradeCatId => $gradeCatAmount){
                    $subCatData = InsuranceSubCategory::with('categories')
                        ->where('id', $request->subCatId)
                        ->whereRelation('categories','id', $gradeCatId)
                        ->get()->toArray();

                        if (count($subCatData)) {
                            $gradeAmount = $gradeCatAmount;
                            break;
                        }
                }
            }

            //dd($userPolData);
            //if (count($userPolData) && count($activePolicyForSubCategory) ) {
                $html = view('_partial.sub-category-details')
                        ->with('userPolData', $userPolData)
                        ->with('gradeAmount', $gradeAmount)
                        ->with('activePolicyForSubCategoryFY',$activePolicyForSubCategoryFY)->render();
                return response()->json([
                    'status' => true,
                    'html' => $html,
                    'message' => 'success'
                ]);
        /*} else{
            return response()->json([
                'status' => true,
                'html' => '<h1>No active policy exist for selected subcategory</h1>',
                'message' => 'error'
            ]);
        }*/

        //dd($userPolData);
        
        //} else {
        //    return false;
        //}
    }

    public function saveEnrollment(Request $request) {
        $fypmap = $request->fypmap;
        $catId = $request->catId;
        $policyId = $request->policyId;
        $selDep = $request->sd;     // selected dependents
        $userId = Auth::user()->id;
        $summary = $request->summary;
        $points = $request->points;

        $userPolDataForCatId = DB::table('map_user_fypolicy as mufyp')
        ->select('mufyp.points_used','mufyp.id as userFYPolMapId', 'mufyp.fypolicy_id_fk as fyPolMapId',
            'ip.id as ip_id','ip.is_base_plan')
        ->leftJoin('map_financial_year_policy as mfyp' ,'mufyp.fypolicy_id_fk', '=', 'mfyp.id')
        ->leftJoin('financial_years as fy' ,'fy.id', '=', 'mfyp.fy_id_fk')
        ->leftJoin('insurance_policy as ip' ,'ip.id', '=', 'mfyp.ins_policy_id_fk')
        ->where('mufyp.user_id_fk', '=', $userId)
        ->where('mufyp.is_active', '=', true)
        ->where('mfyp.is_active', '=', true)
        ->where('fy.is_active', '=', true)
        ->where('ip.is_active', '=', true)
        ->where('ip.ins_subcategory_id_fk', '=', (int)$catId)
        ->get()->toArray();
        //->toSql();
        //print '<pre>';
        //dd($userPolDataForCatId);
        $data = [
            'user_id_fk' => $userId,
            'fypolicy_id_fk' => $fypmap,
            'selected_dependent' => $selDep,
            'encoded_summary' => $summary,
            'points_used' => $points,
            'created_by' => $userId,
            'modified_by' => $userId,
        ];
        //dd($data);
        $whereConditionData = ['id' => -1, 'user_id_fk' => -1];
        $mapUserFYPolicyRow = $message = null;
        $savedPoints = 0;
        $status = false;
        $user = User::where('id',Auth::user()->id)->get()->toArray();
        //if (count($userPolDataForCatId) > 0) {
            foreach($userPolDataForCatId as $item) {
                if($item->is_base_plan) {
                    continue;
                } else {
                    $whereConditionData['id'] = (int)$item->userFYPolMapId; 
                    $whereConditionData['user_id_fk'] = $userId; 
                    unset($data['created_by']);
                    $savedPoints += $item->points_used;
                }
            }
            
            $mapUserFYPolicyRow = MapUserFYPolicy::updateOrCreate($whereConditionData,$data);
            //dd($mapUserFYPolicyRow);
            // update existing points in case policy is changed
            $userData = [
                'points_available'=> $user[0]['points_available'] + $savedPoints - $points,
                'points_used'=> $user[0]['points_used'] - $savedPoints + $points,
            ];
            
            $status = true;
            User::where('id',Auth::user()->id)->update($userData);
            $message = 'Data ' . ($whereConditionData['id'] != -1 ? 'updated' : 'saved') . ' successfully. You need to do final submission(only one submission allowed per user per financial year) of data across policies/categories from "Summary" section post review';
        
        return response()->json([
            'status' => $status,
            //'responseRow' => $mapUserFYPolicyRow,
            'message' => $message
        ]);
    }

    public function saveEnrollmentPV(Request $request) {
        $catId = $request->catId;
        $savePoints = [];
        $userId = Auth::user()->id;
        $summary = [];
        $ids = [];
        foreach (json_decode(base64_decode($request->summary)) as $summItem) {
            $summRow = explode(':', $summItem);
            $summary[$summRow[0]][$summRow[1]] = $summRow[2];
        }

        $summary = array_map("json_encode",$summary);
        $summary = array_map("base64_encode",$summary);
        foreach (json_decode(base64_decode($request->savePoints)) as $spItem) {
            $spRow = explode(':', $spItem);
            $savePoints[$spRow[0]] = (int)$spRow[1];
        }

        $userPolDataForCatId = DB::table('map_user_fypolicy as mufyp')
        ->select('mufyp.points_used','mufyp.id')
        ->leftJoin('map_financial_year_policy as mfyp' ,'mufyp.fypolicy_id_fk', '=', 'mfyp.id')
        ->leftJoin('financial_years as fy' ,'fy.id', '=', 'mfyp.fy_id_fk')
        ->leftJoin('insurance_policy as ip' ,'ip.id', '=', 'mfyp.ins_policy_id_fk')
        ->where('mufyp.user_id_fk', '=', $userId)
        ->where('mufyp.is_active', '=', true)
        ->where('mfyp.is_active', '=', true)
        ->where('fy.is_active', '=', true)
        ->where('ip.is_active', '=', true)
        ->where('ip.ins_subcategory_id_fk', '=', (int)$catId)
        ->get()->toArray();
        //->toSql();
        //print '<pre>';
        
        //dd($userPolDataForCatId);
        $totalPointsSaved = 0; 
        $user = User::where('id',Auth::user()->id)->get()->toArray();
        if (count($userPolDataForCatId)) {
            foreach ($userPolDataForCatId as $fypmapEntry) {
                $ids[] = $fypmapEntry->id;
                $totalPointsSaved += $fypmapEntry->points_used;
            }
            MapUserFYPolicy::whereIn('id', $ids)->delete();       // deleting existing record on every save as updating existing ones may cause corrupted data
        }
        $finalData = [];
        $pointsCounter = 0;
        foreach ($savePoints as $fypmap => $points) {
            $finalData[] = [
                'user_id_fk' => $userId,
                'fypolicy_id_fk' => $fypmap,
                'points_used' => $points,
                'encoded_summary' => $summary[$fypmap],
                'created_by' => $userId,
                'modified_by' => $userId
            ];
            $pointsCounter += $points;
        }

        $message = null;
        $fypmapModel = new MapUserFYPolicy();
        $status = DB::table($fypmapModel->getTable())->insert($finalData);
        if ($status) {
            $message = 'Data saved successfully. You need to do final submission(only one submission allowed per user per financial year) of data across policies/categories from "Summary" section post review';
            
            // update total points
            $userData = [
                    'points_available'=> $user[0]['points_available'] - $pointsCounter  + $totalPointsSaved,
                    'points_used'=> $user[0]['points_used'] + $pointsCounter  - $totalPointsSaved,
                ];
            User::where('id',Auth::user()->id)->update($userData);

            return response()->json([
                'status' => $status,
                //'responseRow' => $mapUserFYPolicyRow,
                'message' => $message
            ]);
        } else {
            return response()->json([
                'status' => $status,
                //'responseRow' => $mapUserFYPolicyRow,
                'message' => 'ERROR'
            ]);
        }
        
    }

    public function loadSummary(){
        $mapUserFYPolicyData = MapUserFYPolicy::where('user_id_fk', '=', Auth::user()->id)->with(['fyPolicy'])
                ->get()->toArray();
        $html = view('summary')
            ->with('mapUserFYPolicyData', $mapUserFYPolicyData)->render();
        return $html;
    }

    public function downloadSummary (){
        $mapUserFYPolicyData = MapUserFYPolicy::where('user_id_fk', '=', Auth::user()->id)->with(['fyPolicy'])
                ->get()->toArray();

        $html = view('summaryDownload')
            ->with('mapUserFYPolicyData', $mapUserFYPolicyData)->render();
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'landscape');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream();
    }

    public function submitEnrollment(Request $request) {
        if($request->session()->has('is_submitted') && !session('is_submitted')) {
            MapUserFYPolicy::where('user_id_fk', Auth::user()->id)
            ->where('is_active', config('constant.$_YES'))
            ->update(['is_submitted' => config('constant.$_YES'), 'modified_by'=> Auth::user()->id]);
            $email =  Auth::user()->email;
            $user = DB::table('users')->where('email', $email)->first();
            Mail::to($email)->send(new SubmitEnrollment($user));
            
            return json_encode(['status' => true, 'msg'=> 'Submission Successfull!!']);

           

        }
    }
}
