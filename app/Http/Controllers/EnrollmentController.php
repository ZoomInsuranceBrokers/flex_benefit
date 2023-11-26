<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InsurancePolicy;
use App\Models\InsuranceCategory;
use Illuminate\Support\Facades\DB;
use App\Models\InsuranceSubCategory;
use App\Models\MapFYPolicy;
use App\Models\MapUserFYPolicy;
use Illuminate\Support\Facades\Auth;
use Facade\FlareClient\Http\Response;
use Illuminate\Http\Client\Response as ClientResponse;
use Dompdf\Dompdf;

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

        return view('enrollment')->with('data', 
        [   'sub_categories_data' => $data->toArray(), 
            'category' => $category->toArray()
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
            $message = 'Data ' . ($whereConditionData['id'] != -1 ? 'updated' : 'saved') . ' successfully. You need to do final submission(only one submission allowed per user per financial year) of data across policies/categories from "Summary" section post review';
        // }
        // if(!count($userPolDataForCatId)) {
        //     $status = false;
        //     $message = 'No Base Policy Exist';
        // }

        return response()->json([
            'status' => $status,
            //'responseRow' => $mapUserFYPolicyRow,
            'message' => $message
        ]);
    }

    public function loadSummary(){
        // category data
        // $category = InsuranceCategory::where('is_active', true)->orderBy('sequence')->get()->toArray();

        // // sub-category data
        // $subCategory = DB::table('insurance_category as ic')
        //             ->leftJoin('insurance_subcategory as isc' ,'isc.ins_category_id_fk', '=', 'ic.id')
        //             ->where('ic.is_active', '=', true)
        //             ->where('isc.is_active', '=', true)
        //             ->select('ic.id as ic_id','ic.name as category', 'sequence', 'tagline','isc.*')
        //             ->get()->toArray();
                    
        // $userPolData = DB::table('map_user_fypolicy as mufyp')
        // ->leftJoin('map_financial_year_policy as mfyp' ,'mufyp.fypolicy_id_fk', '=', 'mfyp.id')
        // ->leftJoin('financial_years as fy' ,'fy.id', '=', 'mfyp.fy_id_fk')
        // ->leftJoin('insurance_policy as ip' ,'ip.id', '=', 'mfyp.ins_policy_id_fk')
        // //->where('mufyp.user_id_fk', '=', Auth::user()->id)
        // ->where('mufyp.user_id_fk', '=', 1)
        // ->where('mufyp.is_active', '=', true)
        // ->where('mfyp.is_active', '=', true)
        // ->where('fy.is_active', '=', true)
        // ->where('ip.is_active', '=', true)
        // ->select('mufyp.points_used','fy.name as fy_name','fy.start_date','fy.end_date', 'ip.id as ip_id')
        // ->get()->toArray();

        $mapUserFYPolicyData = MapUserFYPolicy::where('user_id_fk', '=', Auth::user()->id)->with(['fyPolicy'])
                ->get()->toArray();
        
        //dd($mapUserFYPolicyData);

        $html = view('summary')
            ->with('mapUserFYPolicyData', $mapUserFYPolicyData)->render();
        return $html;
        
        // return response()->json([
        //     'status' => true,
        //     'html' => $html,
        // ]);
    }

    public function downloadSummary (){
        $mapUserFYPolicyData = MapUserFYPolicy::where('user_id_fk', '=', Auth::user()->id)->with(['fyPolicy'])
                ->get()->toArray();
        
        //dd($mapUserFYPolicyData);

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
