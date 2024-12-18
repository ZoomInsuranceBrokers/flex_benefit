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

/*
trait accountTraitMethods {

    public function upsertAccount($accountData) {
        // update or create account
        $rules = [
            'external_id' => 'required',
            'name' => 'required',
            'enrollment_start_date'=> 'required|date:Y-m-d',
            'enrollment_end_date' => 'required|date:Y-m-d',
        ];

        $validator = Validator::make($input, $rules, $messages = [
            'required' => 'The :attribute field is required',
            'date_format' => 'The :attribute should follow date format of YYYY-MM-DD',
        ]);

        if (!$validator->fails()) {
            $account = [];
            $account['external_id'] = 0;
            $account['name'] = 0;
            $account['address'] = 0;
            $account['country_id_fk'] = CountryCurrency::where(DB::raw('UPPER(name)'),strtoupper($jsonRow['Details']['MailingCountry']))
                ->select('id')->first()->toArray()['id'];
            $account['is_active'] = false;
            $account['enrollment_start_date'] = 0;
            $account['enrollment_end_date'] = 0;
            $account['created_by'] = 0;     // admin
            $account['modified_by'] = 0;    // admin

            echo __FUNCTION__ . ':INFO:Account [Name:' . $account['name'] . '] with details[Ext ID:' . 
            $account['external_id'] . ',Enrollment Start Date:' . $account['enrollment_start_date'] . 
            ',Enrollment End Date:' . $account['enrollment_end_date'] . '] to be created/updated ';

            session('confirmUpdate') ? Account::updateOrCreate(['external_id' => $account['external_id']],$account) : '';

        } else {   
            $error = '';             
            //$result['Result'] = "ERROR";
            foreach (array_values($validator->errors()->messages()) as $item) {
                $error.= '<li>' . $item[0] . '</li>';
            }
            $result['Message'] = '<br>----------' . __FUNCTION__ . ':ERROR:<div class="fs-12"><br><b>Account:</b><ul>' . $error . '</ul></div>';
        }
        return $result['Message'];
    }

}

trait financialYearTraitMethods {
    public function upsertFY($accountData) {
        // update or create FY
            
            $rules = [
                'external_id' => 'required',
                'name' => 'required',
                'enrollment_start_date'=> 'required|date:Y-m-d',
                'enrollment_end_date' => 'required|date:Y-m-d',
            ];

            $validator = Validator::make($input, $rules, $messages = [
                'required' => 'The :attribute field is required',
                'date_format' => 'The :attribute should follow date format of YYYY-MM-DD',
            ]);

            if (!$validator->fails()) {
                $fy = [];
                $fy['external_id'] = 0;    
                $fy['name'] = 0;    
                $fy['start_date'] = 0;    
                $fy['end_date'] = 0;    
                $fy['last_enrollment_date'] = 0;    
                $fy['future_fy_year_fk'] = null;    
                $fy['prev_fy_year_fk'] = null;    
                $fy['is_active'] = false;
                $fy['created_by'] = 0;  // admin
                $fy['modified_by'] = 0;    // admin

                echo __FUNCTION__ . ':INFO:Financial Year [Name:' . $fy['name'] . '] with details[Ext ID:' . 
                $fy['external_id'] . ',FY Start Date:' . $fy['enrollment_start_date'] . 
                ',FY End Date:' . $fy['enrollment_end_date'] . ', FY Last Enrollment Date:' . $fy['last_enrollment_date'] . '] to be created/updated ';

                session('confirmUpdate') ? FinancialYear::updateOrCreate(['external_id' => $fy['external_id']],$fy) : '';
            } else {   
                $error = '';             
                //$result['Result'] = "ERROR";
                foreach (array_values($validator->errors()->messages()) as $item) {
                    $error.= '<li>' . $item[0] . '</li>';
                }
                echo '<br>----------' . __FUNCTION__ . ':ERROR:<div class="fs-12"><br><b>FY:</b><ul>' . $error . '</ul></div>';
            }
    }
}

trait gradeTraitMethods {
    public function upsertGrade($gradeData) {
        $rules = [
            'grade_name' => 'required',
        ];
        foreach ($gradeData as $gradeRow) {
            // update or create Grade
            $validator = Validator::make($input, $rules, $messages = [
                'required' => 'The :attribute field is required',
            ]);

            if (!$validator->fails()) {
                $grade = [];            
                $grade['external_id'] = 0;    
                $grade['grade_name'] = 0;    
                $grade['is_active'] = false;    
                $grade['created_by'] = 0;    // admin
                $grade['modified_by'] = 0;    // admin

                echo __FUNCTION__ . ':INFO:Grade [Name:' . $grade['grade_name'] . '] with details[Ext ID:' . 
                $grade['external_id'] . ' to be created/updated ';
            } else {   
                $error = '';             
                foreach (array_values($validator->errors()->messages()) as $item) {
                    $error.= '<li>' . $item[0] . '</li>';
                }
                echo '<br>----------' . __FUNCTION__ . ':ERROR:<div class="fs-12"><br><b>Grade:</b><ul>' . $error . '</ul></div>';
            }
        }        
    }
}

trait insuranceCategoryMethods {
    public function upsertInsuranceCategory($insCategoryData) {
        $rules = [
            'name' => 'required',
            'sequence' => 'required|numeric',
        ];
        foreach ($insCategoryData as $insCategoryRow) {
            // update or create insCategory
            $validator = Validator::make($input, $rules, $messages = [
                'required' => 'The :attribute field is required',
                'numeric' => 'The :attribute field should be numbers only'
            ]);

            if (!$validator->fails()) {
                $insCategory = [];            
                $insCategory['external_id'] = 0;    
                $insCategory['name'] = 0;
                $insCategory['tagline'] = 0;
                $insCategory['sequence'] = 0;
                $insCategory['is_active'] = true;    
                $insCategory['created_by'] = 0;    // admin
                $insCategory['modified_by'] = 0;    // admin

                echo __FUNCTION__ . ':INFO:Insurance Category [Name:' . $insCategory['name'] . '] with details[Ext ID:' . 
                $insCategory['external_id'] . ' to be created/updated ';
            } else {   
                $error = '';             
                foreach (array_values($validator->errors()->messages()) as $item) {
                    $error.= '<li>' . $item[0] . '</li>';
                }
                echo '<br>----------' . __FUNCTION__ . ':ERROR:<div class="fs-12"><br><b>Insurance Category:</b><ul>' . $error . '</ul></div>';
            }
        }        
    }
}

trait insuranceSubCategoryMethods {
    public function upsertInsuranceSubCategory($insSubCategoryData) {
        $rules = [
            'name' => 'required',
        ];
        foreach ($insSubCategoryData as $insSubCategoryDataRow) {
            // update or create insSubCategoryData
            $validator = Validator::make($input, $rules, $messages = [
                'required' => 'The :attribute field is required',
            ]);

            if (!$validator->fails()) {
                $insSubCategoryData = [];            
                $insSubCategoryData['ins_category_id_fk'] = 0;    
                $insSubCategoryData['name'] = 0;
                $insSubCategoryData['fullname'] = 0;
                $insSubCategoryData['description'] = 0;
                $insSubCategoryData['details'] = 0;
                $insSubCategoryData['is_active'] = true;    
                $insSubCategoryData['created_by'] = 0;    // admin
                $insSubCategoryData['modified_by'] = 0;    // admin

                echo __FUNCTION__ . ':INFO:Insurance Sub Category [Name:' . $insSubCategoryData['name'] . '] to be created/updated ';
            } else {   
                $error = '';             
                foreach (array_values($validator->errors()->messages()) as $item) {
                    $error.= '<li>' . $item[0] . '</li>';
                }
                echo '<br>----------' . __FUNCTION__ . ':ERROR:<div class="fs-12"><br><b>Insurance Sub Category:</b><ul>' . $error . '</ul></div>';
            }
        }        
    }
}

trait insuancePolicyMethods {
    public function upsertinsuancePolicy($insPolicyData) {
        $rules = [
            'name' => 'required',
            'sum_insured' => 'required|numeric',
        ];
        foreach ($insPolicyData as $insPolicyDataRow) {
            // update or create insPolicyData
            $validator = Validator::make($input, $rules, $messages = [
                'required' => 'The :attribute field is required',
                'numeric' => 'The :attribute field should be numbers only'
            ]);

            if (!$validator->fails()) {
                $insPolicyData = [];            
                $insPolicyData['external_id'] = 0;    
                $insPolicyData['name'] = 0;
                $insPolicyData['sum_insured'] = 0;
                $insPolicyData['ins_subcategory_id_fk'] = 0;
                $insPolicyData['description'] = 0;
                $insPolicyData['price_tag'] = 0;
                $insPolicyData['points'] = 0;
                $insPolicyData['dependent_structure'] = 0;
                $insPolicyData['is_parent_sublimit'] = 0;
                $insPolicyData['parent_sublimit_amount'] = 0;
                //$insPolicyData['replacement_of_policy_id'] = null;
                //$insPolicyData['replacement_of_policy_sfdc_id'] = null;
                $insPolicyData['currency_id_fk'] = 0;
                $insPolicyData['is_active'] = true;
                $insPolicyData['si_factor'] = 0;
                $insPolicyData['is_base_plan'] = 0;
                $insPolicyData['is_default_selection'] = 0;
                $insPolicyData['is_point_value_based'] = null;
                $insPolicyData['base_plan_id_sfdc'] = null;
                $insPolicyData['base_plan_text'] = null;
                $insPolicyData['is_multi_selectable'] = 0;
                $insPolicyData['show_value_column'] = 0;
                $insPolicyData['created_by'] = 0;    // admin
                $insPolicyData['modified_by'] = 0;    // admin

                echo __FUNCTION__ . ':INFO:Insurance Policy [Name:' . $insPolicyData['name'] . '] with details[Sum Insured:' . 
                $insPolicyData['sum_insured'] . ',Is Base Plan:'.$insPolicyData['is_base_plan'] .',Is Default Selection:' . 
                $insPolicyData['is_default_selection'] . ',Is Multi Selectable:' . $insPolicyData['is_multi_selectable'] . 
                ',Is Value based:' . $insPolicyData['show_value_column'] . '] to be created/updated ';
            } else {   
                $error = '';             
                foreach (array_values($validator->errors()->messages()) as $item) {
                    $error.= '<li>' . $item[0] . '</li>';
                }
                echo '<br>----------' . __FUNCTION__ . ':ERROR:<div class="fs-12"><br><b>Insurance Sub Category:</b><ul>' . $error . '</ul></div>';
            }
        }        
    }
}*/
?>