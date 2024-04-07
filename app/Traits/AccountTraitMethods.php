<?php
namespace App\Traits;

use App\Models\Grade;
use App\Models\Account;
use App\Models\FinancialYear;
use App\Models\CountryCurrency;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

trait accountTraitMethods {

    public function upsertAccount($jsonData) {
        // update or create account
        $rules = [
            'external_id' => 'required',
            'name' => 'required',
            'enrollment_start_date'=> 'required|date:Y-m-d',
            'enrollment_end_date' => 'required|date:Y-m-d',
        ];
        foreach ($jsonData as $accRow) {
            $account = $this->_extractAccountData($accRow['Response']);
                
            

            $validator = Validator::make($account, $rules, $messages = [
                'required' => 'The :attribute field is required',
                'date_format' => 'The :attribute should follow date format of YYYY-MM-DD',
            ]);
        

            if (!$validator->fails()) {
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
                echo '<br>----------' . __FUNCTION__ . ':ERROR:<div class="fs-12"><br><b>Account:</b><ul>' . $error . '</ul></div>';
            }
        }
        //return $result['Message'];
    }
    
    private function _extractAccountData($jsonData){
        return [
            'external_id' => $jsonData['Client']['Id'],
            'name' => $jsonData['Client']['Name'],
            'address' => $jsonData['Client']['FullAddress__c'],
            'country_id_fk' => CountryCurrency::where(DB::raw('UPPER(name)'),strtoupper($jsonData['Client']['ShippingCountry']))
                ->select('id')->first()->toArray()['id'],
            'is_active' => true,
            'enrollment_start_date' => date('Y-m-d', strtotime('13-09-2022')),
            'enrollment_end_date' => date('Y-m-d', strtotime('13-09-2022')),
            'created_by' => 0,
            'modified_by' => 0,
        ];
    }

}

trait financialYearTraitMethods {
    public function upsertFY($jsonData) {
        // update or create FY
        $rules = [
            'external_id' => 'required',
            'name' => 'required',
            'start_date'=> 'required|date:Y-m-d',
            'end_date' => 'required|date:Y-m-d',
            'last_enrollment_date' => 'required|date:Y-m-d',
        ];
        foreach ($jsonData as $accRow) {
            $fy = $this->_extractFYData($accRow['Response']);
            $validator = Validator::make($fy, $rules, $messages = [
                'required' => 'The :attribute field is required',
                'date_format' => 'The :attribute should follow date format of YYYY-MM-DD',
            ]);

            if (!$validator->fails()) {
                echo __FUNCTION__ . ':INFO:Financial Year [Name:' . $fy['name'] . '] with details[Ext ID:' . 
                $fy['external_id'] . ',FY Start Date:' . $fy['start_date'] . 
                ',FY End Date:' . $fy['end_date'] . ', FY Last Enrollment Date:' . $fy['last_enrollment_date'] . '] to be created/updated ';

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

    private function _extractFYData($jsonData){
        return [
            'external_id' => $jsonData['Client']['FY_InsurancePolicy']['FinancialYear']['Id'],
            'name' => $jsonData['Client']['FY_InsurancePolicy']['FinancialYear']['Name'],
            'start_date' => date('Y-m-d', $jsonData['Client']['FY_InsurancePolicy']['FinancialYear']['FY_Start_Date__c']),
            'end_date' => date('Y-m-d', $jsonData['Client']['FY_InsurancePolicy']['FinancialYear']['FY_End_Date__c']),
            'last_enrollment_date' => date('Y-m-d', $jsonData['Client']['FY_InsurancePolicy']['FinancialYear']['Last_Enrollment_Date__c']),
            // @todo :  get external ID/php ID of prev and/or future FY
            //'future_fy_year_fk' => $jsonData['Client']['FY_InsurancePolicy']['FinancialYear']['Past_FY__c'],
            //'prev_fy_year_fk' => $jsonData['Client']['FY_InsurancePolicy']['FinancialYear']['Future_FY__c'],
            'is_active' => $jsonData['Client']['FY_InsurancePolicy']['FinancialYear']['Current_FY__c'],
            'created_by' => 0,
            'modified_by' => 0,
        ];
    }
}

trait gradeTraitMethods {
    public function upsertGrade($jsonData) {
        // update or create Grade
        $rules = [
            '*.grade_name' => 'required',   // nested validation
        ];
        foreach ($jsonData as $accRow) {
            $grade = [];
            foreach($accRow['SumInsuredMapping'] as $gradeRow){
                $grade[] = $this->_extractGradeData($accRow['SumInsuredMapping']);
            }
                
            // update or create Grade
            $validator = Validator::make($grade, $rules, $messages = [
                'required' => 'The :attribute field is required',
            ]);

            if (!$validator->fails()) {
                foreach ($grade as $gradeRow) { // multiple grade add/update through loop
                    echo __FUNCTION__ . ':INFO:Grade [Name:' . $gradeRow['grade_name'] . '] with details[Ext ID:' . 
                    $gradeRow['external_id'] . ' to be created/updated ';
                
                    session('confirmUpdate') ? Grade::updateOrCreate(['external_id' => $gradeRow['external_id']],$gradeRow) : '';
                }
            } else {   
                $error = '';             
                foreach (array_values($validator->errors()->messages()) as $item) {
                    $error.= '<li>' . $item[0] . '</li>';
                }
                echo '<br>----------' . __FUNCTION__ . ':ERROR:<div class="fs-12"><br><b>Grade:</b><ul>' . $error . '</ul></div>';
            }
        }        
    }

    private function _extractGradeData($jsonData) {
        return [
            'external_id' => $jsonData['Id'],
            'grade_name' => $jsonData['EmployeeGrade__c'],
            // @todo External system to sent is_active 
            'is_active' => true,
            'created_by' => 0,
            'modified_by' => 0,
        ];
    }
}

trait insuranceCategoryMethods {
    public function upsertInsuranceCategory($jsonData) {
        $rules = [
            '*.name' => 'required',
            //'*.sequence' => 'required|numeric', @todo
        ];
        foreach ($jsonData as $accRow) {
            $insuranceCategories = [];
            foreach ($accRow['Response']['Client']['FY_InsurancePolicy']['PolicyCluster'] as $policyClusterRow) {
                $insuranceCategories[] = $this->_extractInsCatData($policyClusterRow['InsurancePolicy']);
            }
        
            // update or create insCategory
            $validator = Validator::make($insuranceCategories, $rules, $messages = [
                'required' => 'The :attribute field is required',
                'numeric' => 'The :attribute field should be numbers only'
            ]);

            if (!$validator->fails()) {
                foreach ($insuranceCategories as $insCategory) {
                    echo __FUNCTION__ . ':INFO:Insurance Category [Name:' . $insCategory['name'] . '] with details[Ext ID:' . 
                    $insCategory['external_id'] . ' to be created/updated ';

                    session('confirmUpdate') ? Grade::updateOrCreate(['external_id' => $insCategory['external_id']],$insCategory) : '';
                }
            } else {   
                $error = '';             
                foreach (array_values($validator->errors()->messages()) as $item) {
                    $error.= '<li>' . $item[0] . '</li>';
                }
                echo '<br>----------' . __FUNCTION__ . ':ERROR:<div class="fs-12"><br><b>Insurance Category:</b><ul>' . $error . '</ul></div>';
            }
        }
               
    }

    private function _extractInsCatData($jsonData) {
        return [
            'external_id' => $jsonData['RecordTypeId'],
            'name' => $jsonData['Record_Type__c'],
            'tagline' => $jsonData['Description__c'],
            // @todo External system to sent is_active, sequence 
            //'sequence' => '',
            'is_active' => true,
            'created_by' => 0,
            'modified_by' => 0,
        ];
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