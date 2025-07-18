<?php

use App\Http\Controllers\Api\LogBookController;
use App\Http\Controllers\FuelRequestsController;
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
    Route::get('/', [DashboardController::class, 'index'])->name('home');
    Route::get('/home', [DashboardController::class, 'index']);
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/vehicle/{regid}', [VehicleController::class, 'details'])->name('vehicle.details');

    // Route::get('/vehicles/search', [VehicleController::class, 'searchVehicle'])->name('vehicles.search');

    //vehicle info
    Route::middleware('role:super-admin,director-admin,committe-user,vehicle-supervisor')->group(function () {
        Route::get('/vehicles', [VehicleController::class, 'vehicles'])->name('vehicles.info');
    });
    //vehicle tracking
    Route::middleware('role:super-admin,director-admin,committe-user,divisional-user')->group(function () {
        Route::get('/tracking', [VehicleController::class, 'tracking'])->name('vehicle.tracking');
    });
    // review maintenance
    Route::middleware('role:super-admin,director-admin,committe-user,divisional-user')->group(function () {
        Route::get('/maintenance', [MaintenanceController::class, 'index'])->name('vehicle.maintenance');
    });
    Route::middleware('role:super-admin,director-admin,committe-user,divisional-user')->group(function () {
        Route::get('/reporting', [ReportController::class, 'index'])->name('reports');
        Route::get('/reporting/vehicle-status', [ReportController::class, 'vehicleStatus'])->name('report.vehicle-status');
        Route::get('/reporting/maintenance-report', [ReportController::class, 'MaintenanceReport'])->name('report.maintenance');
        Route::post('/maintenance-report/pdf', [ReportController::class, 'generateVehicleReport'])->name('maintenance.report.pdf');

    });
    // maintenance approve / reject
    Route::middleware('role:director-admin,committe-user')->group(function () {
        Route::post('/maintenance/approve/{id}', [MaintenanceController::class, 'approve'])->name('maintenance.approve');
        Route::post('/maintenance/assign/{id}', [MaintenanceController::class, 'assign'])->name('maintenance.assign');
        Route::post('/maintenance/reject/{id}', [MaintenanceController::class, 'reject'])->name('maintenance.reject');
    });

    // Super Admin
    Route::prefix('super-admin')->middleware('role:super-admin')->group(function () {
        Route::get('/', [DashboardController::class, 'dashboardStatistics'])->name('super-admin.dashboard');
        Route::post('/vehicles', [VehicleController::class, 'store'])->name('vehicles.store');
        Route::put('/vehicles/{regid}', [VehicleController::class, 'update'])->name('vehicles.update');
        Route::post('/vehicles/assign', [VehicleController::class, 'assignVehicle'])->name('vehicle.assign');
        Route::PUT('/vehicles/deallocate/{regid}', [VehicleController::class, 'deallocateVehicle'])->name('vehicle.deallocate');
        Route::get('/role-management', [UserController::class, 'index'])->name('users.role-management');
        Route::post('/users/store', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/delete-user/{user}', [UserController::class, 'destroy'])->name('users.delete');
        Route::delete('/delete-vehicle/{id}', [VehicleController::class, 'destroy'])->name('vehicles.destroy');
        Route::get('/logbooks', [VehicleController::class, 'showLogbooks'])->name('logbooks');
    });
    //  Director Admin
    Route::prefix('director-admin')->middleware('role:director-admin')->group(function () {
        Route::get('/', [DashboardController::class, 'dashboardStatistics'])->name('director-admin.dashboard');
        Route::get('/fuel-request', [FuelRequestsController::class, 'index'])->name('fuel-requests');
        Route::post('/fuel-request/approve/{id}', [FuelRequestsController::class, 'approveFuelRequest'])->name('fuel-requests.approve');
        Route::post('/fuel-request/reject/{id}', [FuelRequestsController::class, 'rejectFuelRequest'])->name('fuel-requests.reject');
    });
    //  Committe User
    Route::prefix('committe-user')->middleware('role:committe-user')->group(function () {
        Route::get('/', [DashboardController::class, 'dashboardStatistics'])->name('committe-user.dashboard');
    });
    // vehicle supervisor
    Route::prefix('vehicle-supervisor')->middleware('role:vehicle-supervisor')->group(function () {
        Route::get('/', [DashboardController::class, 'dashboardStatistics'])->name('vehicle-supervisor.dashboard');
        Route::get('/maintenance', [MaintenanceController::class, 'vehicleMaintenance'])->name('vehicle-supervisor.maintenance');
        Route::post('/maintenance/report/{id}', [ReportController::class, 'createSupervisorReport'])->name('vehicle-maintenance.complete');
    });
    //  Divisional User
    Route::prefix('divisional-user')->middleware('role:divisional-user')->group(function () {
        Route::get('/', [DashboardController::class, 'dashboardStatistics'])->name('divisional-user.dashboard');
        Route::get('/vehicles', [VehicleController::class, 'vehiclesByDivision'])->name('divisional-user.vehicles');
        Route::get('/vehicles/tracking', [VehicleController::class, 'divisionVehiclesTracking'])->name('divisional-user.vehicles.tracking');
    });
    //  District User
    Route::prefix('district-user')->middleware('role:district-user')->group(function () {
        Route::get('/', [DashboardController::class, 'dashboardStatistics'])->name('district-user.dashboard');
        Route::get('/vehicles', [VehicleController::class, 'vehiclesByDistrict'])->name('district-user.vehicles');
        Route::get('/vehicles/tracking', [VehicleController::class, 'districtVehiclesTracking'])->name('district-user.vehicles.tracking');
    });
});
