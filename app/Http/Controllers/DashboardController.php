<?php

namespace App\Http\Controllers;
use Illuminate\Routing\Controller; // Make sure you're importing the correct Controller class
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\FuelRequest;
use App\Models\MaintenanceRequest;
use App\Models\VehicleMaintenance;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        switch ($user->role->role_name) {
            case 'super-admin':
                return redirect()->route('super-admin.dashboard');
            case 'director-admin':
                return redirect()->route('director-admin.dashboard');
            case 'divisional-user':
                return redirect()->route('director-admin.dashboard');
            case 'district-user':
                return redirect()->route('director-admin.dashboard');
            case 'committe-user':
                return redirect()->route('director-admin.dashboard');
            default:
                abort(403, 'Unauthorized');
        }
    }


    function dashboardStatistics()
    {
        // Summary counts
        $totalFuelRequests = FuelRequest::count();
        $pendingMaintenanceRequests = MaintenanceRequest::where('status', 'pending')->count();
        $totalVehicles = Vehicle::count();
        $totalApplications = $totalFuelRequests + $pendingMaintenanceRequests;
    
        // Application status counts for doughnut chart
        $applicationStatusData = [
            'approved' => MaintenanceRequest::where('status', 'approved')->count(),
            'pending' => MaintenanceRequest::where('status', 'pending')->count(),
            'rejected' => MaintenanceRequest::where('status', 'rejected')->count(),
        ];
    
        // Maintenance cost over last 5 months using Eloquent
        $months = collect(range(0, 4))->map(function ($i) {
            return Carbon::now()->subMonths($i)->startOfMonth();
        })->reverse();
    
        $labels = $months->map(fn($date) => $date->format('M'))->toArray();
    
        $costData = $months->map(function ($monthStart) {
            return VehicleMaintenance::whereBetween('created_at', [
                $monthStart,
                $monthStart->copy()->endOfMonth()
            ])->sum('actual_cost');
        })->toArray();
    
        return view('dashboard.dashboard', compact(
            'totalApplications',
            'totalVehicles',
            'applicationStatusData',
            'labels',
            'costData'
        ));
    }
    
}