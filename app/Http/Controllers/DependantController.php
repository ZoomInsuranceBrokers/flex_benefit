<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Dependant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;
use App\Models\MapUserFYPolicy;
use Illuminate\Support\Facades\Mail;
use App\Mail\LifeEventAcknowledgement;
use App\Models\User;
use DateTime;
use DateInterval;
use Illuminate\Support\Facades\Storage;


class DependantController extends Controller
{
    public function loadDependants()
    {
        $relation_Table = config('constant.relationshipDep_type_jTable');
        $dependants = Dependant::where('is_active', config('constant.$_YES'))
            ->where('user_id_fk', Auth::user()->id)
            ->where('relationship_type', '<>', config('constant.$_RLTN_SELF'))
            ->get()->toArray();

        $activeFinancialYear = DB::table('financial_years')
            ->where('is_active', 1)
            ->first();

        $mapUserFYPolicyData = MapUserFYPolicy::where('user_id_fk', '=', Auth::user()->id)->with(['fyPolicy']);
        $is_submitted = $mapUserFYPolicyData->whereRelation('fyPolicy.financialYears', 'id', $activeFinancialYear->id)
            ->where('is_submitted', 1)->get()->toArray();
        if (count($is_submitted) > 0) {
            $is_submitted = TRUE;
        } else {
            $is_submitted = FALSE;
        }

        session(['is_submitted' => $is_submitted]);

        $relation_Table = implode(',', $relation_Table);

        $dependants = Dependant::where('is_active', config('constant.$_YES'))
            ->where('user_id_fk', Auth::user()->id)
            ->where('relationship_type', '<>', config('constant.$_RLTN_SELF'))
            ->where('approval_status', 1)
            ->orderBy('dependent_name', 'ASC')
            ->get();

        return view('dependant', compact('relation_Table', 'dependants'));
    }

    public function getAvailableRelations(Request $request)
    {
        // all relation possible in case fo Life Event
        if ($request->has('isLEList') && $request->isLEList) {
            $relation_Table = config('constant.relationshipLE_type_jTable');
        } else {
            //$relation_Table = config('constant.relationship_type');
            $relation_Table = config('constant.relationshipDep_type_jTable');
            unset($relation_Table[config('constant.$_RLTN_SELF')]);
        }
        $relationshipNonDuplicate_Table = config('constant.relationshipNonDuplicate_types');
        // check logged in user's existing dependants
        $dependants = Dependant::where('is_active', config('constant.$_YES'))
            ->where('user_id_fk', Auth::user()->id)
            ->get()->toArray();

        if (count($dependants)) {
            foreach ($dependants as $depItem) {
                if (array_key_exists($depItem['relationship_type'], $relationshipNonDuplicate_Table)) {
                    unset($relation_Table[$depItem['relationship_type']]);
                }
            }
        }
        unset($dependants);
        return implode(',', array_keys($relation_Table));
    }


    public function listDependants(Request $request)
    {
        $jTableResult = array('Result' => 'OK');        // default optimistic approach
        $dependants = Dependant::where('is_active', config('constant.$_YES'))
            //->where('is_deceased',config('constant.$_NO'))
            ->where('user_id_fk', Auth::user()->id)
            ->where('relationship_type', '<>', config('constant.$_RLTN_SELF'))
            ->orderBy('dependent_name', 'ASC');
        if ($request->has('isLEList') && $request->isLEList) {
            $dependants->where('approval_status', config('constant.$_APPR_STATUS_APPROVED'));
        }
        $dependants = $dependants->get();
        if (!count($dependants)) {       // no/zero dependant count
            $jTableResult['Result'] = 'ERROR';
            $jTableResult['Message'] = 'No dependants found for you! Please secure your loved ones asap.';
        } else {        // dependants found 
            $jTableResult['Result'] = "OK";
            $jTableResult['Records'] = $dependants->toArray();
        }
        return json_encode($jTableResult);
    }

    public function create(Request $request)
    {
        if ($request->isMethod('post')) {
            $input = $request->all();
            $rules = [
                'dependent_name' => 'required|between:3,32|',
                'dob' => 'required|date:d-m-Y',
                'gender' => 'required|min:0|max:3',
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
                $dependant = new Dependant();
                $dependant->external_id = null;
                $dependant->dependent_name = $input['dependent_name'];
                $dependant->user_id_fk = Auth::user()->id;
                foreach (config('constant.dependant_code') as $code => $rltnArr) {
                    if (in_array($input['relationship_type'], $rltnArr)) {
                        $dependant->dependent_code = $code;
                        break;
                    }
                }
                $dependant->dob = date('Y-m-d', strtotime($input['dob']));
                $dependant->gender = $input['gender'];
                $dependant->nominee_percentage = $input['nominee_percentage'];
                $dependant->relationship_type = $input['relationship_type'];
                $dependant->approval_status = config('constant.$_APPR_STATUS_APPROVED');
                $dependant->is_active = config('constant.$_YES');
                $dependant->is_deceased = 0;
                $dependant->created_by = Auth::user()->id;
                $dependant->modified_by = Auth::user()->id;
                $dependant->save();

                return redirect()->back()->with('success', 'Dependent Added Successfully');
            } else {
                return redirect()->back()->with('error', 'All Fields are required');
            }
        } else {
            return redirect()->back()->with('error', 'Only POST method is allowed');
        }
        return json_encode($jTableResult);
    }


    public function findDependent(Request $request)
    {

        $dependentId = $request->input('id');

        $dependent = Dependant::find($dependentId);

        if (!$dependent) {
            return response()->json(['error' => 'Dependent not found'], 404);
        }

        return response()->json(['dependent' => $dependent]);
    }
    public function update(Request $request)
    {

        if ($request->isMethod('post')) {
            $input = $request->all();
            $rules = [
                'edit_dependent_name' => 'required|between:3,32|',
                'edit_dob' => 'required|date_format:d-m-Y',
                'edit_nominee_percentage' => 'required|numeric|digits_between:0,100',
            ];


            $dependants = Dependant::where('is_active', config('constant.$_YES'))
                ->where('user_id_fk', Auth::user()->id)
                ->where('relationship_type', '<>', config('constant.$_RLTN_SELF'))
                ->orderBy('dependent_name', 'ASC')
                ->get();


            $dependant = Dependant::find($input['id']);
            $totalNomineePercentage = ($dependants->sum('nominee_percentage') -  $dependant->nominee_percentage + $input['edit_nominee_percentage']);
            if ($totalNomineePercentage > 100) {
                return redirect()->back()->with('error', 'Total nominee percentage exceeds 100%');
            }
            $dependant->id = $input['id'];
            $dependant->dependent_name = $input['edit_dependent_name'];

            $dependant->dob = date('Y-m-d', strtotime($input['edit_dob']));
            $dependant->nominee_percentage = $input['edit_nominee_percentage'];
            $dependant->modified_by = Auth::user()->id;
            $dependant->save();
            return redirect()->back()->with('success', 'Dependent Edit Successfully');
        } else {
            return redirect()->back()->with('error', 'Only post method is allowed.');
        }
    }

    public function updateLE(Request $request)
    {
        //Return result to jTable
        $jTableResult = array();
        if ($request->isMethod('post')) {
            $input = $request->all();
            // dd($input);
            $rules = [
                'dependent_name' => 'required|between:3,32|',
                'dob' => 'required|date_format:d-m-Y',
                'nominee_percentage' => 'required|numeric|digits_between:0,100',
            ];

            $validator = Validator::make($input, $rules, $messages = [
                'required' => 'The :attribute field is required.',
                'numeric' => 'The :attribute field should be numbers only',
                'boolen' => 'The :attribute field should be Yes or No.',
                'date_format' => 'The :attribute should follow date format of YYYY-MM-DD',
            ]);

            if (!$validator->fails()) {
                if ($input['relationship_type'] == 6) {
                    $inputDob = new DateTime($input['doe']);
                    $oneMonthLater = (new DateTime())->sub(new DateInterval('P1M'));
                    if ($inputDob < $oneMonthLater) {
                        $error = 'Date of Event not greater than one month.';
                        $jTableResult['Result'] = "ERROR";
                        //dd($validator->errors()->messages());
                        $jTableResult['Message'] = '<div class="fs-12"><ul>' . $error . '</ul></div>';
                        return json_encode($jTableResult);
                    }
                    $relation = "Spouse";
                } else {
                    $inputDob = new DateTime($input['dob']);
                    $oneMonthLater = (new DateTime())->sub(new DateInterval('P1M'));
                    if ($inputDob < $oneMonthLater) {
                        $error = 'Date of Birth not greater than one month for new born baby.';
                        $jTableResult['Result'] = "ERROR";
                        //dd($validator->errors()->messages());
                        $jTableResult['Message'] = '<div class="fs-12"><ul>' . $error . '</ul></div>';
                        return json_encode($jTableResult);
                    }
                    $relation = "Child";
                }
                $dependant = Dependant::find($input['id']);
                $dependant->id = $input['id'];
                $dependant->dependent_name = $input['dependent_name'];
                // // find dependant code from relationship type
                // foreach (config('constant.dependent_code') as $code => $rltnArr) {
                //     //print_r([$code,$rltnArr,$input['relationship_type']]);
                //     if(in_array($input['relationship_type'], $rltnArr))
                //     {
                //         $dependant->dependent_code = $code;
                //         break;
                //     }
                // }
                $dependant->dob = date('Y-m-d', strtotime($input['dob']));
                //$dependant->gender = $input['gender'];
                $dependant->nominee_percentage = $input['nominee_percentage'];
                //$dependant->relationship_type = $input['relationship_type'];
                //$dependant->approval_status = 1;      
                //$dependant->is_deceased = $input['is_deceased'];                
                $dependant->is_active = config('constant.$_YES');
                $dependant->modified_by = Auth::user()->id;
                $dependant->save();

                $jTableResult['Result'] = "OK";
                $jTableResult['Record'] = $dependant->toArray();
            } else {
                $error = '';
                $jTableResult['Result'] = "ERROR";
                foreach (array_values($validator->errors()->messages()) as $item) {
                    $error .= '<li>' . $item[0] . '</li>';
                }
                //dd($validator->errors()->messages());
                $jTableResult['Message'] = '<div class="fs-12"><ul>' . $error . '</ul></div>';
            }
        } else {
            // throw error

            $jTableResult['Result'] = "ERROR";
            $jTableResult['Message'] = 0;
        }
        return json_encode($jTableResult);
    }

    public function updateAfterSubmission(Request $request)
    {
        //Return result to jTable
        $jTableResult = array();
        if ($request->isMethod('post')) {
            $input = $request->all();
            $rules = [
                'dependent_name' => 'required|between:3,32|',

            ];

            $validator = Validator::make($input, $rules, $messages = [
                'required' => 'The :attribute field is required.',
            ]);

            if (!$validator->fails()) {
                $dependant = Dependant::find($input['id']);
                $dependant->id = $input['id'];
                $dependant->dependent_name = $input['dependent_name'];
                // // find dependant code from relationship type
                // foreach (config('constant.dependent_code') as $code => $rltnArr) {
                //     //print_r([$code,$rltnArr,$input['relationship_type']]);
                //     if(in_array($input['relationship_type'], $rltnArr))
                //     {
                //         $dependant->dependent_code = $code;
                //         break;
                //     }
                // }
                //$dependant->gender = $input['gender'];
                //$dependant->relationship_type = $input['relationship_type'];
                //$dependant->approval_status = 1;      
                //$dependant->is_deceased = $input['is_deceased'];                
                $dependant->is_active = config('constant.$_YES');
                $dependant->modified_by = Auth::user()->id;
                $dependant->save();

                $jTableResult['Result'] = "OK";
                $jTableResult['Record'] = $dependant->toArray();
            } else {
                $error = '';
                $jTableResult['Result'] = "ERROR";
                foreach (array_values($validator->errors()->messages()) as $item) {
                    $error .= '<li>' . $item[0] . '</li>';
                }
                //dd($validator->errors()->messages());
                $jTableResult['Message'] = '<div class="fs-12"><ul>' . $error . '</ul></div>';
            }
        } else {
            // throw error

            $jTableResult['Result'] = "ERROR";
            $jTableResult['Message'] = 0;
        }
        return json_encode($jTableResult);
    }



    public function delete(Request $request)
    {
        //Return result to jTable
        $jTableResult = array();
        if ($request->isMethod('post')) {
            try {
                $input = $request->all();

                $dependant = Dependant::find($input['id']);
                $dependant->is_active = false;
                $dependant->modified_by = Auth::user()->id;
                $dependant->save();

                $jTableResult['Result'] = "OK";
                $jTableResult['Record'] = $dependant->toArray();
            } catch (Exception $e) {
                $jTableResult['Result'] = "ERROR";
                $jTableResult['Message'] = json_encode($e->getMessage());
            }
        } else {
            // throw error

            $jTableResult['Result'] = "ERROR";
            $jTableResult['Message'] = 0;
        }
        return json_encode($jTableResult);
    }

    /**
     * Ajax function to load list of any Life Events entries present
     * **/
    public function listLifeEvents(Request $request)
    {
        $jTableResult = array('Result' => 'OK');        // default optimistic approach
        $dependants = Dependant::where('is_active', config('constant.$_YES'))
            ->where('user_id_fk', Auth::user()->id)
            ->where('is_life_event', true)
            // ->where('approval_status', 3)
            ->get();
        if (!count($dependants)) {       // no/zero dependant count
            $jTableResult['Result'] = 'OK';
            $jTableResult['Message'] = 'No life event dependants found for you';
        } else {        // dependants found 
            $jTableResult['Result'] = "OK";
            $jTableResult['Records'] = $dependants->toArray();
        }
        return json_encode($jTableResult);
    }

    /**
     * Function to load Life Event Page
     * **/
    public function loaddependantsLE()
    {
        $relationLE_Table = config('constant.relationshipLE_type_jTable');
        $relation_Table = config('constant.relationshipDep_type_jTable');

        $dependants = Dependant::where('is_active', config('constant.$_YES'))
            ->where('user_id_fk', Auth::user()->id)
            ->whereIn('relationship_type', [config('constant.$_RLTN_SPOUSE')])
            ->get()->toArray();

        if (count($dependants)) {
            foreach ($dependants as $depItem) {
                if (array_key_exists($depItem['relationship_type'], $relationLE_Table)) {
                    unset($relationLE_Table[$depItem['relationship_type']]);
                }
            }
        }
        $activeFinancialYear = DB::table('financial_years')
            ->where('is_active', 1)
            ->first();

        $mapUserFYPolicyData = MapUserFYPolicy::where('user_id_fk', '=', Auth::user()->id)->with(['fyPolicy']);
        $is_submitted = $mapUserFYPolicyData->whereRelation('fyPolicy.financialYears', 'id', $activeFinancialYear->id)
            ->where('is_submitted', 1)->get()->toArray();
        if (count($is_submitted) > 0) {
            $is_submitted = TRUE;
        } else {
            $is_submitted = FALSE;
        }

        session(['is_submitted' => $is_submitted]);


        $dependants = Dependant::where('is_active', config('constant.$_YES'))
            ->where('user_id_fk', Auth::user()->id)
            ->where('relationship_type', '<>', config('constant.$_RLTN_SELF'))
            ->where('is_life_event', 1)
            ->orderBy('dependent_name', 'ASC')
            ->get();

        $relationLE_Table = implode(',', $relationLE_Table);
        $relation_Table = implode(',', $relation_Table);

        return view('dependantLE', compact('relationLE_Table', 'relation_Table', 'dependants'));
    }

    public function createLE(Request $request)
    {

        if ($request->isMethod('post')) {
            $input = $request->all();
            $rules = [
                'dependent_name' => 'required|between:3,32|',
                'dob' => 'required',
                'doe' => 'required_if:relationship_type,==,' . config('constant.$_RLTN_SPOUSE'),
                'gender' => 'required|min:0|max:3',
                'nominee_percentage' => 'required|numeric|digits_between:0,100',
                'relationship_type'  => 'required|min:1|max:12',
                'upload_document' => 'required|file', // Adjust max file size as needed
            ];

            $validator = Validator::make($input, $rules, $messages = [
                'required' => 'The :attribute field is required.',
                'required_if' => 'The :attribute field is required for selected "Relation Type"',
                'numeric' => 'The :attribute field should be numbers only',
                'boolen' => 'The :attribute field should be Yes or No.',
                'date_format' => 'The :attribute should follow date format of YYYY-MM-DD',
                'min' => 'The :attribute field value is invalid',
                'max' => 'The :attribute field value is invalid',
                'file' => 'The :attribute must be a file.',

            ]);

            if (!$validator->fails()) {
                if ($input['relationship_type'] == 6) {
                    $inputDob = new DateTime($input['doe']);
                    $oneMonthLater = (new DateTime())->sub(new DateInterval('P1M'));
                    if ($inputDob < $oneMonthLater) {
                        $error = 'Date of Event not greater than one month.';

                        return redirect()->back()->with('error', $error);
                    }
                    $relation = "Spouse";
                } else {
                    $inputDob = new DateTime($input['dob']);
                    $oneMonthLater = (new DateTime())->sub(new DateInterval('P1M'));
                    if ($inputDob < $oneMonthLater) {
                        $error = 'Date of Birth not greater than one month for new born baby.';

                        return redirect()->back()->with('error', $error);
                    }
                    $relation = "Child";
                }

                $uploadedFile = $request->file('upload_document');
                $fileName = time() . '_' . $uploadedFile->getClientOriginalName();
                $temporaryFilePath = 'uploads/' . $fileName; // Path relative to the public directory
                Storage::disk('public')->put($temporaryFilePath, file_get_contents($uploadedFile));
    

                $dependant = new Dependant();
                $dependant->external_id = null;
                $dependant->dependent_name = $input['dependent_name'];
                $dependant->user_id_fk = Auth::user()->id;
                // find dependant code from relationship type
                foreach (config('constant.dependant_code') as $code => $rltnArr) {
                    if (in_array($input['relationship_type'], $rltnArr)) {
                        $dependant->dependent_code = $code;
                        break;
                    }
                }
                $dependant->dob = date('Y-m-d', strtotime($input['dob']));
                if ($input['relationship_type'] == config('constant.$_RLTN_SPOUSE')) {
                    $dependant->doe = $input['doe'];    // date of event
                }
                $dependant->gender = $input['gender'];
                $dependant->nominee_percentage = $input['nominee_percentage'];
                $dependant->relationship_type = $input['relationship_type'];
                $dependant->approval_status = config('constant.$_APPR_STATUS_INPROGRESS');
                $dependant->is_active = config('constant.$_YES');
                $dependant->is_deceased = config('constant.$_NO');
                $dependant->is_life_event = 1;
                $dependant->supported_document = $temporaryFilePath;
                $dependant->created_by = Auth::user()->id;
                $dependant->modified_by = Auth::user()->id;
                //dd($dependant);    
                $dependant->save();



                $user = User::where('id', Auth::user()->id)->first();

                Mail::to(Auth::user()->email)->send(new LifeEventAcknowledgement($user, $relation));

                return redirect()->back()->with('success', 'Dependent Added Successfully');
            } else {
                $error = '';
                $jTableResult['Result'] = "ERROR";
                foreach (array_values($validator->errors()->messages()) as $item) {
                    $error .= '<li>' . $item[0] . '</li>';
                }
                return redirect()->back()->with('error', $error);
            }
        } else {
            return redirect()->back()->with('error', "error");
        }
    }

    public function getRelationshipTypes(Request $request)
    {
        $relation_Table = config('constant.relationship_type');
        unset($relation_Table[config('constant.$_RLTN_SELF')]);
        //dd($relation_Table);
        //$relation_Table = config('constant.relationship_type_jTable');
        //dd($relation_Table);
        // check logged in user's existing dependants
        $dependants = Dependant::where('is_active', config('constant.$_YES'))
            ->where('user_id_fk', Auth::user()->id)
            //->whereIn('relationship_type',[config('constant.$_RLTN_SPOUSE')])
            ->get()->toArray();

        if (count($dependants)) {
            foreach ($dependants as $depItem) {
                if (
                    array_key_exists($depItem['relationship_type'], $relation_Table) &&
                    (!in_array($depItem['relationship_type'], [config('constant.$_RLTN_SON'), config('constant.$_RLTN_DAUGHTER')]))
                ) {
                    unset($relation_Table[$depItem['relationship_type']]);
                }
            }
        }
        foreach (([-1 => '--Select--'] + $relation_Table) as $k => $v) {
            $generatedOptions[$k] = "{Value:'" . $k . "',DisplayText:'" . $v . "'}";
        }
        $jTableResult['Result'] = "OK";
        $jTableResult['Options'] = '[' . json_encode($generatedOptions) . ']';
        //return view('dependentLE',compact('relationLE_Table'));
        //dd($jTableResult['Options'] );
        return json_encode($jTableResult);
    }

    public function getGenderByRelation(Request $request)
    {
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

    public function getNominationAllocation(Request $request)
    {
        $editId = $request->editId;
        $data = [];
        $response = array('msg' => '', 'status' => TRUE);
        $dep = Dependant::where('is_active', config('constant.$_YES'))
            ->where('user_id_fk', Auth::user()->id)
            ->select(['id', 'nominee_percentage', 'dependent_name'])
            ->get()->toArray();
        $str = [];
        $nomSum = 0;
        $idFound = false;
        if (count($dep)) {
            foreach ($dep as $depRow) {
                if ($depRow['id'] == $editId) {
                    $nomSum += $request->nomAlloc;
                    $idFound = true;
                } else {
                    $nomSum += $depRow['nominee_percentage'];
                    $data[$depRow['id']] = ['name' => $depRow['dependent_name'], 'nomAllocated' => $depRow['nominee_percentage']];
                }
            }
            $totalNom = (int)($nomSum + ($idFound ? 0 : $request->nomAlloc));
            if ($totalNom > 100) {
                $response['status'] = FALSE;
                $str = [];
                foreach ($data as $depRow) {
                    $str[] = $depRow['name'] . '[' . (int)$depRow['nomAllocated']  . '%]';
                }
                $response['msg'] = implode(', ', $str);
            }
        }
        return json_encode($response);
    }
}
