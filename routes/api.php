<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\SkuController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::group(['prefix' => '/v1'], function() {
    Route::group(['middleware' => 'auth:sanctum'], function() {
        Route::apiResource('/customers',CustomerController::class);
        Route::apiResource('/skus',SkuController::class);
        Route::apiResource('/purchase-orders',PurchaseOrderController::class);
    });
    Route::post('/login',[AuthController::class, 'login']);
});
