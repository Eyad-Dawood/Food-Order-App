<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Enums\Auth\UserRole;
use App\Http\Controllers\Api\CustomerAddressController;

Route::prefix('auth')->group(function () {
    // Public routes
    Route::post('register/customer', [AuthController::class, 'registerCustomer']);
    Route::post('register/restaurant_manager', [AuthController::class, 'registerRestaurant_Manager']);
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
    
    //Restaurant Manager only routes
    

    //Customer only routes
    Route::middleware(['role:'.UserRole::CUSTOMER->value])->group(function () {

        //address routes
        Route::prefix('addresses')->group(function () {
            Route::get('/', [CustomerAddressController::class, 'index']);
            Route::post('/', [CustomerAddressController::class, 'store']);
            Route::put('/{id}', [CustomerAddressController::class, 'update']);
            Route::delete('/{id}', [CustomerAddressController::class, 'destroy']);
        });
    });



    });
});