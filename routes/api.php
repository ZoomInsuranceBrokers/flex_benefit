<?php

use App\Models\User;
// use App\Models\Contact;
use App\Models\Currency;
use Illuminate\Http\Request;
// use Illuminate\Foundation\Auth\User;
use App\Models\CountryCurrency;
use App\Models\InsurancePolicy;
use App\Models\MapUserFYPolicy;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


// Route::post('/dependents/create', 'create');
// Route::post('/dependents/update', 'update');
// Route::post('/dependents/delete', 'delete');


Route::post('/users', [ApiController::class, 'getAllUsers']);
Route::post('/salesforce-data', [ApiController::class, 'getSalesforceData']);

Route::get('/currency/create', function(Request $request){
    echo $contact = Currency::create([
        'name' => 'Indian Rupee','symbol' => '&#8377;','short_name' => 'INR',
        'description' => 'Currency of India','is_active' => true,
        'created_by' => 1,'modified_by' => 1
    ]);
});
Route::get('/country-currency/create', function(Request $request){
    echo $contact = CountryCurrency::create([
        'name' => 'India','short_name' => 'IND','currency_id_fk' => 1,'is_active' => true
    ]);
});
Route::get('/user/defaultpolicymapping/', function(Request $request){
    if(!MapUserFYPolicy::all()->count()){
        $users = User::where('is_active',1)->get()->toArray();
        $mapFYpolicyData = DB::table('map_financial_year_policy as mfyp')
            ->select('mfyp.id')
            ->leftJoin('financial_years as fy' ,'fy.id', '=', 'mfyp.fy_id_fk')
            ->leftJoin('insurance_policy as ip' ,'ip.id', '=', 'mfyp.ins_policy_id_fk')
            ->where('mfyp.is_active', '=', true)
            ->where('fy.is_active', '=', true)
            ->where('ip.is_active', '=', true)
            ->where('ip.is_base_plan', true)
            ->orWhere('ip.is_default_selection', true)
            ->get()->toArray();

        foreach($users as $user) {
            foreach($mapFYpolicyData as $mfypRow) {
                $data[] = [
                    'user_id_fk' => $user['id'],
                    'fypolicy_id_fk' => $mfypRow->id,
                    'selected_dependent' => NULL,
                    'encoded_summary' => NULL,
                    'points_used' => 0,
                    'created_by' => 0,
                    'modified_by' => 0,
                ];
            }
        }
        MapUserFYPolicy::insert($data);
        echo 'Data Inserted';
    } else {
        echo 'Map User FY Policy table already has data';
    }

});
Route::get('/user/create/', function(Request $request){
    echo $contact = User::create([
        'sfdc_id' => '2334efe3dad3rzszz',
        'fname' => 'Aakash',
        'mname' => '',
        'lname' => 'K',
        'employee_id' => '1q2w32s2swe4r123',
        'email' => 'aakash@zoom_insurance.com',
        'grade' => 'C3A',
        'hire_date' => '2000/09/01 11:23:23',
        'address' => 'Tis Hazari 34/2 Sector 6 Karol Bagh',
        'country_id_fk' => 1,
        'mobile_number' => '+91983433211',
        'salutation' => 2,
        'title' => 2,
        'suffix' => 'Dpty Mgr',
        'gender' => 1,
        'nominee_percentage' => 50,
        'is_active' => true,
        'password' => bcrypt('1234567890'),
        'created_by' => 1,
        'modified_by' => 1
    ]);
});

Route::get('/account/', function(Request $request){

});


