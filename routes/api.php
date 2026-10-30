<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Enums\Auth\UserRole;

Route::prefix('auth')->group(function () {
    Route::post('register/customer', [AuthController::class, 'registerCustomer']);
    Route::post('register/vendor', [AuthController::class, 'registerVendor']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    
Route::middleware('auth:api')->group(function () {

    // registered users (no permission check)
    Route::get('me', [AuthController::class, 'me']);      
    Route::post('logout', [AuthController::class, 'logout']);
    
    //Admin only routes
    Route::middleware(['role:'.UserRole::ADMIN->value])->group(function () {
        Route::post('register/admin', [AuthController::class, 'registerAdmin']);
    });
    
    //Vendor only routes
    

    //Customer only routes

    });
});