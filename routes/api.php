<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LocationController;

Route::get('/vehicle/{vehicle_id}/location', [LocationController::class, 'latest']);
Route::middleware('throttle:60,1')->post('/vehicle/location', [LocationController::class, 'store']);
