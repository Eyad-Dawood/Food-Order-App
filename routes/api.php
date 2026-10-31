<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Enums\Auth\UserRole;
use App\Http\Controllers\Api\CustomerAddressController;
use App\Http\Controllers\Api\RestaurantController;

Route::prefix('auth')->group(function () {
    Route::post('register/customer', [AuthController::class, 'registerCustomer']);
    Route::post('register/restaurant_manager', [AuthController::class, 'registerRestaurant_Manager']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    
    Route::middleware('auth:api')->group(function () {
        Route::get('me', [AuthController::class, 'me']);      
        Route::post('logout', [AuthController::class, 'logout']);
    });
});

Route::middleware('auth:api')->group(function () {

    // Admin only routes
    Route::middleware(['role:'.UserRole::ADMIN->value])->group(function () {
        
        Route::post('register/admin', [AuthController::class, 'registerAdmin']); 

        // Restaurants Activation
        Route::prefix('restaurants')->group(function () {
            Route::put('/{id}/deactivate', [RestaurantController::class, 'deActivate']);
            Route::put('/{id}/activate', [RestaurantController::class, 'activate']);
        });
    });
    
    // Restaurant Manager only routes
    Route::middleware(['role:'.UserRole::Restaurant_Manager->value])->group(function () {
        Route::prefix('restaurants')->group(function () {
            Route::get('/', [RestaurantController::class, 'index']);
            Route::post('/', [RestaurantController::class, 'store']);
            Route::get('/{id}', [RestaurantController::class, 'show']);
            Route::put('/{id}', [RestaurantController::class, 'update']);
            Route::delete('/{id}', [RestaurantController::class, 'destroy']);
        });
    });

    // Customer only routes
    Route::middleware(['role:'.UserRole::CUSTOMER->value])->group(function () {
        Route::prefix('addresses')->group(function () {
            Route::get('/', [CustomerAddressController::class, 'index']);
            Route::post('/', [CustomerAddressController::class, 'store']);
            Route::put('/{id}', [CustomerAddressController::class, 'update']);
            Route::delete('/{id}', [CustomerAddressController::class, 'destroy']);
        });
    });

});