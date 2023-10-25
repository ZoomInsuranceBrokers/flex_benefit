<?php

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
//});

// DEPENDENT CONTROLLER ROUTES
//Route::controller(DependentController::class)->group(function () {
   // die('here123');
    Route::post('/dependents/create', [DependentController::class, 'create']);
    Route::post('/dependents/update', [DependentController::class, 'update']);
    Route::post('/dependents/delete', [DependentController::class, 'delete']);    
    Route::post('/dependents/list', [DependentController::class, 'list']);
    Route::post('/dependents/saveLifeEvent', [DependentController::class, 'createLE']);
    Route::get('/dependents/listLE', [DependentController::class, 'listLifeEvents']);
    Route::get('/dependents/life-events', [DependentController::class, 'loadDependentsLE']);
    Route::get('/dependents', function () { return view('dependent'); });
//});

Route::get('/enrollment/getPolicybySubCategory/', [EnrollmentController::class, 'yg4']);
Route::get('/enrollment', [EnrollmentController::class, 'home']);

Route::get('/', [UserController::class, 'home']);




