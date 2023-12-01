<?php

namespace App\Http\Controllers;

use Dompdf\Dompdf;
use App\Models\User;
use App\Models\Dependent;
use App\Models\MapFYPolicy;
use Illuminate\Http\Request;
use App\Models\InsurancePolicy;
use App\Models\MapUserFYPolicy;
use App\Models\InsuranceCategory;
use Illuminate\Support\Facades\DB;
use App\Models\InsuranceSubCategory;
use Illuminate\Support\Facades\Auth;
use Facade\FlareClient\Http\Response;
use Illuminate\Http\Client\Response as ClientResponse;

class EnrollmentController extends Controller
{
    public function home()
    {
        // category data
        $category = InsuranceCategory::where('is_active', true)->orderBy('sequence')->get();

        // sub-category data
        $data = DB::table('insurance_category as ic')
                    ->leftJoin('insurance_subcategory as isc' ,'isc.ins_category_id_fk', '=', 'ic.id')
                    ->where('ic.is_active', '=', true)
                    ->where('isc.is_active', '=', true)
                    ->select('ic.id as ic_id','ic.name as category', 'sequence', 'tagline','isc.*')
                    ->get();

        // dependent
        $dependents = Dependent::where('is_active', config('constant.$_YES'))
                                //->where('is_deceased',config('constant.$_NO'))
                                ->where('user_id_fk',Auth::user()->id)
                                ->where('is_deceased',config('constant.$_NO'))
                                ->get();

        return view('enrollment')->with('data', 
        [   'sub_categories_data' => $data->toArray(), 
            'category' => $category->toArray(),
            'dependent' => $dependents->toArray()
        ]);
    }

    public function getInsuranceListBySubCategory(Request $request)
    {
        $userPolData = DB::table('map_user_fypolicy as mufyp')
        ->leftJoin('map_financial_year_policy as mfyp' ,'mufyp.fypolicy_id_fk', '=', 'mfyp.id')
        ->leftJoin('financial_years as fy' ,'fy.id', '=', 'mfyp.fy_id_fk')
        ->leftJoin('insurance_policy as ip' ,'ip.id', '=', 'mfyp.ins_policy_id_fk')
        //->where('mufyp.user_id_fk', '=', Auth::user()->id)
        ->where('mufyp.user_id_fk', '=', 1)
        ->where('mufyp.is_active', '=', true)
        ->where('mfyp.is_active', '=', true)
        ->where('fy.is_active', '=', true)
        ->where('ip.is_active', '=', true)
        ->select('mufyp.points_used','fy.name as fy_name','fy.start_date','fy.end_date', 'ip.id as ip_id')
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

            //if (count($userPolData) && count($activePolicyForSubCategory) ) {
                $html = view('_partial.sub-category-details')
                        ->with('userPolData', $userPolData)
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
        $userId = Auth::user()->id;
        $summary = $request->summary;
        $points = $request->points;

        $userPolDataForCatId = DB::table('map_user_fypolicy as mufyp')
        ->select(/*'mufyp.points_used',*/'mufyp.id as userFYPolMapId', 'mufyp.fypolicy_id_fk as fyPolMapId',
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
            'encoded_summary' => $summary,
            'points_used' => $points,
            'created_by' => $userId,
            'modified_by' => $userId
        ];
        $whereConditionData = ['id' => -1, 'user_id_fk' => -1];
        $mapUserFYPolicyRow = $message = null;
        $status = false;
        //if (count($userPolDataForCatId) > 0) {
            foreach($userPolDataForCatId as $item) {
                if($item->is_base_plan) {
                    continue;
                } else {
                    $whereConditionData['id'] = (int)$item->userFYPolMapId; 
                    $whereConditionData['user_id_fk'] = $userId; 
                    unset($data['created_by']);
                }
            }
            $mapUserFYPolicyRow = MapUserFYPolicy::updateOrCreate($whereConditionData,$data);
            $status = true;
            // update total points
            $user = User::where('id',Auth::user()->id)->get()->toArray();
            $userData = [
                    'points_available'=> $user[0]['points_available']- $points,
                    'points_used'=> $user[0]['points_used'] + $points,
                ];
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
        ->select(/*'mufyp.points_used',*/'mufyp.id')
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
        if (count($userPolDataForCatId)) {
            foreach ($userPolDataForCatId as $fypmapEntry) {
                $ids[] = $fypmapEntry->id;
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
            $user = User::where('id',Auth::user()->id)->get()->toArray();
            $userData = [
                    'points_available'=> $user[0]['points_available']- $pointsCounter,
                    'points_used'=> $user[0]['points_used'] + $pointsCounter,
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
}
