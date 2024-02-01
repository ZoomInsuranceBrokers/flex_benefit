<?php

namespace App\Traits;

use App\Models\Dependant;
use App\Models\MapUserFYPolicy;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

trait EnrollmentTraitMethods {

    public function generateBaseDefaultPolicyMapping ($users, $submissionStatus = false,$confirmUpdate = false) {
        $mapFYpolicyData = DB::table('map_financial_year_policy as mfyp')
            ->select('mfyp.id')
            ->leftJoin('financial_years as fy', 'fy.id', '=', 'mfyp.fy_id_fk')
            ->leftJoin('insurance_policy as ip', 'ip.id', '=', 'mfyp.ins_policy_id_fk')
            ->where('mfyp.is_active', '=', true)
            ->where('fy.is_active', '=', true)
            ->where('ip.is_active', '=', true)
            ->where(function ($query) {
                $query->where('ip.is_base_plan', 1)
                      ->orWhere('ip.is_default_selection', 1);
            })
            ->get()->toArray();

        foreach ($users as $user) {
            foreach ($mapFYpolicyData as $mfypRow) {
                $data[] = [
                    'user_id_fk' => $user['id'],
                    'fypolicy_id_fk' => $mfypRow->id,
                    'selected_dependent' => NULL,
                    'encoded_summary' => NULL,
                    'is_submitted' => $submissionStatus,
                    'points_used' => 0,
                    'created_by' => '0',
                    'modified_by' => '0',
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
            echo '<br>' . __FUNCTION__ . ':INFO:Default Policy entries added for userId:' . $user['id'];
        }
        $confirmUpdate ? MapUserFYPolicy::insert($data) : '';
    }

}

trait dependantTraitMethods {
    public function validatedUpsertDependant ($input, $userData, $depRow) {
        if (array_key_exists('external_id', $input)) { // possibly from api hit
            $rules = [
                'dependent_name' => 'required|between:3,32|',
                'dob'=> 'required|date:d-m-Y',
                //'gender' => 'required|min:0|max:3', @todo
                'nominee_percentage' => 'required|numeric|digits_between:0,100',
                'relationship_type'  => 'required|min:1|max:12',
            ];

            $validator = Validator::make($input, $rules, $messages = [
                'required' => 'The :attribute field is required.',
                'numeric' => 'The :attribute field should be numbers only',
                'boolen' => 'The :attribute field should be Yes or No.',
                'date_format' => 'The :attribute should follow date format of YYYY-MM-DD',
                'min' => 'The :attribute field value is invalid',
                'max' => 'The :attribute field value is invalid',
            ]);
            
            if (!$validator->fails()) {
                //$dependant = new Dependant();
                $dependant['external_id'] = array_key_exists('external_id', $input) ? $input['external_id'] : '';
                $dependant['dependent_name'] = $input['dependent_name'];
                $dependant['user_id_fk'] = $userData['id'];
                // find dependant code from relationship type
                foreach (config('constant.dependant_code') as $code => $rltnArr) {
                    if(in_array($input['relationship_type'], $rltnArr))
                    {
                        $dependant['dependent_code'] = $code;
                        break;
                    }
                }
                $dependant['dob'] = date('Y-m-d', strtotime($input['dob']));
                $dependant['gender'] = $input['gender'];
                $dependant['nominee_percentage'] = $input['nominee_percentage'];
                $dependant['relationship_type'] = $input['relationship_type'];
                $dependant['approval_status'] = $input['approval_status'];      
                $dependant['is_active'] = config('constant.$_YES');
                $dependant['is_deceased'] = $input['is_deceased'];
                $dependant['created_by'] = 0; //admin    
                $dependant['modified_by'] = 0;  // admin

                session('confirmUpdate') ? Dependant::updateOrCreate(['external_id' => $input['external_id']],$dependant) : '';

                //$result['Result'] = "OK";
                $result['Message'] = '<br>----------' . __FUNCTION__ . ':INFO:Dependant upsert(Name:' . implode(' ', [$dependant['dependent_name']]) . 
                ', Relation:' . $depRow['Relationship_Type__c']  . ',ID:' . $input['external_id'] . 
                ') for user(Ext ID:' . $userData['external_id'] . ', User Name:' . implode(' ', [$userData['fname'],$userData['lname']]) . ')';
                
            } else {   
                $error = '';             
                //$result['Result'] = "ERROR";
                foreach (array_values($validator->errors()->messages()) as $item) {
                    $error.= '<li>' . $item[0] . '</li>';
                }
                $result['Message'] = '<br>----------' . __FUNCTION__ . ':ERROR:<div class="fs-12"><ul>' . $error . '</ul></div>';
            }
            return $result['Message'];
        } else {

        }
    }
}
?>