<?php

use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VehicleController;

// ====================
// 🔓 Public Routes
// ====================

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});
// ====================
// 🔐 Authenticated Routes
// ====================

Route::middleware('auth')->group(function () {

    // 🏠 Role Redirecting Dashboard Controller
    Route::get('/', [DashboardController::class, 'index'])->name('home');
    Route::get('/home', [DashboardController::class, 'index']);
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // 🔁 Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Super Admin
    Route::prefix('super-admin')->middleware('role:super-admin')->group(function () {
        Route::get('/', [VehicleController::class,'vehicles'])->name('dashboard');
        Route::get('/vehicle/{regid}',[VehicleController::class,'details'])->name('vehicle.details');
        Route::get('/tracking',[VehicleController::class,'tracking'])->name('vehicle.tracking');
        Route::get('/maintenance', fn() => view('dashboard.shared.maintenance-history'))->name('vehicle.maintenance');
        Route::get('/role-management', [UserController::class,'index'])->name('users.role-management');
        Route::post('/users/store', [UserController::class,'store'])->name('users.store');   
        Route::put('/users/{user}', [UserController::class,'update'])->name('users.update');   
        Route::delete('/delete-user/{user}', [UserController::class,'destroy'])->name('users.delete');
        Route::get('/reporting',[ReportController::class,'index'])->name('reports');
        Route::get('/reporting/vehicle-status',[ReportController::class,'vehicleStatus'])->name('report.vehicle-status');
        Route::get('/reporting/maintenance-report',[ReportController::class,'MaintenanceReport'])->name('report.maintenance');
    });

    //  Director Admin
    Route::prefix('director-admin')->middleware('role:director-admin')->group(function () {
        Route::get('/', fn() => view('director-admin.dashboard'))->name('director-admin.dashboard');
    });

    // 
    //  Divisional User
    Route::prefix('divisional-user')->middleware('role:divisional-user')->group(function () {
        Route::get('/', fn(): View => view('divisional-user.dashboard'))->name('divisional-user.dashboard');
    });

    //  District User
    Route::prefix('district-user')->middleware('role:district-user')->group(function () {
        Route::get('/', fn() => view('district-user.dashboard'))->name('district-user.dashboard');
    });

    //  Committe User
    Route::prefix('committe-user')->middleware('role:committe-user')->group(function () {
        Route::get('/', fn() => view('committe-user.dashboard'))->name('committe-user.dashboard');
    });
});
