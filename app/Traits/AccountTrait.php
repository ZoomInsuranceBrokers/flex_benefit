<?php

namespace App\AccountTraits;
use App\Models\Account;
use App\Models\CountryCurrency;
use App\Models\FinancialYear;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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
}
?>