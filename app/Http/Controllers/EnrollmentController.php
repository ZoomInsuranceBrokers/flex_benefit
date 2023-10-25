<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InsurancePolicy;
use App\Models\InsuranceCategory;
use Illuminate\Support\Facades\DB;
use App\Models\InsuranceSubCategory;
use App\Models\MapFYPolicy;
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
}
