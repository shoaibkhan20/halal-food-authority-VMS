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
            case 'vehicle-supervisor':
                return redirect()->route('vehicle-supervisor.dashboard');
            case 'divisional-user':
                return redirect()->route('divisional-user.dashboard');
            case 'district-user':
                return redirect()->route('district-user.dashboard');
            case 'committe-user':
                return redirect()->route('committe-user.dashboard');
            default:
                abort(403, 'Unauthorized');
        }
    }

    public function dashboardStatistics()
    {
        // Summary counts
        $totalFuelRequests = FuelRequest::where('status','pending')->count();
        $pendingMaintenanceRequests = MaintenanceRequest::where('status', 'pending')->count();
        $totalVehicles = Vehicle::count();
        $totalApplications = $totalFuelRequests + $pendingMaintenanceRequests;

        // Application status breakdown
        $statuses = ['pending', 'under_committee_review', 'committee_approved', 'committee_rejected', 'final_approved', 'final_rejected'];
        $applicationStatusData = [];
        foreach ($statuses as $status) {
            $applicationStatusData[$status] = MaintenanceRequest::where('status', $status)->count();
        }

        // Last 5 months (labels and cost data)
        $months = collect(range(0, 4))->map(fn($i) => now()->subMonths($i)->startOfMonth())->reverse();

        $labels = $months->map(fn($date) => $date->format('M'))->toArray();

        $costData = $months->map(function ($monthStart) {
            $start = $monthStart->copy();
            $end = $monthStart->copy()->endOfMonth();

            return VehicleMaintenance::whereBetween('created_at', [$start, $end])
                ->sum('actual_cost');
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