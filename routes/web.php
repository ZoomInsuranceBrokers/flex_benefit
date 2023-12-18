<?php

use App\Http\Controllers\ClaimController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DependentController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// CONTACT CONTROLLER ROUTES
//Route::controller(ContactController::class)->group(function () {
//die('here');
// Route::get('/orders/{id}', 'show');
Route::post('/user/login', [UserController::class, 'login']);
Route::post('/user/resetpassword', [UserController::class, 'resetPassword']);
Route::get('/user/logout', [UserController::class, 'logout']);

Route::get('forgot-password', [UserController::class,'showForm'])->name('password.request');
Route::post('forgot-password', [UserController::class,'sendResetLinkEmail'])->name('password.email');
Route::get('reset-password/{token}', [UserController::class,'showResetForm'])->name('password.reset');
Route::post('reset-password', [UserController::class,'reset'])->name('password.update');
//});

// DEPENDENT CONTROLLER ROUTES
Route::group(['middleware' => 'auth'], function() {
    // lots of routes that require auth middleware
    
    Route::get('password-reset', [UserController::class,'passworReset'])->name('previous.form.route');
    Route::post('/password/authupdate', [UserController::class, 'updatePassword'])->name('password.authupdate');
    Route::post('/dependents/create', [DependentController::class, 'create']);
    Route::post('/dependents/update', [DependentController::class, 'update']);
    Route::post('/dependents/delete', [DependentController::class, 'delete']);
    Route::post('/dependents/getRelationshipTypes', [DependentController::class, 'getRelationshipTypes']);
    Route::get('/dependents/nominationCount', [DependentController::class, 'getNominationAllocation']);
    Route::post('/dependents/list', [DependentController::class, 'list']);
    Route::post('/dependents/saveLifeEvent', [DependentController::class, 'createLE']);
    Route::get('/dependents/listLE', [DependentController::class, 'listLifeEvents']);
    Route::get('/dependents/life-events', [DependentController::class, 'loadDependentsLE']);
    Route::get('/dependents', function () { return view('dependent');});

    Route::post('/enrollment/save', [EnrollmentController::class, 'saveEnrollment']);
    Route::post('/enrollment/savePV', [EnrollmentController::class, 'saveEnrollmentPV']);
    Route::post('/enrollment/finalSubmit', [EnrollmentController::class, 'submitEnrollment']);
    Route::post('/enrollment/resetCategory', [EnrollmentController::class, 'resetCategory']);
    Route::get('/enrollment/summary', [EnrollmentController::class, 'loadSummary']);
    Route::get('/enrollment/summaryDownload', [EnrollmentController::class, 'downloadSummary']);
    Route::get('/enrollment/getPolicybySubCategory', [EnrollmentController::class, 'getInsuranceListBySubCategory']);
    Route::get('/enrollment', [EnrollmentController::class, 'home']);

    // Route::post('/claim/create', [DependentController::class, 'create']);
    // Route::post('/claim/update', [DependentController::class, 'update']);
    Route::any('/claim/loadHospital', [ClaimController::class, 'loadNetworkHospital']);
    Route::post('/claim/searchHospital', [ClaimController::class, 'searchNetworkHospital']);


//});

// Route::post('/enrollment/save', [EnrollmentController::class, 'saveEnrollment']);
// Route::post('/enrollment/savePV', [EnrollmentController::class, 'saveEnrollmentPV']);
// Route::get('/enrollment/summary', [EnrollmentController::class, 'loadSummary']);
// Route::get('/enrollment/summaryDownload', [EnrollmentController::class, 'downloadSummary']);
// Route::get('/enrollment/getPolicybySubCategory', [EnrollmentController::class, 'getInsuranceListBySubCategory']);
// Route::get('/enrollment', [EnrollmentController::class, 'home']);

// Route::post('/claim/create', [DependentController::class, 'create']);
// Route::post('/claim/update', [DependentController::class, 'update']);
    Route::any('/claim/loadHospital/{tpa}', [ClaimController::class, 'loadNetworkHospital']);
    Route::post('/claim/searchHospital', [ClaimController::class, 'searchNetworkHospital']);
    Route::get('/claim/initiate', [ClaimController::class, 'loadClaimIntimation']);
    Route::post('/claim/initiate', [ClaimController::class, 'saveClaimIntimation']);


    Route::get('/logout', [UserController::class, 'logout']);
    Route::get('/user/ecard', [UserController::class, 'downloadEcard']);
});



Route::get('/', [UserController::class, 'home'])->name('home');
