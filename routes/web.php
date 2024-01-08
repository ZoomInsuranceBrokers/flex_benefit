<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClaimController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DependantController;
use App\Http\Controllers\EnrollmentController;

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

Route::get('forgot-password', [UserController::class, 'showForm'])->name('password.request');
Route::post('forgot-password', [UserController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('reset-password/{token}', [UserController::class, 'showResetForm'])->name('password.reset');
Route::post('reset-password', [UserController::class, 'reset'])->name('password.update');
//});

// DEPENDENT CONTROLLER ROUTES
Route::group(['middleware' => 'auth'], function () {
    // lots of routes that require auth middleware

    Route::get('password-reset', [UserController::class, 'passworReset'])->name('previous.form.route');
    Route::post('/password/authupdate', [UserController::class, 'updatePassword'])->name('password.authupdate');
    Route::post('/dependants/create', [DependantController::class, 'create']);
    Route::post('/dependants/update', [DependantController::class, 'update']);
    Route::post('/dependants/delete', [DependantController::class, 'delete']);
    Route::post('/dependants/getRelationshipTypes', [DependantController::class, 'getRelationshipTypes']);
    Route::get('/dependants/nominationCount', [DependantController::class, 'getNominationAllocation']);
    Route::post('/dependants/list', [DependantController::class, 'listDependants']);
    Route::post('/dependants/saveLifeEvent', [DependantController::class, 'createLE']);
    Route::get('/dependants/listLE', [DependantController::class, 'listLifeEvents']);
    Route::get('/dependants/life-events', [DependantController::class, 'loaddependantsLE']);
    Route::get('/dependants/getRelations', [DependantController::class, 'getAvailableRelations']);
    Route::get('/dependants', [DependantController::class, 'loaddependants']);

    Route::post('/enrollment/updatePoints', [EnrollmentController::class, 'getPoints']);
    Route::post('/enrollment/save', [EnrollmentController::class, 'saveEnrollment']);
    Route::post('/enrollment/savePV', [EnrollmentController::class, 'saveEnrollmentPV']);
    Route::post('/enrollment/finalSubmit', [EnrollmentController::class, 'submitEnrollment']);
    Route::post('/enrollment/resetCategory', [EnrollmentController::class, 'resetCategory']);
    Route::get('/enrollment/summary', [EnrollmentController::class, 'loadSummary']);
    Route::get('/enrollment/summaryDownload', [EnrollmentController::class, 'downloadSummary']);
    Route::get('/enrollment/getPolicybySubCategory', [EnrollmentController::class, 'getInsuranceListBySubCategory']);
    Route::get('/enrollment', [EnrollmentController::class, 'home']);

    // Route::post('/claim/create', [DependantController::class, 'create']);
    // Route::post('/claim/update', [DependantController::class, 'update']);
    Route::any('/claim/loadHospital', [ClaimController::class, 'loadNetworkHospital']);
    Route::post('/claim/searchHospital', [ClaimController::class, 'searchNetworkHospital']);

    Route::get('/claim/initiate', [ClaimController::class, 'loadClaimIntimation']);
    Route::post('/claim/initiate', [ClaimController::class, 'saveClaimIntimation']);
    Route::get('/claim/track', [ClaimController::class, 'trackClaimStatus']);



    Route::get('/logout', [UserController::class, 'logout']);
    Route::get('/user/ecard', [UserController::class, 'downloadEcard']);
    Route::get('/user/summary', [UserController::class, 'viewSummary']);
});



Route::get('/', [UserController::class, 'home'])->name('home');
