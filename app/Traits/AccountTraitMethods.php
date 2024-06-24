<?php
namespace App\Traits;

use App\Models\Grade;
use App\Models\Account;
use App\Models\FinancialYear;
use App\Models\CountryCurrency;
use App\Models\InsuranceCategory;
use App\Models\InsurancePolicy;
use App\Models\InsuranceSubCategory;
use App\Models\MapGradeCategory;
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
                echo '<br>----------' . __FUNCTION__ . 
                    ':ERROR<div class="fs-12"><br><b>Account:</b><ul>' . $error . '</ul></div>';
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
            'enrollment_end_date' => date('Y-m-d', strtotime('13-10-2022')),
            'created_by' => 0,
            'modified_by' => 0,
        ];
    }

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
                echo '<br><br>' . __FUNCTION__ . ':INFO:Financial Year [Name:' . $fy['name'] . '] with details[Ext ID:' . 
                $fy['external_id'] . ',FY Start Date:' . $fy['start_date'] . 
                ',FY End Date:' . $fy['end_date'] . ', FY Last Enrollment Date:' . $fy['last_enrollment_date'] . 
                '] to be created/updated ';

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
            'start_date' => date('Y-m-d', strtotime($jsonData['Client']['FY_InsurancePolicy']['FinancialYear']['FY_Start_Date__c'])),
            'end_date' => date('Y-m-d', strtotime($jsonData['Client']['FY_InsurancePolicy']['FinancialYear']['FY_End_Date__c'])),
            //@todo 'last_enrollment_date' => date('Y-m-d', strtotime($jsonData['Client']['FY_InsurancePolicy']['FinancialYear']['Last_Enrollment_Date__c'])),
            'last_enrollment_date' => date('Y-m-d', strtotime('2024-05-12')),
            // @todo :  get external ID/php ID of prev and/or future FY
            //'future_fy_year_fk' => $jsonData['Client']['FY_InsurancePolicy']['FinancialYear']['Past_FY__c'],
            //'prev_fy_year_fk' => $jsonData['Client']['FY_InsurancePolicy']['FinancialYear']['Future_FY__c'],
            'is_active' => $jsonData['Client']['FY_InsurancePolicy']['FinancialYear']['Current_FY__c'],
            'created_by' => 0,
            'modified_by' => 0,
        ];
    }

    public function upsertGrade($jsonData) {
        // update or create Grade
        $rules = [
            '*.grade_name' => 'required',   // nested validation
        ];
        foreach ($jsonData as $accRow) {
            $grade = [];
            foreach($accRow['SumInsuredMapping'] as $gradeRow){
                $grade[] = $this->_extractGradeData($gradeRow);
            }
                
            // update or create Grade
            $validator = Validator::make($grade, $rules, $messages = [
                'required' => 'The :attribute field is required',
            ]);

            if (!$validator->fails()) {
                foreach ($grade as $gradeRow) { // multiple grade add/update through loop
                    echo '<br><br>' . __FUNCTION__ . ':INFO:Grade [Name:' . $gradeRow['grade_name'] . '] with details[Ext ID:' . 
                    $gradeRow['external_id'] . ' to be created/updated ';
                
                    session('confirmUpdate') ? Grade::updateOrCreate(['external_id' => $gradeRow['external_id']],$gradeRow) : '';
                }
            } else {   
                $error = '';             
                foreach (array_values($validator->errors()->messages()) as $item) {
                    $error.= '<li>' . $item[0] . '</li>';
                }
                echo '<br>----------' . __FUNCTION__ . 
                    ':ERROR:<div class="fs-12"><br><b>Grade:</b><ul>' . $error . '</ul></div>';
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

                    session('confirmUpdate') ? InsuranceCategory::updateOrCreate(['external_id' => $insCategory['external_id']],$insCategory) : '';
                }
            } else {   
                $error = '';             
                foreach (array_values($validator->errors()->messages()) as $item) {
                    $error.= '<li>' . $item[0] . '</li>';
                }
                echo '<br>----------' . __FUNCTION__ . 
                    ':ERROR:<div class="fs-12"><br><b>Insurance Category:</b><ul>' . $error . '</ul></div>';
            }

            //***** add mapping grade and category *****// 
            $gradeCatMap = $this->_prepareGradeCatMapping($accRow['SumInsuredMapping']);
            
        }
               
    }

    private function _extractInsCatData($jsonData) {
        return [
            'external_id' => $jsonData['RecordTypeId'],
            'name' => $jsonData['Record_Type__c'],
            //'tagline' => $jsonData['Description__c'],
            // @todo External system to sent is_active, sequence 
            //'sequence' => '',
            'is_active' => true,
            'created_by' => 0,
            'modified_by' => 0,
        ];
    }

    private function _prepareGradeCatMapping($jsonData) {
        // fetch all insurance categories
        $insCategories = InsuranceCategory::select(['id', 'external_id'])
            ->where('is_active', true)
            ->get()->toArray();
        $insCategoriesArr = [];
        if (count($insCategories)) {
            foreach ($insCategories as $insCatRow) {
                $insCategoriesArr[$insCatRow['external_id']] = $insCatRow['id'];
            }
        } else {
            die(__FUNCTION__ . ':ERROR:Insurance Categories not found.');
        }

        // fetch all grades
        $grades = Grade::select(['id', 'external_id'])
            ->where('is_active', true)
            ->get()->toArray();
        $gradeArr = [];
        if (count($grades)) {
            foreach ($grades as $grdRow) {
                if (strlen(trim($grdRow['external_id']))) {
                    $gradeArr[$grdRow['external_id']] = $grdRow['id'];
                } else {
                    echo __FUNCTION__ . ':ERROR:Empty external ID found in Grade at line ' . __LINE__;
                }
                
            }
        } else {
            die(__FUNCTION__ . ':ERROR:Grades not found.');
        }

        // map grade and categories
        $mapGradeCat = [];
        foreach($jsonData as $jsonGrade) {
            if (array_key_exists($jsonGrade['Id'], $gradeArr) 
                && array_key_exists($jsonGrade['Type1_Category_Id__c'], $insCategoriesArr)) {
                $mapGradeCat[] = [
                    'grade_id_fk' => $gradeArr[$jsonGrade['Id']],
                    'category_id_fk' => $insCategoriesArr[$jsonGrade['Type1_Category_Id__c']],
                    'amount' => $jsonGrade['Type1_Sum_Insured__c']
                ];
            }
        }
        
        echo '<br>'. __FUNCTION__ . ':INFO: Grade-Category-Mapping:<pre>';
        print_r($mapGradeCat);
        echo '</pre>';
        session('confirmUpdate') ? MapGradeCategory::updateOrCreate([], $mapGradeCat) : '';

        return $mapGradeCat;die;
    }
}

trait insuranceSubCategoryMethods {
    public function upsertInsuranceSubCategory($jsonData) {
        $insCategories = InsuranceCategory::select(['id', 'external_id'])
            ->where('is_active', true)
            ->get()->toArray();
        $rules = [
            '*.name' => 'required',
            '*.ins_category_id_fk' => 'required|numeric|min:1',
        ];
        foreach ($jsonData as $accRow) {
            $insuranceSubCategories = [];
            foreach ($accRow['Response']['Client']['FY_InsurancePolicy']['PolicyCluster'] as $policyClusterRow) {
                // only unique subcategories to be created by sub-category NAME match as externl system doesn't have external ID
                if (!array_key_exists($policyClusterRow['InsurancePolicy']['Policy_Type__c'], $insuranceSubCategories)) {
                    $insuranceSubCategories[$policyClusterRow['InsurancePolicy']['Policy_Type__c']] = 
                        $this->_extractInsSubCatData($policyClusterRow['InsurancePolicy'], 
                        array_search($policyClusterRow['InsurancePolicy']['RecordTypeId'], $insCategories));
                }
            }
        
            // update or create insSubCategory
            $validator = Validator::make($insuranceSubCategories, $rules, $messages = [
                'required' => 'The :attribute field is required',
                'numeric' => 'The :attribute field should be numbers only',
                'min' => 'The :attribute field should be greater than ID 0. Shows no matching category found in DB'
            ]);

            if (!$validator->fails()) {
                foreach ($insuranceSubCategories as $insSubCategory) {
                    echo __FUNCTION__ . ':INFO:Insurance Sub Category [Name:' . $insSubCategory['name'] . '] with category[ID:' . 
                    $insSubCategory['ins_category_id_fk'] . ' to be created/updated ';

                    session('confirmUpdate') ? InsuranceSubCategory::updateOrCreate(
                        [
                            'ins_category_id_fk' => $insSubCategory['ins_category_id_fk'],
                            'ins_category_id_fk' => $insSubCategory['name']
                        ],
                        $insSubCategory) : '';
                }
            } else {   
                $error = '';             
                foreach (array_values($validator->errors()->messages()) as $item) {
                    $error.= '<li>' . $item[0] . '</li>';
                }
                echo '<br>----------' . __FUNCTION__ . 
                    ':ERROR:<div class="fs-12"><br><b>Insurance Sub Category:</b><ul>' . $error . '</ul></div>';
            }
        }       
    }

    private function _extractInsSubCatData($jsonData, $categoryId) {
        return [
            'ins_category_id_fk' => $categoryId,
            'name' => $jsonData['Policy_Type__c'],
            //@todo 'fullname' => $jsonData['Description__c'],
            //@todo 'description' => '',
            //@todo 'details' => $jsonData['Description__c'],
            'is_active' => true,
            'created_by' => 0,
            'modified_by' => 0,
        ];
    }
}

trait insurancePolicyMethods {
    public function upserInsurancePolicy($jsonData) {
        $insSubCat = InsuranceSubCategory::select(['id','name'])->get()->toArray();
        $rules = [
            '*.name' => 'required',
            '*.sum_insured' => 'required|numeric',
        ];
        foreach ($jsonData as $accRow) {
            $insurancePolicies = [];
            foreach ($accRow['Response']['Client']['FY_InsurancePolicy']['PolicyCluster'] as $policyClusterRow) {
                $insurancePolicies[] = $this->_extractPolicyData($policyClusterRow,
                array_search($policyClusterRow['InsurancePolicy']['Policy_Type__c'], $insSubCat));
            }
        
            // update or create insCategory
            $validator = Validator::make($insurancePolicies, $rules, $messages = [
                'required' => 'The :attribute field is required',
                'numeric' => 'The :attribute field should be numbers only'
            ]);

            if (!$validator->fails()) {
                foreach ($insurancePolicies as $insPolicyData) {
                    echo __FUNCTION__ . ':INFO:Insurance Policy [Name:' . $insPolicyData['name'] . '] with details[Sum Insured:' . 
                    $insPolicyData['sum_insured'] . ',Is Base Plan:'.$insPolicyData['is_base_plan'] .',Is Default Selection:' . 
                    $insPolicyData['is_default_selection'] . ',Is Multi Selectable:' . $insPolicyData['is_multi_selectable'] . 
                    ',Is Value based:' . $insPolicyData['show_value_column'] . '] to be created/updated ';

                    session('confirmUpdate') ? InsurancePolicy::updateOrCreate(['external_id' => $insPolicyData['external_id']],$insPolicyData) : '';
                }
            } else {   
                $error = '';             
                foreach (array_values($validator->errors()->messages()) as $item) {
                    $error.= '<li>' . $item[0] . '</li>';
                }
                echo '<br>----------' . __FUNCTION__ . 
                    ':ERROR:<div class="fs-12"><br><b>Insurance Policy:</b><ul>' . $error . '</ul></div>';
            }
        }       
    }

    private function _extractPolicyData($jsonData, $subCatId) {
        return [                   
            'external_id' => $jsonData['Id'],    
            'name'=> $jsonData['Insurance_Policy_Name__c'],
            'sum_insured' => $jsonData['Plan_Sum_Assured__c'],
            'ins_subcategory_id_fk' => $subCatId,
            'description' => 'Please ask admin to enter decription in PHP DB',
            'price_tag' => $jsonData['Price_Tag__c'],
            'points' => $jsonData['Flex_Points__c'],
            'dependent_structure' => $jsonData['Family_Structure__c'],
            'is_parent_sublimit' => $jsonData['InsurancePolicy']['Parents_Sub_Limit_Applicable__c'],
            'parent_sublimit_amount' => $jsonData['InsurancePolicy']['Parents_Sub_Limit_Amount__c'],
            // @todo 'replacement_of_policy_id' => null,
            'replacement_of_policy_sfdc_id' => $jsonData['Replacement_Of__c'],
            'currency_id_fk' => CountryCurrency::where(DB::raw('UPPER(name)'),strtoupper($jsonData['Client']['ShippingCountry']))
                ->select('id')->first()->toArray()['id'],
            'is_active' => true,
            'si_factor' => $jsonData['Plan_Factor_Desc__c'],
            'is_base_plan' => $jsonData['Is_Base_Plan__c'],
            'is_default_selection' => $jsonData['Is_Default__c'],
            'is_point_value_based' => $jsonData['Is_Point_Value_Based__c'],
            'base_plan_id_sfdc' => $jsonData['Base_Plan__c'],
            // @todo 'base_plan_id' => $jsonData[''],
            // @todo 'base_plan_text' => $jsonData[''],
            'is_multi_selectable' => $jsonData['Is_Multiselect__c'],
            'show_value_column' => $jsonData['Is_Value_Based__c'],
            'created_by' => 0,    // admin
            'modified_by' => 0,    // admin
        ];
    }
}
?>