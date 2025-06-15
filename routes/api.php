<?php

use App\Http\Controllers\Api\RequestsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LogBookController;


Route::get('/vehicle/{vehicle_id}/location', [LocationController::class, 'latest']);
Route::middleware('throttle:60,1')->post('/vehicle/location', [LocationController::class, 'store']);
Route::post('/login',[AuthController::class,'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    // Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/maintenance/request',[RequestsController::class,'MaintenanceRequest']);
    Route::post('/fuel/request',[RequestsController::class,'fuelRequest']);
    Route::post('/logbook',[LogBookController::class,'store']);
});
