<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// define controllers
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UniqueUserController;

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

// route api/v1
Route::prefix('v1')->group(function(){

    // route prefix api/v1/auth
    Route::prefix('auth')->group(function () {
        // public routes
        Route::post('login', [AuthController::class,'login']);
        Route::post('register', [AuthController::class,'register']);
        // socialite
        Route::get('/{driver}', [AuthController::class,'redirectToProvider']);
        Route::get('/{driver}/callback', [AuthController::class,'handleProviderCallback']);
    });

    // middleware sanctum only for authenticated
    Route::middleware('auth:sanctum')->group(function(){
        // route for crud
        Route::resource('unique_user', UniqueUserController::class);
    });
    

});
