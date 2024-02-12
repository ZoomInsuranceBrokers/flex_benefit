<?php

namespace App\Http\Controllers;

use App\AccountTraits\accountTraitMethods;
use App\AccountTraits\financialYearTraitMethods;
use App\AccountTraits\gradeTraitMethods;
use App\AccountTraits\insuranceCategoryMethods;
use App\Models\Account;
use Illuminate\Http\Request;


class AccountController extends Controller
{
    use accountTraitMethods;
    use financialYearTraitMethods;
    use gradeTraitMethods;
    use insuranceCategoryMethods;
    public function setupAccount(Request $request) {
        if ($request->has('accId') && strlen(trim($request->accId))) {
            session(['confirmUpdate' => $request->has('confirmUpdate')]);
            $this->upsertAccount();


            // financial year

            // grade

            // insurance category

            // map_grade_category

            // insurance subcategory

            // insurance policy

            // map_financial_year_policy

            // 


        } else {
            die(__FUNCTION__ . ':ERROR:' . 'No Account ID detail found. Setup Failed!!');
        }

    }
}
