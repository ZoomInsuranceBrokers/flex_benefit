<?php

namespace App\Http\Controllers;

use DateTime;
use Dompdf\Dompdf;
use App\Models\User;
use NumberFormatter;
use App\Models\Account;
use App\Models\Dependant;
use App\Models\MapFYPolicy;
use Illuminate\Http\Request;
use App\Models\FinancialYear;
use App\Mail\SubmitEnrollment;
use App\Models\InsurancePolicy;
use App\Models\MapUserFYPolicy;
use App\Models\MapGradeCategory;
use App\Models\InsuranceCategory;
use Illuminate\Support\Facades\DB;
use App\Models\InsuranceSubCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Facade\FlareClient\Http\Response;
use Illuminate\Contracts\Session\Session;
use Dompdf\Options;
use Illuminate\Http\Client\Response as ClientResponse;

class EnrollmentController extends Controller
{
    public function home()
    {
        // function encryptData($data, $key, $iv) {
        //     $cipher = "aes-256-cbc";
        //     $options = 0;
        //     $encryptedData = openssl_encrypt($data, $cipher, $key, $options, $iv);
        //     return base64_encode($encryptedData);
        // }

        // function decryptData($encryptedData, $key, $iv) {
        //     $cipher = "aes-256-cbc";
        //     $options = 0;
        //     $decryptedData = openssl_decrypt(base64_decode($encryptedData), $cipher, $key, $options, $iv);
        //     return $decryptedData;
        // }

        // // Example usage:
        // $key = "your_secret_key"; // Replace with a secure key
        // $key = "QCsmMqMwEE+Iqfv0IIXDjAqrK4SOSp3tZfCadq1KlI4="; // Replace with a secure key
        // $iv = openssl_random_pseudo_bytes(16); // Initialization Vector
        // $iv = 'G4bfDHjL3gXiq5NCFFGnqQ==';
        // $dataToEncrypt = "Hello, Salesforce!";
        // $encryptedData = encryptData($dataToEncrypt, $key, $iv);

        // echo "Encrypted Data: " . $encryptedData . PHP_EOL;
        // $encryptedData = 'x0BhTs/2d4TR7NaEaCTJXG8hI1+jJ+OSg29ueuWZC/g=';
        // $decryptedData = decryptData($encryptedData, $key, $iv);
        // echo "Decrypted Data: " . $decryptedData . PHP_EOL;
        // exit;
        // get enrollment window and if it is open then only extract further data from db
        // $accountData = Account::all()->toArray();
        // $todayDate       = new DateTime(); // Today
        // $enrollmentDateBegin = new DateTime($accountData[0]['enrollment_start_date']);
        // $enrollmentDateEnd = new DateTime($accountData[0]['enrollment_end_date']);

        // financial year start and end date
        $fyData = FinancialYear::select('id', 'name', 'start_date', 'end_date', 'is_active', 'last_enrollment_date')
            ->orderBy('start_date', 'DESC')->limit(5)
            ->get()->toArray();
        //dd($fyData);
        foreach ($fyData as $fyd) {
            if ($fyd['is_active']) {
                session(['fy' => $fyd]);
                break;
            }
        }

        // check if data already final submission made
        $is_submitted = MapUserFYPolicy::where('user_id_fk', Auth::user()->id)->where('is_submitted', true)->get();
        if ($is_submitted->count()) {
            $is_submitted = TRUE;
        } else {
            $is_submitted = FALSE;
        }
        session(['is_submitted' => $is_submitted]);

        //if (session('is_enrollment_window')) {
        // is in between
        // category data
        $category = InsuranceCategory::where('is_active', true)->orderBy('sequence')->get();

        // sub-category data
        $data = DB::table('insurance_category as ic')
            ->leftJoin('insurance_subcategory as isc', 'isc.ins_category_id_fk', '=', 'ic.id')
            ->where('ic.is_active', '=', true)
            ->where('isc.is_active', '=', true)
            ->select('ic.id as ic_id', 'ic.name as category', 'sequence', 'tagline', 'isc.*')
            ->get();

        // get logged in user saved/selected policies
        $fypmapData = MapUserFYPolicy::where('is_active', true)
            ->with(['fyPolicy'])
            ->where('user_id_fk', '=', Auth::user()->id)
            //->whereRelation('policy', 'ins_subcategory_id_fk',$request->subCatId)
            ->get()->toArray();

        $currentSelectedData = $basePlan = [];
        if (count($fypmapData)) {
            foreach ($fypmapData as $fypRow) {
                if (!$fypRow['fy_policy']['policy']['is_base_plan'] && !$fypRow['fy_policy']['policy']['is_default_selection']) {
                    $currentSelectedData[$fypRow['fy_policy']['policy']['ins_subcategory_id_fk']][] = [
                        'polName' => $fypRow['fy_policy']['policy']['name'], 'points' => $fypRow['points_used']
                    ];
                }
            }
        }
        // dd($currentSelectedData);

        $basePlan = InsurancePolicy::where('is_base_plan', 1)
            ->orWhere('is_default_selection', 1)
            ->where('is_active', 1)
            ->with('subcategory')
            ->get()->toArray();

        session(['base_default_plans' => $basePlan]);
        //dd($basePlan);


        // mappedgrade data
        $mappedGradeData = User::where('id', Auth::user()->id)
            ->with(['grade'])
            ->whereRelation('grade', 'id', Auth::user()->grade_id_fk)
            ->get()->toArray();
        $gradeData = [];
        if (count($mappedGradeData)) {
            foreach ($mappedGradeData[0]['grade']['category_mapping'] as $gradeCatData) {
                $gradeData[$gradeCatData['category_id_fk']] = $gradeCatData['amount'];
            }
        }

        session(['gradeData' => $gradeData]);

        // dependant
        $dependants = Dependant::where('is_active', config('constant.$_YES'))
            //->where('is_deceased',config('constant.$_NO'))
            ->where('user_id_fk', Auth::user()->id)
            ->where('is_deceased', 0)
            ->orderBy('relationship_type')
            ->orderBy('dependent_code')
            ->get();

        $viewArray = [
            'sub_categories_data' => $data->toArray(),
            'category' => $category->toArray(),
            'currentSelectedData' => $currentSelectedData,
            'basePlan' => $basePlan,
            'gradeAmtData' => $gradeData,
            'dependant' => $dependants->toArray(),
            'fyData' => $fyData,
            'is_enrollment_window' => session('is_enrollment_window')
        ];

        // dd($viewArray);
        //} else {
        //    $viewArray = ['is_enrollment_window' => session('is_enrollment_window')];
        //}


        return view('enrollment')->with('data', $viewArray);
    }

    public function getInsuranceListBySubCategory(Request $request)
    {
        $userPolData = DB::table('map_user_fypolicy as mufyp')
            ->leftJoin('map_financial_year_policy as mfyp', 'mufyp.fypolicy_id_fk', '=', 'mfyp.id')
            ->leftJoin('financial_years as fy', 'fy.id', '=', 'mfyp.fy_id_fk')
            ->leftJoin('insurance_policy as ip', 'ip.id', '=', 'mfyp.ins_policy_id_fk')
            ->where('mufyp.user_id_fk', '=', Auth::user()->id)
            ->where('ip.ins_subcategory_id_fk', '=', $request->subCatId)
            ->where('ip.is_base_plan', '<>', config('constant.$_YES'))
            ->where('mufyp.is_active', '=', config('constant.$_YES'))
            ->where('mfyp.is_active', '=', config('constant.$_YES'))
            ->where('fy.is_active', '=', config('constant.$_YES'))
            ->where('ip.is_active', '=', config('constant.$_YES'))
            ->select('mufyp.id as mufypId', 'mfyp.id as mfypId', 'mufyp.points_used', /*'fy.name as fy_name', 'fy.start_date', 'fy.end_date',*/ 'ip.id as ip_id')
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
            ->with(['financialYears', 'policy'])
            ->whereRelation('policy', 'ins_subcategory_id_fk', $request->subCatId)
            ->get()->toArray();
        //dd($activePolicyForSubCategoryFY);

        //get subcategory and category for user grade
        $gradeData = session('gradeData');
        $gradeAmount = 0;
        $subCatData = null;
        if (count($gradeData)) {
            foreach ($gradeData as $gradeCatId => $gradeCatAmount) {
                $subCatData = InsuranceSubCategory::with('categories')
                    ->where('id', $request->subCatId)
                    ->whereRelation('categories', 'id', $gradeCatId)
                    ->get()->toArray();

                if (count($subCatData)) {
                    $gradeAmount = $gradeCatAmount;
                    break;
                }
            }
        }
        session(['gradeAmount' => [$gradeCatId => $gradeAmount]]);

        //dd($userPolData);
        //if (count($userPolData) && count($activePolicyForSubCategory) ) {
        $html = view('_partial.sub-category-details')
            ->with('userPolData', $userPolData)
            ->with('gradeAmount', $gradeAmount)
            ->with('activePolicyForSubCategoryFY', $activePolicyForSubCategoryFY)->render();
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

    public function saveEnrollment(Request $request)
    {
        $fypmap = $request->fypmap;
        $catId = $request->catId;
        $policyId = $request->policyId;
        $selDep = $request->sd;     // selected dependants
        $userId = Auth::user()->id;
        $summary = $request->summary;
        $points = $request->points;

        // get policy detail for generating encoded summary
        $policyDetail = MapFYPolicy::where('id', $fypmap)->with(['policy'])->get()->toArray();
        //dd($policyDetail);

        $userPolDataForCatId = DB::table('map_user_fypolicy as mufyp')
            ->select(
                'mufyp.points_used',
                'mufyp.id as userFYPolMapId',
                'mufyp.fypolicy_id_fk as fyPolMapId',
                'ip.id as ip_id',
                'ip.is_base_plan'
            )
            ->leftJoin('map_financial_year_policy as mfyp', 'mufyp.fypolicy_id_fk', '=', 'mfyp.id')
            ->leftJoin('financial_years as fy', 'fy.id', '=', 'mfyp.fy_id_fk')
            ->leftJoin('insurance_policy as ip', 'ip.id', '=', 'mfyp.ins_policy_id_fk')
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
            'selected_dependent' => $selDep,
            'encoded_summary' => $this->_generateEncodedSummary($catId, $policyDetail[0]), //$summary,
            //'encoded_summary' => $summary,
            'points_used' => $points,
            'created_by' => $userId,
            'modified_by' => $userId,
        ];

        $whereConditionData = ['id' => -1, 'user_id_fk' => -1];
        $mapUserFYPolicyRow = $message = null;
        $savedPoints = 0;
        $status = false;
        $user = User::where('id', Auth::user()->id)->get()->toArray();
        //if (count($userPolDataForCatId) > 0) {
        foreach ($userPolDataForCatId as $item) {
            if ($item->is_base_plan) {
                continue;
            } else {
                $whereConditionData['id'] = (int)$item->userFYPolMapId;
                $whereConditionData['user_id_fk'] = $userId;
                unset($data['created_by']);
                $savedPoints += $item->points_used;
            }
        }

        $mapUserFYPolicyRow = MapUserFYPolicy::updateOrCreate($whereConditionData, $data);
        //dd($mapUserFYPolicyRow);
        // update existing points in case policy is changed
        $userData = [
            'points_available' => $user[0]['points_available'] + $savedPoints - $points,
            'points_used' => $user[0]['points_used'] - $savedPoints + $points,
            'updated_at' => now()
        ];

        $status = true;
        User::where('id', Auth::user()->id)->update($userData);
        $message = 'Data ' . ($whereConditionData['id'] != -1 ? 'updated' : 'saved') . ' successfully. You need to do final submission(only one submission allowed per user per financial year) of data across policies/categories from "Summary" section post review';

        return response()->json([
            'status' => $status,
            //'responseRow' => $mapUserFYPolicyRow,
            'message' => $message
        ]);
    }

    public function saveEnrollmentPV(Request $request)
    {
        $catId = $request->catId;
        $savePoints = [];
        $userId = Auth::user()->id;
        $ids = [];
        foreach (json_decode(base64_decode($request->savePoints)) as $spItem) {
            $spRow = explode(':', $spItem);
            if ((int)$spRow[1]) {
                $savePoints[$spRow[0]] = (int)$spRow[1];
            }
        }
        //dd($savePoints);
        if (!count($savePoints)) {
            return response()->json([
                'status' => NULL,
                'message' => 'No plans selected or invalid points entered'
            ]);
        }

        // get policy details for each flexicash plans generating encoded summary
        $policyDetail = MapFYPolicy::whereIn('id', array_keys($savePoints))->with(['policy'])->get()->toArray();
        $policyDetailArr = [];
        foreach ($policyDetail as $polDetRow) {
            if ($polDetRow['policy']['is_point_value_based']) { // in case of point/value plan, update points
                $polDetRow['policy']['points'] = $savePoints[$polDetRow['id']];
            }
            $policyDetailArr[$polDetRow['id']] = $polDetRow;
        }
        $policyDetail = $policyDetailArr;

        $userPolDataForCatId = DB::table('map_user_fypolicy as mufyp')
            ->select('mufyp.points_used', 'mufyp.id')
            ->leftJoin('map_financial_year_policy as mfyp', 'mufyp.fypolicy_id_fk', '=', 'mfyp.id')
            ->leftJoin('financial_years as fy', 'fy.id', '=', 'mfyp.fy_id_fk')
            ->leftJoin('insurance_policy as ip', 'ip.id', '=', 'mfyp.ins_policy_id_fk')
            ->where('mufyp.user_id_fk', '=', $userId)
            ->where('mufyp.is_active', '=', true)
            ->where('mfyp.is_active', '=', true)
            ->where('fy.is_active', '=', true)
            ->where('ip.is_active', '=', true)
            ->where('ip.ins_subcategory_id_fk', '=', (int)$catId)
            ->get()->toArray();

        $totalPointsSaved = 0;
        $user = User::where('id', Auth::user()->id)->get()->toArray();
        if (count($userPolDataForCatId)) {
            foreach ($userPolDataForCatId as $fypmapEntry) {
                $ids[] = $fypmapEntry->id;
                $totalPointsSaved += $fypmapEntry->points_used;
            }
            MapUserFYPolicy::whereIn('id', $ids)->delete(); // deleting existing record on every save as updating existing ones may cause corrupted data
        }
        $finalData = [];
        $pointsCounter = 0;
        foreach ($savePoints as $fypmap => $points) {
            $finalData[] = [
                'user_id_fk' => $userId,
                'fypolicy_id_fk' => $fypmap,
                'points_used' => $points,
                //'encoded_summary' => $summary[$fypmap],
                'encoded_summary' => $this->_generateEncodedSummary($catId, $policyDetail[$fypmap]),
                'created_by' => $userId,
                'modified_by' => $userId,
                'created_at' => now(),
                'updated_at' => now()
            ];
            $pointsCounter += $points;
        }

        $message = null;
        $fypmapModel = new MapUserFYPolicy();
        $status = DB::table($fypmapModel->getTable())->insert($finalData);
        if ($status) {
            $message = 'Data saved successfully. You need to do final submission(only one submission allowed per user per financial year) of data across policies/categories from "Summary" section post review';

            // update total points
            $userData = [
                'points_available' => $user[0]['points_available'] - $pointsCounter  + $totalPointsSaved,
                'points_used' => $user[0]['points_used'] + $pointsCounter  - $totalPointsSaved,
                'updated_at' => now()
            ];
            User::where('id', Auth::user()->id)->update($userData);

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

    private function _generateEncodedSummary($catId, $fymapDet)
    {
        $fypmap = $fymapDet['id'];
        $polDet = $fymapDet['policy'];
        $formatter = new NumberFormatter('en_GB',  NumberFormatter::CURRENCY);
        $basePlans = session('base_default_plans');
        $gradeAmount = array_key_exists($catId, session('gradeAmount')) ? session('gradeAmount') : 0;
        $bpsa = 0;
        $bpName = '';
        $is_lumpsum = $is_si_sa = $is_sa = $is_grade_based = FALSE;
        $base_si_factor = 0;
        $encryptedData =  Auth::user()->salary;
        $encryptionKey = 'QCsmMqMwEE+Iqfv0IIXDjAqrK4SOSp3tZfCadq1KlI4=';
        $initializationVector = 'G4bfDHjL3gXiq5NCFFGnqQ==';

        // Decrypt the data
        $cipher = "aes-256-cbc";
        $options = OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING;

        $salary = openssl_decrypt(base64_decode($encryptedData), $cipher, base64_decode($encryptionKey), $options, base64_decode($initializationVector));

        if ($salary === false) {
            echo "Error during decryption: " . openssl_error_string() . PHP_EOL;
        } else {
            $salary = floatval(rtrim($salary, "\0"));
        }
        foreach ($basePlans as $bpRow) {
            if ($bpRow['ins_subcategory_id_fk'] == $catId && $bpRow['is_base_plan']) {
                if ($gradeAmount) {
                    $bpsa = (int)$gradeAmount;
                    $is_grade_based = TRUE;
                } else {
                    $sa = !is_null($bpRow['sum_insured']) ? $bpRow['sum_insured'] : 0;
                    $sa_si = !is_null($bpRow['si_factor']) ?
                        $sa_si = $bpRow['si_factor'] * $salary : 0;
                    if ($sa_si > $sa) {
                        $bpsa = (int)$sa_si;
                        $is_si_sa = TRUE;
                        $base_si_factor = $bpRow['si_factor'];
                    } else {
                        $bpsa = (int)$sa;
                        $is_sa = TRUE;
                    }
                }
                // name of base policy
                $bpName = $bpRow['name'];
                break;
            }
        }

        $dataArr = [];
        $dataArr['extId'] = $polDet['external_id'];
        $dataArr['user'] = Auth::user()->fname . ' ' . Auth::user()->lname . '[EMPID:' . Auth::user()->employee_id . ']';
        $dataArr['extId'] = $polDet['external_id'];
        $dataArr['ptf'] = $polDet['price_tag'];
        $dataArr['bpName'] = $bpName;
        $dataArr['pt'] = $formatter->formatCurrency($polDet['points'], 'INR');
        $dataArr['name'] = $polDet['name'];
        $dataArr['osa'] = $formatter->formatCurrency($polDet['sum_insured'], 'INR');
        $dataArr['is-sa'] = $is_sa;
        $dataArr['is-si-sa'] = $is_si_sa;
        $dataArr['grdbsd'] = $is_grade_based;
        $dataArr['fypmap'] = $fypmap;
        $dataArr['isbp'] = $polDet['is_base_plan'];
        $dataArr['bpsa'] = $bpsa > 0 ? $formatter->formatCurrency($bpsa, 'INR') : '';
        $dataArr['opplsa'] = !$polDet['is_base_plan'] ? $formatter->formatCurrency($polDet['sum_insured'], 'INR') : 0;
        $dataArr['totsa'] = $formatter->formatCurrency(($bpsa + (!$polDet['is_base_plan'] ? (int)$polDet['sum_insured'] : 0)), 'INR');
        $dataArr['isvp'] = $polDet['is_point_value_based'];
        $dataArr['isvbsd'] = $polDet['show_value_column'];
        $dataArr['annup'] = $formatter->formatCurrency($polDet['points'], 'INR');
        $dataArr['annupwocurr'] = $polDet['points'];
        $fyStartDate = session('fy')['start_date'];
        $fyEndDate = session('fy')['end_date'];
        $joiningDate = Auth::user()->hire_date;
        $policyStartDate = $joiningDate > $fyStartDate ? $joiningDate : $fyStartDate;
        $dataArr['psd'] =  date_format(date_create($policyStartDate), 'd-M-Y');
        $dataArr['ped'] = date_format(date_create($fyEndDate), 'd-M-Y');
        $totalDays = date_diff(date_create($policyStartDate), date_create($fyEndDate));
        $dataArr['totdc'] = $totalDays->days . ' Days';
        $prorationfactor = number_format(($totalDays->days / date_diff(
            date_create($fyStartDate),
            date_create($fyEndDate)
        )->days) * 100, '2', '.', '');
        $dataArr['prorf'] = $prorationfactor;
        $pts = 0;
        if ($polDet['is_point_value_based']) {
            $pts = $polDet['points'];
        } else {
            if (!is_null($polDet['price_tag']) && $polDet['price_tag'] > 0) {
                $pts = ($polDet['sum_insured']) * $polDet['price_tag'] * ($prorationfactor / 100);
            } else if (!is_null($polDet['points'])) {
                $pts = $polDet['points'] * ($prorationfactor / 100);
            }
        }
        $dataArr['opplpt'] = !$polDet['is_base_plan'] ? $formatter->formatCurrency(round($pts), 'INR') : 0;
        $dataArr['effecp'] = !$polDet['is_base_plan'] ? $formatter->formatCurrency(round($pts), 'INR') : 0;
        $dataArr['totpt'] = !$polDet['is_base_plan'] ? $formatter->formatCurrency(round($pts), 'INR') : 0;
        $dataArr['totptwocurr'] = !$polDet['is_base_plan'] ? round($pts) : 0;
        $dataArr['memcvrd'] = $polDet['dependent_structure'];
        $dataArr['prntSbLim'] = $polDet['is_parent_sublimit'] ? $formatter->formatCurrency($polDet['parent_sublimit_amount'], 'INR') : 0;
        $dataArr['corem'] = $base_si_factor . 'X of CTC';
        $dataArr['coresa'] = $formatter->formatCurrency($bpsa, 'INR');
        $dataArr['jongDate'] = Auth::user()->hire_Date;

        return base64_encode(json_encode($dataArr));
    }

    public function loadSummary(Request $request)
    {
        $mapUserFYPolicyData = MapUserFYPolicy::where('user_id_fk', '=', Auth::user()->id)->with(['fyPolicy']);
        $activeEntries = true;
        // dd(base64_decode($request->fid));
        if ($request->has('fid') && base64_decode($request->fid)) {
            $mapUserFYPolicyData->whereRelation('fyPolicy.financialYears', 'id', base64_decode($request->fid));
            $activeEntries = null;
        }
        $activeEntries ? $mapUserFYPolicyData->where('is_active', true) : '';
        //$mapUserFYPolicyData->toSql();
        $mapUserFYPolicyData = $mapUserFYPolicyData->get()->toArray();
        //dd($mapUserFYPolicyData);
        $html = view('summary')
            ->with('mapUserFYPolicyData', $mapUserFYPolicyData)->render();
        return $html;
    }

    public function downloadSummary()
    {
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

    public function submitEnrollment(Request $request)
    {
        if ($request->session()->has('is_submitted') && !session('is_submitted')) {
            MapUserFYPolicy::where('user_id_fk', Auth::user()->id)
                ->where('is_active', true)
                ->update(['is_submitted' => true, 'modified_by' => Auth::user()->id, 'updated_at' => now()]);

            // update user submission columns
            $userUpdateData = [
                'is_enrollment_submitted' => true,
                'enrollment_submit_date' => now(),
                'submission_by' => Auth::user()->id
            ];
            // User::whereIn('id',Auth::user()->id)->update($userUpdateData);

            $email =  Auth::user()->email;
            $user = DB::table('users')->where('email', $email)->first();
            $mapUserFYPolicyData = MapUserFYPolicy::where('user_id_fk', '=', Auth::user()->id)
                ->with(['fyPolicy'])
                ->get()
                ->toArray();

            Mail::to($email)->send(new SubmitEnrollment($user, $mapUserFYPolicyData));

            return json_encode(['status' => true, 'msg' => 'Submission Successfull!!']);
        }
    }

    public function resetCategory(Request $request)
    {
        $userPolData = DB::table('map_user_fypolicy as mufyp')
            ->leftJoin('map_financial_year_policy as mfyp', 'mufyp.fypolicy_id_fk', '=', 'mfyp.id')
            ->leftJoin('financial_years as fy', 'fy.id', '=', 'mfyp.fy_id_fk')
            ->leftJoin('insurance_policy as ip', 'ip.id', '=', 'mfyp.ins_policy_id_fk')
            ->where('mufyp.user_id_fk', '=', Auth::user()->id)
            ->where('ip.ins_subcategory_id_fk', '=', $request->catId)
            ->where('mufyp.is_active', '=', true)
            ->where('mfyp.is_active', '=', true)
            ->where('fy.is_active', '=', true)
            ->where('ip.is_active', '=', true)
            // ->where(function ($query) {
            //     $query->where('ip.is_base_plan','<>', true)
            //           ->orWhere('ip.is_default_selection','<>',true);
            // })
            ->where('ip.is_base_plan', '<>', true)
            //->where('ip.is_default_selection', '<>', true)
            ->select('mufyp.id as mufypId', 'mfyp.id as mfypId', 'mufyp.points_used', 'ip.id as ip_id')
            ->get()->toArray();
        //->toSql();
        // dd($userPolData);
        $pointsCounter = 0;
        $ids = [];

        if (count($userPolData)) {    // other policy saved apart from base_plan
            foreach ($userPolData as $polRow) {
                $pointsCounter += $polRow->points_used;
                $ids[] = $polRow->mufypId;
            }
        }
        MapUserFYPolicy::whereIn('id', $ids)->delete(); // deleting existing record on every reset as soft deleting existing ones may cause corrupted data

        // update user points
        $user = User::where('id', Auth::user()->id)->get()->toArray();
        $userData = [
            'points_available' => $user[0]['points_available'] + $pointsCounter,
            'points_used' => $user[0]['points_used'] - $pointsCounter,
            'updated_at' => now()
        ];
        User::where('id', Auth::user()->id)->update($userData);

        // non-base is_default_selection re-entry 
        $insurancePolicyDefault = InsurancePolicy::where('is_active', true)
            ->with(['mapFyPolicies'])
            ->where('ins_subcategory_id_fk', $request->catId)
            ->where('is_base_plan', '<>', true)
            ->where('is_default_selection', true)
            ->get()->toArray();

        $fyPolId = 0;
        if (count($insurancePolicyDefault)) {
            foreach ($insurancePolicyDefault as $polDefRow) {
                if ($polDefRow['is_default_selection']) {
                    foreach ($polDefRow['map_fy_policies'] as $polfyPol) {
                        if ($polfyPol['is_active']) {
                            $fyPolId = $polfyPol['id']; // Map_FY_Policy ID for entry in MapUserFYPol table
                            break;
                        }
                    }
                }
            }
            // default policy re-added
            $mapUserFYPolicyData = [
                'user_id_fk' => Auth::user()->id,
                'fypolicy_id_fk' => $fyPolId,
                'created_by' => Auth::user()->id,
                'modified_by' => Auth::user()->id,
                'created_at' => now(),
                'updated_at' => now()
            ];
            MapUserFYPolicy::insert($mapUserFYPolicyData);

            // make entries in_active in mapUserFYPolicyTable
            // MapUserFYPolicy::whereIn('id',$ids)->update([
            //     'is_active' => false,
            //     'modified_by' => Auth::user()->id,
            //     'updated_at' => now()
            // ]);

            $response = ['status' => true, 'msg' => 'Reset Done. Default entries added!!!'];
        } else {
            $response = ['status' => true, 'msg' => 'Reset Done. No default entries present!!!'];
        }
        return json_encode($response);
    }

    public function getPoints(Request $request)
    {
        // get logged in user saved/selected policies
        $fypmapData = MapUserFYPolicy::where('is_active', true)
            ->with(['fyPolicy'])
            ->where('user_id_fk', '=', Auth::user()->id)
            ->get()->toArray();

        $currentSelectedData = $basePlan = [];
        if (count($fypmapData)) {
            foreach ($fypmapData as $fypRow) {
                if (!$fypRow['fy_policy']['policy']['is_base_plan'] && !$fypRow['fy_policy']['policy']['is_default_selection']) {
                    $currentSelectedData[$fypRow['fy_policy']['policy']['ins_subcategory_id_fk']][] = [
                        'polName' => $fypRow['fy_policy']['policy']['name'], 'points' => $fypRow['points_used']
                    ];
                }
            }
        }

        $userPoints = User::where('id', Auth::user()->id)->select(['points_used', 'points_available'])->get()->toArray();

        return json_encode(['userpts' => $userPoints, 'catpts' => $currentSelectedData]);
    }

    public function updateBaseDefaultEncodedSummary(Request $request)
    {
        $basePlan = InsurancePolicy::where('is_base_plan', 1)
            ->orWhere('is_default_selection', 1)
            ->where('is_active', 1)
            ->with('subcategory')
            ->get()->toArray();

        session(['base_default_plans' => $basePlan]);

        $userPolDataObj = DB::table('map_user_fypolicy as mufyp')->select(
            'mufyp.id as mufypId',
            'mufyp.fypolicy_id_fk',
            'ip.*',
            'u.id as uid',
            'u.fname',
            'u.lname',
            'u.employee_id',
            'u.salary',
            'mgc.amount as gradeBasedAmount',
            'fy.start_date',
            'fy.end_date',
            'u.hire_date'
        )
            ->orderBy('u.id');
        $userPolDataObj
            ->leftJoin('map_financial_year_policy as mfyp', 'mufyp.fypolicy_id_fk', '=', 'mfyp.id')
            ->leftJoin('financial_years as fy', 'mfyp.fy_id_fk', '=', 'fy.id')
            ->leftJoin('insurance_policy as ip', 'ip.id', '=', 'mfyp.ins_policy_id_fk')
            //->leftJoin('insurance_subcategory as isc', 'isc.id', '=', 'ip.ins_subcategory_id_fk')
            //->leftJoin('insurance_category as ic', 'ic.id', '=', 'isc.ins_category_id_fk')
            ->leftJoin('users as u', 'u.id', '=', 'mufyp.user_id_fk')
            ->leftJoin('map_grade_category as mgc', 'mgc.grade_id_fk', '=', 'u.grade_id_fk');

        // ->where('ip.is_base_plan', 1)
        // ->orWhere('ip.is_default_selection', 1)
        //->where('ip.ins_subcategory_id_fk', '=', $request->catId)
        $userPolDataObj->where('mufyp.is_active', 1)
            ->where('mfyp.is_active', 1)
            ->where('fy.is_active', 1)
            ->where('ip.is_active', 1)
            //->where('isc.is_active', 1)
            //->where('ic.is_active', 1)
            //->where('mgc.is_active', 1)
            ->where('u.is_active', 1)
            ->whereNull('mufyp.encoded_summary',)
            ->orderBy('u.id');

        if ($request->has('eid') && $request->eid) {
            $userPolDataObj->where('u.id', $request->eid);
        }
        if (!$request->has('includeAllBaseDefault')) {  // filter only default/base when no parameter coming in URL
            $userPolDataObj->where(function ($query) {
                $query->where('ip.is_base_plan', 1)
                    ->orWhere('ip.is_default_selection', 1);
            });
        }
        $userPolData =   $userPolDataObj->get()->toArray();
        //->toSql();
        //dd($basePlan);
        if (count($userPolData)) {
            foreach ($userPolData as $rowData) {
                $data = [
                    'encoded_summary' => $this->_generateBaseDefaultEncodedSummary($rowData),
                    'modified_by' => 0,
                    'updated_at' => now()
                ];
                if ($request->has('confirmUpdate') && $request->confirmUpdate) {
                    MapUserFYPolicy::where('id', $rowData->mufypId)->update($data);
                }
                echo '<br>Data of: ' . $rowData->fname . ' ' . $rowData->lname . '[Policy Name:' .
                    $rowData->name . '][UID: ' . $rowData->uid . ',EMPID:' . $rowData->employee_id
                    . '][Data:' . json_encode($data) . ']<br>';
            }
            echo '<h1>Encoded summary ' . ($request->has('confirmUpdate') && $request->confirmUpdate ? '' : 'to be') . ' updated for row count ' . count($userPolData) . '</h1>';
        } else {
            echo 'No empty encoded summary rows found!!!';
            die;
        }
    }

    private function _generateBaseDefaultEncodedSummary($data)
    {
        $fypmap = $data->fypolicy_id_fk;
        //$polDet = $data['policy'];
        $formatter = new NumberFormatter('en_GB',  NumberFormatter::CURRENCY);
        $gradeAmount = $data->is_grade_based ? $data->gradeBasedAmount : 0;
        $basePlans = session('base_default_plans');
        if (!$data->is_grade_based) {   // select grade amount in case current policy is not grade based but core policy is grade based
            foreach ($basePlans as $bpRow) {
                if (
                    $bpRow['ins_subcategory_id_fk'] == $data->ins_subcategory_id_fk &&
                    $bpRow['is_base_plan'] && $bpRow['is_grade_based']
                ) {
                    $gradeAmount = $data->gradeBasedAmount;
                }
            }
        }
        $bpsa = 0;
        $bpName = '';
        $is_lumpsum = $is_si_sa = $is_sa = $is_grade_based = FALSE;
        $base_si_factor = 0;
        $salary =  decryptAES(['data' => $data->salary, 'type' => 'salary']);

        foreach ($basePlans as $bpRow) {
            if (
                $bpRow['ins_subcategory_id_fk'] == $data->ins_subcategory_id_fk &&
                $bpRow['is_base_plan']
            ) {
                if ($gradeAmount) {
                    $bpsa = (int)$gradeAmount;
                    $is_grade_based = TRUE;
                } else {
                    $sa = !is_null($data->sum_insured) ? $data->sum_insured : 0;
                    $sa_si = !is_null($data->si_factor) ?
                        $sa_si = $data->si_factor * $salary : 0;
                    if ($sa_si > $sa) {
                        $bpsa = (int)$sa_si;
                        $is_si_sa = TRUE;
                        $base_si_factor = $data->si_factor;
                    } else {
                        $bpsa = (int)$sa;
                        $is_sa = TRUE;
                    }
                }
                // name of base policy
                $bpName = $data->name;
                //break;
            }
        }


        $dataArr = [];
        $dataArr['extId'] = $data->external_id;
        $dataArr['user'] = $data->fname . ' ' . $data->lname . '[EMPID:' . $data->employee_id . ']';
        $dataArr['ptf'] = $data->price_tag;
        $dataArr['bpName'] = $bpName;
        $dataArr['pt'] = $formatter->formatCurrency($data->points, 'INR');
        $dataArr['name'] = $data->name;
        $dataArr['osa'] = $formatter->formatCurrency($data->sum_insured, 'INR');
        $dataArr['is-sa'] = $is_sa;
        $dataArr['is-si-sa'] = $is_si_sa;
        $dataArr['grdbsd'] = $is_grade_based;
        $dataArr['fypmap'] = $fypmap;
        $dataArr['isbp'] = $data->is_base_plan;
        $dataArr['bpsa'] = $bpsa > 0 ? $formatter->formatCurrency($bpsa, 'INR') : '';
        $dataArr['opplsa'] = !$data->is_base_plan ? $formatter->formatCurrency($data->sum_insured, 'INR') : 0;
        $dataArr['totsa'] = $formatter->formatCurrency(($bpsa + (!$data->is_base_plan ? (int)$data->sum_insured : 0)), 'INR');
        $dataArr['isvp'] = $data->is_point_value_based;
        $dataArr['isvbsd'] = $data->show_value_column;
        $dataArr['annup'] = $formatter->formatCurrency($data->points, 'INR');
        $dataArr['annupwocurr'] = $data->points;
        $fyStartDate = $data->start_date;
        $fyEndDate = $data->end_date;
        $joiningDate = $data->hire_date;
        $policyStartDate = $joiningDate > $fyStartDate ? $joiningDate : $fyStartDate;
        $dataArr['psd'] =  date_format(date_create($policyStartDate), 'd-M-Y');
        $dataArr['ped'] = date_format(date_create($fyEndDate), 'd-M-Y');
        $totalDays = date_diff(date_create($policyStartDate), date_create($fyEndDate));
        $dataArr['totdc'] = $totalDays->days . ' Days';
        $prorationfactor = number_format(($totalDays->days / date_diff(
            date_create($fyStartDate),
            date_create($fyEndDate)
        )->days) * 100, '2', '.', '');
        $dataArr['prorf'] = $prorationfactor;
        $pts = 0;
        if ($data->is_point_value_based) {
            $pts = $data->points;
        } else {
            if (!is_null($data->price_tag) && $data->price_tag > 0) {
                $pts = ($data->sum_insured) * $data->price_tag * ($prorationfactor / 100);
            } else if (!is_null($data->points)) {
                $pts = $data->points * ($prorationfactor / 100);
            }
        }
        $dataArr['opplpt'] = !$data->is_base_plan ? $formatter->formatCurrency(round($pts), 'INR') : 0;
        $dataArr['effecp'] = !$data->is_base_plan ? $formatter->formatCurrency(round($pts), 'INR') : 0;
        $dataArr['totpt'] = !$data->is_base_plan ? $formatter->formatCurrency(round($pts), 'INR') : 0;
        $dataArr['totptwocurr'] = !$data->is_base_plan ? round($pts) : 0;
        $dataArr['memcvrd'] = $data->dependent_structure;
        $dataArr['prntSbLim'] = $data->is_parent_sublimit ? $formatter->formatCurrency($data->parent_sublimit_amount, 'INR') : 0;
        $dataArr['corem'] = $base_si_factor . 'X of CTC';
        $dataArr['coresa'] = $formatter->formatCurrency($bpsa, 'INR');
        $dataArr['jongDate'] = $data->hire_date;


        //print '<pre>';
        //print_r($dataArr);

        return base64_encode(json_encode($dataArr));
    }

    public function downloadByBackend()
    {
        $fyData = FinancialYear::select('id', 'name', 'start_date', 'end_date', 'is_active', 'last_enrollment_date')
            ->orderBy('start_date', 'DESC')->limit(5)
            ->get()->toArray();
        foreach ($fyData as $fyd) {
            if ($fyd['is_active']) {
                session(['fy' => $fyd]);
                break;
            }
        }

        $is_submitted = MapUserFYPolicy::where('user_id_fk', Auth::user()->id)->where('is_submitted', true)->get();
        if ($is_submitted->count()) {
            $is_submitted = TRUE;
        } else {
            $is_submitted = FALSE;
        }
        session(['is_submitted' => $is_submitted]);


        $category = InsuranceCategory::where('is_active', true)->orderBy('sequence')->get();

        $data = DB::table('insurance_category as ic')
            ->leftJoin('insurance_subcategory as isc', 'isc.ins_category_id_fk', '=', 'ic.id')
            ->where('ic.is_active', '=', true)
            ->where('isc.is_active', '=', true)
            ->select('ic.id as ic_id', 'ic.name as category', 'sequence', 'tagline', 'isc.*')
            ->get();

        $fypmapData = MapUserFYPolicy::where('is_active', true)
            ->with(['fyPolicy'])
            ->where('user_id_fk', '=', Auth::user()->id)
            //->whereRelation('policy', 'ins_subcategory_id_fk',$request->subCatId)
            ->get()->toArray();

        $currentSelectedData = $basePlan = [];
        if (count($fypmapData)) {
            foreach ($fypmapData as $fypRow) {
                if (!$fypRow['fy_policy']['policy']['is_base_plan'] && !$fypRow['fy_policy']['policy']['is_default_selection']) {
                    $currentSelectedData[$fypRow['fy_policy']['policy']['ins_subcategory_id_fk']][] = [
                        'polName' => $fypRow['fy_policy']['policy']['name'], 'points' => $fypRow['points_used']
                    ];
                }
            }
        }
        // dd($currentSelectedData);

        $basePlan = InsurancePolicy::where('is_base_plan', 1)
            ->orWhere('is_default_selection', 1)
            ->where('is_active', 1)
            ->with('subcategory')
            ->get()->toArray();

        session(['base_default_plans' => $basePlan]);
        //dd($basePlan);


        // mappedgrade data
        $mappedGradeData = User::where('id', Auth::user()->id)
            ->with(['grade'])
            ->whereRelation('grade', 'id', Auth::user()->grade_id_fk)
            ->get()->toArray();
        $gradeData = [];
        if (count($mappedGradeData)) {
            foreach ($mappedGradeData[0]['grade']['category_mapping'] as $gradeCatData) {
                $gradeData[$gradeCatData['category_id_fk']] = $gradeCatData['amount'];
            }
        }

        session(['gradeData' => $gradeData]);

        $mapUserFYPolicyData = MapUserFYPolicy::where('user_id_fk', '=', Auth::user()->id)->with(['fyPolicy'])
            ->get()->toArray();

        $data['gradeAmtData'] = session('gradeData');
        $formatter = new NumberFormatter('en_GB', NumberFormatter::CURRENCY);
        $summaryData = [];
        $summaryDataBaseDefault = [];
        foreach ($mapUserFYPolicyData as $item) {
            if (!$item['fy_policy']['policy']['is_base_plan'] && !$item['fy_policy']['policy']['is_default_selection']) {
                $summaryData[$item['id']]['category'] = $item['fy_policy']['policy']['subcategory']['categories']['name'];
                $summaryData[$item['id']]['subcategory'] = $item['fy_policy']['policy']['subcategory']['name'];
                $summaryData[$item['id']]['subcategory_id'] = $item['fy_policy']['policy']['subcategory']['id'];
                $summaryData[$item['id']]['policy'] = $item['fy_policy']['policy']['name'];
                $summaryData[$item['id']]['policyDetail'] = $item['fy_policy']['policy'];
                $summaryData[$item['id']]['summary'] = json_decode(base64_decode($item['encoded_summary']));
                $summaryData[$item['id']]['points'] = $item['points_used'];
                $summaryData[$item['id']]['is_base_default'] = FALSE;
            } else {
                $summaryDataBaseDefault[$item['id']]['category'] = $item['fy_policy']['policy']['subcategory']['categories']['name'];
                $summaryDataBaseDefault[$item['id']]['subcategory'] = $item['fy_policy']['policy']['subcategory']['name'];
                $summaryDataBaseDefault[$item['id']]['subcategory_id'] = $item['fy_policy']['policy']['subcategory']['id'];
                $summaryDataBaseDefault[$item['id']]['policy'] = $item['fy_policy']['policy']['name'];
                $summaryDataBaseDefault[$item['id']]['policyDetail'] = $item['fy_policy']['policy'];
                $summaryDataBaseDefault[$item['id']]['summary'] = json_decode(base64_decode($item['encoded_summary']));
                $summaryDataBaseDefault[$item['id']]['points'] = $item['points_used'];
                $summaryDataBaseDefault[$item['id']]['is_base_default'] = TRUE;
            }
        }

        foreach ($summaryDataBaseDefault as $baseDefaultKey => $baseDefaultRow) {
            foreach ($summaryData as $summaryRow) {
                if ($summaryRow['subcategory_id'] == $baseDefaultRow['subcategory_id']) {
                    unset($summaryDataBaseDefault[$baseDefaultKey]);
                }
            }
        }
        $summaryData = $summaryData + $summaryDataBaseDefault;

        ksort($summaryData);

        $image_path = public_path('assets/images/invoice-01.jpg');

        // Check if the image exists
        if (!file_exists($image_path)) {
            abort(404, 'Image not found');
        }
        $forward_img =  public_path('assets/images/forward.png');
        // Get image data and encode it to base64
        $image_data = file_get_contents($image_path);
        $forward_img_data = file_get_contents($forward_img);
        $image_base64 = base64_encode($image_data);
        $forward_image_base64 =  base64_encode($forward_img_data);
        
        // Get authenticated user's information
        $user = Auth::user();
        
        // Construct the HTML template
        $html = '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Invoice</title>
            <style>
            body {
                font-family: "Calibri", sans-serif;
            }
    
            .bold-text {
                font-weight: bold;
                font-size: 1.5rem;
            }
    
            .light-text {
                font-weight: lighter;
            }
    
            .first-part {
                width: 100%;
                height: auto;
            }
    
            .first-part img {
                width: 100%;
                height: 30vh;
            }
    
            .left-image {
                width: 3%;
                height: inherit;
                float: left;
            }
    
            .second-part {
                width: 100%;
                height: 10vh;
                display: flex;
                justify-content: space-between;
                width: 100%;
                margin-top: 20px;
            }
    
            .lines {
                margin-right: 20vh;
                display: flex;
            }
    
            .second1 {
                display: flex;
            }
    
            .semi-circle {
                position: relative;
                width: 4vh;
                height: 7vh;
                border-width: 15px 15px 15px 0;
                border-style: solid;
                border-color: #B5B5B5;
                border-top-right-radius: 60px;
                border-bottom-right-radius: 60px;
            }
    
            .right-semi-circle {
                position: relative;
                width: 5vh;
                height: 8vh;
                border-width: 15px 0 15px 15px;
                border-style: solid;
                border-color: #03313D;
                border-top-left-radius: 60px;
                border-bottom-left-radius: 60px;
            }
    
            .diagonal-line {
                width: 2px;
                height: 14vh;
                background-color: #03313D;
                transform: rotate(45deg);
                transform-origin: 0 100%;
                margin: 10px;
                position: relative;
                bottom: 35px;
            }
    
            .box {
                display: flex;
                align-items: center;
                justify-content: center;
                height: 7vh;
                border-radius: 10px;
                text-align: center;
                font-weight: 900;
                background-color: #B5B5B5;
            }
    
            .box1 {
                width: 38%;
                margin: 0 1%;
            }
    
            .box2 {
                width: 59%;
                margin-right: 1%;
            }
    
            .box1 ul {
                list-style: none;
                margin: 0;
                padding: 0;
            }
    
            .box1 li {
                position: relative;
                padding-left: 20px;
                margin-bottom: 10px;
            }
    
            .box1 li::before {
                content: "";
                position: absolute;
                left: 0;
            }
    
            .inner-li {
                margin-left: 20px;
                margin-bottom: 10px;
            }
    
            .inner-li img {
                vertical-align: middle;
                margin-right: 5px;
                width: 3vh;
            }
    
            .third-part {
                width: 100%;
            }
    
            .forth-part {
                width: 100%;
                height: 20vh;
                display: flex;
                flex-direction: column-reverse;
                align-items: flex-end;
            }
    
            .fifth-part {
                width: 100%;
                margin-top: 3vh;
                height: auto;
            }
    
            .fifth-part img {
                width: 100%;
                height: 20vh;
            }
    
    
            @media (max-width: 600px) {
                .first-part {
                    width: 100%;
                    height: 10vh;
                }
    
                .first-part img {
                    width: 100%;
                    height: auto;
                }
            }
    
            .top-div {
                width: 70%;
                position: absolute;
                left: 50%;
                transform: translateX(-50%);
            }
    
            .first-part,
            .second-part,
            .third-part,
            .forth-part,
            .fifth-part {
                text-align: center;
            }
        </style>
        </head>
        <body>
            <div class="container">
                <div class="first-part">
                    <img src="data:image/jpeg;base64,'. $image_base64 .'" alt="Invoice">
                </div>
                <div class="second-part">
                    <div class="second1">
                        <div class="semi-circle"></div>
                        <div class="">
                            <h3 class="light-text" style="margin-left:7vh;">'. $user->fname . ' ' . $user->lname .'</h3>
                            <h3 class="light-text" style="margin-left:7vh;">'. $user->employee_id .'</h3>
                        </div>
                    </div>
                    <div class="lines">
                            <div class="diagonal-line"></div>
                            <div class="diagonal-line"></div>
                            <div class="diagonal-line"></div>
                            <div class="diagonal-line"></div>
                            <div class="diagonal-line"></div>
                            <div class="diagonal-line"></div>
                            <div class="diagonal-line"></div>
                            <div class="diagonal-line"></div>
                            <div class="diagonal-line"></div>
                    </div>
                    </div>
                    <div class="third-part">
                        <div style="display: flex;margin-top: 5vh;">
                            <div class="bold-text box box1">Name</div>
                            <div class="bold-text box box2">Detail</div>
                        </div>';
    
    foreach ($summaryData as $item) {
        $html .= '
                        <div style="display: flex;margin-top: 1vh; border-bottom: 3px solid #B5B5B5;">
                            <div class="box1 bold-text">
                                <ul>
                                    <li>' . $item['category'] . '
                                        <ul class="inner-li" style="margin-top: 2vh;">
                                            <li>
                                                <img  src="data:image/png;base64,'. $forward_image_base64 .'" alt="Invoice">
                                                ' . $item['subcategory'] . '
                                                <ul class="inner-li" style="margin-top: 2vh;">
                                                    <li>
                                                        <img src="data:image/png;base64,'. $forward_image_base64 .'" alt="Invoice">
                                                        ' . $item['policy'] . '
                                                    </li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                            <div class="box2">
                                <div style="display:flex;justify-content:space-between;align-items:center; border-bottom: 1px solid #B5B5B5;">
                                    <p style="width: 30%;padding: 10px; display: inline; margin: 0px;" class="bold-text">Base Plan Coverage <br><span class="light-text">' . (!$item['is_base_default'] && strlen($item['summary']->bpsa) ? $item['summary']->bpsa : 'N.A.') . '</span></p>
                                    <p style="width: 30%;padding: 10px; display: inline; margin: 0px;" class="bold-text">Optional Coverage <br><span class="light-text">' . (!$item['is_base_default'] ? $item['summary']->opplsa : 'N.A.') . '</span></p>
                                    <p style="width: 30%;padding: 10px; display: inline; margin: 0px;" class="bold-text">Total Coverage <br> <span class="light-text"></span></p>
                                </div>
                                <p style="width: 100%;padding:10px; margin: 0px; text-align: left;" class="bold-text">Point Used <br> <span class="light-text"></span></p>
                            </div>
                        </div>';
    }

           $html .= '   <div style="display: flex;justify-content: center;flex-direction: column; height:30vh;  border-bottom: 3px solid #B5B5B5; " class="bold-text">
           <p style="margin:0px; margin-left: 47%;  display: inline;">Total Points Used: 31100</p>
                <p style="margin:0px; margin-left: 47%; display: inline;">Salary Contribuation: 26900</p>
                <p style="margin:0px; margin-left: 47%; display: inline;">Monthly Installment:
                    3000
                </p>
       </div>
    </div>
            <div class="forth-part">
            <div class="right-semi-circle"></div>
            </div>
            <div class="fifth-part">
            <img  alt="">
            </div>

           </div>
        </body>
        </html>';
        
        // Generate PDF
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        
        // Download the PDF file
        return $dompdf->stream('summary.pdf');
        
    }

    /* public function generateBaseDefaultPolicyMapping ($users, $confirmUpdate) {
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
                    'points_used' => 0,
                    'created_by' => '0',
                    'modified_by' => '0',
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
            echo __FUNCTION__ . ':INFO:Default Policy entries added for userId:' . $user['id'];
        }
        $confirmUpdate ? MapUserFYPolicy::insert($data) : '';
    } */
}
