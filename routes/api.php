<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;


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

// Using api to clear cache, cookies, and sessions
Route::get('/clear', function () {
    Artisan::call('optimize:clear');
    return Artisan::output();
});

// Version 1
Route::prefix('v1')->group(function () {
    //API route for register new user
    Route::post('/register', [AuthController::class, 'register']);

    //API route for login user
    Route::post('/login', [AuthController::class, 'login']);

    Route::prefix('otp')->group(function () {
        Route::post('/send', [AuthController::class, 'sendOtp']);
        Route::post('/verify', [AuthController::class, 'verifyOtp']);
    });

    Route::prefix('forgot-password')->group(function () {
        Route::post('/', [AuthController::class, 'forgotPassword']);
        Route::post('/verify', [AuthController::class, 'verifyForgotPassword']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    });

    //Protecting Routes
    Route::group(['middleware' => ['auth:sanctum']], function () {

        Route::prefix('profile')->group(function () {
            Route::get('/', [AuthController::class, 'profile']);
            Route::post('/change-password', [AuthController::class, 'changePassword']);
            Route::post('/update', [UserController::class, 'updateProfile']);
        });

        Route::get('/user/delete/{id}', [UserController::class, 'delete']);
        Route::get('/user/{id}', [UserController::class, 'findUserById']);
        Route::get('/users', [UserController::class, 'getAllUsers']);
        Route::post('/user/update/{id}', [UserController::class, 'updateUser']);

        // API route for logout user
        Route::get('/logout', [AuthController::class, 'logout']);
    });
});
