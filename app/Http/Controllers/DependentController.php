<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Dependent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;

class DependentController extends Controller {
    // 'sfdc_id',
    // 'dependent_name',
    // 'contact_id_fk',
    // 'dependent_code',
    // 'dob',
    // 'gender',
    // 'nominee_percentage',
    // 'relationship_type',
    // 'approval_status',
    // 'is_active',
    // 'is_deceased',
    // 'created_by',
    // 'modified_by'

    public function list(Request $request) {        
        $jTableResult = array('Result' => 'OK');        // default optimistic approach
        $dependents = Dependent::where('is_active', config('constant.$_YES'))
                                //->where('is_deceased',config('constant.$_NO'))
                                ->where('user_id_fk',Auth::user()->id)
                                ->orderBy('dependent_name', 'ASC')
                                ->get();
        if(!count($dependents)) {       // no/zero dependent count
            $jTableResult['Result'] = 'ERROR';
            $jTableResult['Message'] = 'No dependents found for you! Please secure your loved ones asap.';
        } else {        // dependents found 
            $jTableResult['Result'] = "OK";
            $jTableResult['Records'] = $dependents->toArray();
        }
        return json_encode($jTableResult);
    }

    public function create(Request $request) {
        //Return result to jTable
        $jTableResult = array();
        if($request->isMethod('post')) {
            $input = $request->all();
            $rules = [
                'dependent_name' => 'required|between:3,32|',
                'dob'=> 'required|date:d-m-Y',
                'gender' => 'required',
                'nominee_percentage' => 'required|numeric|digits_between:0,100',
                'relationship_type'  => 'required|numeric',
            ];

            $validator = Validator::make($input, $rules, $messages = [
                'required' => 'The :attribute field is required.',
                'numeric' => 'The :attribute field should be numbers only',
                'boolen' => 'The :attribute field should be Yes or No.',
                'date_format' => 'The :attribute should follow date format of YYYY-MM-DD',
            ]);
            
            if (!$validator->fails()) {
                $dependent = new Dependent();
                $dependent->sfdc_id = 'sfdc_' . time();
                $dependent->dependent_name = $input['dependent_name'];
                $dependent->user_id_fk = Auth::user()->id;
                // find dependent code from relationship type
                foreach (config('constant.dependent_code') as $code => $rltnArr) {
                    //print_r([$code,$rltnArr,$input['relationship_type']]);
                    if(in_array($input['relationship_type'], $rltnArr))
                    {
                        $dependent->dependent_code = $code;
                        break;
                    }
                }
                $dependent->dob = date('Y-m-d', strtotime($input['dob']));
                $dependent->gender = $input['gender'];
                $dependent->nominee_percentage = $input['nominee_percentage'];
                $dependent->relationship_type = $input['relationship_type'];
                $dependent->approval_status = 1;      // hard coded
                $dependent->is_active = config('constant.$_YES');
                $dependent->is_deceased = config('constant.$_NO');
                $dependent->created_by = 1;     // hard coded
                $dependent->modified_by = 1;     // hard coded
                $dependent->save();

                $jTableResult['Result'] = "OK";
                $jTableResult['Record'] = $dependent->toArray();

            } else {   
                $error = '';             
                $jTableResult['Result'] = "ERROR";
                foreach (array_values($validator->errors()->messages()) as $item) {
                    $error.= '<li>' . $item[0] . '</li>';
                }
                //dd($validator->errors()->messages());
                $jTableResult['Message'] = '<div class="fs-12"><ul>' . $error . '</ul></div>';
                
                
            }
        }
        else {
            // throw error
            
            $jTableResult['Result'] = "ERROR";
            $jTableResult['Message'] = 0;
        }
        return json_encode($jTableResult);
    }

    public function update(Request $request) {
        //Return result to jTable
        $jTableResult = array();
        if($request->isMethod('post')) {
            $input = $request->all();
            //dd($input);
            $rules = [
                'dependent_name' => 'required|between:3,32|',
                'dob'=> 'required|date_format:d-m-Y',
                'nominee_percentage' => 'required|numeric|digits_between:0,100',
            ];

            $validator = Validator::make($input, $rules, $messages = [
                'required' => 'The :attribute field is required.',
                'numeric' => 'The :attribute field should be numbers only',
                'boolen' => 'The :attribute field should be Yes or No.',
                'date_format' => 'The :attribute should follow date format of YYYY-MM-DD',
            ]);
            
            if (!$validator->fails()) {               
                $dependent = Dependent::find($input['id']);
                $dependent->id = $input['id'];
                $dependent->dependent_name = $input['dependent_name'];
                // // find dependent code from relationship type
                // foreach (config('constant.dependent_code') as $code => $rltnArr) {
                //     //print_r([$code,$rltnArr,$input['relationship_type']]);
                //     if(in_array($input['relationship_type'], $rltnArr))
                //     {
                //         $dependent->dependent_code = $code;
                //         break;
                //     }
                // }
                $dependent->dob = date('Y-m-d', strtotime($input['dob']));
                //$dependent->gender = $input['gender'];
                $dependent->nominee_percentage = $input['nominee_percentage'];
                //$dependent->relationship_type = $input['relationship_type'];
                //$dependent->approval_status = 1;      // hard coded
                //$dependent->is_deceased = $input['is_deceased'];                
                $dependent->is_active = config('constant.$_YES') ;
                $dependent->modified_by = 1;     // hard coded
                $dependent->save();

                $jTableResult['Result'] = "OK";
                $jTableResult['Record'] = $dependent->toArray();

            } else {                   
                $error = '';             
                $jTableResult['Result'] = "ERROR";
                foreach (array_values($validator->errors()->messages()) as $item) {
                    $error.= '<li>' . $item[0] . '</li>';
                }
                //dd($validator->errors()->messages());
                $jTableResult['Message'] = '<div class="fs-12"><ul>' . $error . '</ul></div>';
                
                
            }
        }
        else {
            // throw error
            
            $jTableResult['Result'] = "ERROR";
            $jTableResult['Message'] = 0;
        }
        return json_encode($jTableResult);
    }

    public function delete(Request $request) {
        //Return result to jTable
        $jTableResult = array();
        if($request->isMethod('post')) {
            try {
                $input = $request->all();

                $dependent = Dependent::find($input['id']);
                $dependent->is_active = config('constant.$_NO');
                $dependent->modified_by = 1;     // hard coded
                $dependent->save();

                $jTableResult['Result'] = "OK";
                $jTableResult['Record'] = $dependent->toArray();
            } catch(Exception $e){
                $jTableResult['Result'] = "ERROR";
                $jTableResult['Message'] = json_encode($e->getMessage());
            }            
        }
        else {
            // throw error
            
            $jTableResult['Result'] = "ERROR";
            $jTableResult['Message'] = 0;
        }
        return json_encode($jTableResult);
    }

    public function listLifeEvents(Request $request) {        
        $jTableResult = array('Result' => 'OK');        // default optimistic approach
        $dependents = Dependent::where('is_active', config('constant.$_YES'))
                                ->where('user_id_fk',Auth::user()->id)
                                ->get();        
        if(!count($dependents)) {       // no/zero dependent count
            $jTableResult['Result'] = 'ERROR';
            //$jTableResult['Message'] = 'No dependents found for you! Please secure your loved ones asap.';
        } else {        // dependents found 
            $jTableResult['Result'] = "OK";
            $jTableResult['Records'] = $dependents->toArray();
        }
        return json_encode($jTableResult);
    }

    public function loadDependentsLE() {
        // all relation possible in case fo Life Event
        $relationLE_Table = config('constant.relationshipLE_type_jTable');
        //dd($relationLE_Table);
        // check logged in user's existing dependents
        $dependents = Dependent::where('is_active', config('constant.$_YES'))
                                ->where('user_id_fk',Auth::user()->id)
                                ->whereIn('relationship_type',[config('constant.$_RLTN_SPOUSE')])
                                ->get()->toArray();
        
        if (count($dependents)){
            foreach ($dependents as $depItem) {                    
                if (array_key_exists($depItem['relationship_type'], $relationLE_Table)) {
                    unset($relationLE_Table[$depItem['relationship_type']]);
                }
            }
        }
        $relationLE_Table = implode(',',$relationLE_Table);

        return view('dependentLE',compact('relationLE_Table'));
    }

    public function createLE(Request $request) {
        //Return result to jTable
        $jTableResult = array();
        if($request->isMethod('post')) {
            $input = $request->all();
            $rules = [
                'dependent_name' => 'required|between:3,32|',
                'dob'=> 'required|date_format:d-m-Y',
                'gender' => 'required',
                'nominee_percentage' => 'required|numeric|digits_between:0,100',
                'relationship_type'  => 'required|numeric',
                //'is_active' => 'required',
                // 'is_deceased' => 'required'
            ];

            $validator = Validator::make($input, $rules, $messages = [
                'required' => 'The :attribute field is required.',
                'numeric' => 'The :attribute field should be numbers only',
                'boolen' => 'The :attribute field should be Yes or No.',
                'date_format' => 'The :attribute should follow date format of YYYY-MM-DD',
            ]);
            
            if (!$validator->fails()) {
                $dependent = new Dependent();
                $dependent->sfdc_id = 'sfdc_' . time();
                $dependent->dependent_name = $input['dependent_name'];
                $dependent->user_id_fk = Auth::user()->id;
                // find dependent code from relationship type
                foreach (config('constant.dependent_code') as $code => $rltnArr) {
                    if(in_array($input['relationship_type'], $rltnArr))
                    {
                        $dependent->dependent_code = $code;
                        break;
                    }
                }
                $dependent->dob = $input['dob'];
                $dependent->gender = $input['gender'];
                $dependent->nominee_percentage = $input['nominee_percentage'];
                $dependent->relationship_type = $input['relationship_type'];
                $dependent->approval_status = config('constant.$_APPR_STATUS_INPROGRESS');      // hard coded
                $dependent->is_active = config('constant.$_YES');
                $dependent->is_deceased = config('constant.$_NO');
                $dependent->created_by = 1;     // hard coded
                $dependent->modified_by = 1;     // hard coded
                $dependent->save();

                $jTableResult['Result'] = "OK";
                $jTableResult['Record'] = $dependent->toArray();
            } else {                
                $error = '';             
                $jTableResult['Result'] = "ERROR";
                foreach (array_values($validator->errors()->messages()) as $item) {
                    $error.= '<li>' . $item[0] . '</li>';
                }
                //dd($validator->errors()->messages());
                $jTableResult['Message'] = '<div class="fs-12"><ul>' . $error . '</ul></div>';
            }
        }
        else {
            // throw error
            
            $jTableResult['Result'] = "ERROR";
            $jTableResult['Message'] = 0;
        }
        return json_encode($jTableResult);
    }

    public function getRelationshipTypes () {
        $relation_Table = config('constant.relationship_type');
        unset($relation_Table[config('constant.$_RLTN_SELF')]);
        //$relation_Table = config('constant.relationship_type_jTable');
        //dd($relation_Table);
        // check logged in user's existing dependents
        $dependents = Dependent::where('is_active', config('constant.$_YES'))
                                ->where('user_id_fk',Auth::user()->id)
                                //->whereIn('relationship_type',[config('constant.$_RLTN_SPOUSE')])
                                ->get()->toArray();
        
        if (count($dependents)){
            foreach ($dependents as $depItem) {                    
                if (array_key_exists($depItem['relationship_type'], $relation_Table) && 
                (!in_array($depItem['relationship_type'], [config('constant.$_RLTN_SON'), config('constant.$_RLTN_DAUGHTER')]))) {
                    unset($relation_Table[$depItem['relationship_type']]);
                }
            }
        }
        foreach (([-1 => '--Select--'] + $relation_Table) as $k => $v) {
            $generatedOptions[$k] = "{Value:'" . $k . "',DisplayText:'" . $v . "'}";
        }
        $jTableResult['Result'] = "OK";
        $jTableResult['Options'] = '['. json_encode($generatedOptions) . ']';
        //return view('dependentLE',compact('relationLE_Table'));
        //dd($jTableResult['Options'] );
        return json_encode($jTableResult);

    }

    public function getGenderByRelation(Request $request){
        //return 
        $maleGenderMap = [
            config('constant.$_RLTN_FATHER'),
            config('constant.$_RLTN_BRTOHER'),
            config('constant.$_RLTN_FATHERINLAW'),
            config('constant.$_RLTN_SON'),
            config('constant.$_RLTN_FATHER'),
            config('constant.$_RLTN_FATHER'),
            config('constant.$_RLTN_FATHER'),
            config('constant.$_RLTN_FATHER'),
        ];
    }
}
