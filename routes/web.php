<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VehicleController;

// ====================
// Public Routes
// ====================

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});

// ====================
// Authenticated Routes
// ====================
Route::middleware('auth')->group(function () {
    // 🏠 Role Redirecting Dashboard Controller
    Route::get('/', [DashboardController::class, 'index'])->name('home');
    Route::get('/home', [DashboardController::class, 'index']);
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    //shared routes
    Route::middleware('role:super-admin,director-admin,committe-user')->group(function () {
        Route::get('/vehicles', [VehicleController::class, 'vehicles'])->name('vehicles.info');
        Route::get('/vehicle/{regid}', [VehicleController::class, 'details'])->name('vehicle.details');
        Route::get('/vehicles/search', [VehicleController::class, 'searchVehicle'])->name('vehicles.search');
        Route::get('/tracking', [VehicleController::class, 'tracking'])->name('vehicle.tracking');
        Route::get('/maintenance', [MaintenanceController::class, 'index'])->name('vehicle.maintenance');
        Route::get('/reporting', [ReportController::class, 'index'])->name('reports');
        Route::get('/reporting/vehicle-status', [ReportController::class, 'vehicleStatus'])->name('report.vehicle-status');
        Route::get('/reporting/maintenance-report', [ReportController::class, 'MaintenanceReport'])->name('report.maintenance');
    });
    // committee and director shared routes
    Route::middleware('role:director-admin,committe-user')->group(function () {
        Route::post('/maintenance/approve/{id}', [MaintenanceController::class, 'approve'])->name('maintenance.approve');
        Route::post('/maintenance/reject/{id}', [MaintenanceController::class, 'reject'])->name('maintenance.reject');
    });
    // Super Admin
    Route::prefix('super-admin')->middleware('role:super-admin')->group(function () {
        Route::get('/', [DashboardController::class, 'dashboardStatistics'])->name('super-admin.dashboard');
        Route::post('/vehicles', [VehicleController::class, 'store'])->name('vehicles.store');
        Route::put('/vehicles/{regid}', [VehicleController::class, 'update'])->name('vehicles.update');
        Route::post('/vehicles/assign', [VehicleController::class, 'assignVehicle'])->name('vehicle.assign');
        Route::get('/role-management', [UserController::class, 'index'])->name('users.role-management');
        Route::post('/users/store', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/delete-user/{user}', [UserController::class, 'destroy'])->name('users.delete');
    });
    //  Director Admin
    Route::prefix('director-admin')->middleware('role:director-admin')->group(function () {
        Route::get('/', [DashboardController::class, 'dashboardStatistics'])->name('director-admin.dashboard');
    });
    //  Committe User
    Route::prefix('committe-user')->middleware('role:committe-user')->group(function () {
        Route::get('/', [DashboardController::class, 'dashboardStatistics'])->name('committe-user.dashboard');
    });
    //  Divisional User
    Route::prefix('divisional-user')->middleware('role:divisional-user')->group(function () {
        Route::get('/', [DashboardController::class, 'dashboardStatistics'])->name('divisional-user.dashboard');
    });
    //  District User
    Route::prefix('district-user')->middleware('role:district-user')->group(function () {
        Route::get('/', [DashboardController::class, 'dashboardStatistics'])->name('district-user.dashboard');

    });
});
