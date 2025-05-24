<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\VehicleMaintenance;
class ReportController extends Controller
{
    //
    public function index()
    {
        $vehicles = Vehicle::with(['currentAssignment.user', 'branch'])
            ->get()
            ->map(function ($vehicle) {
                return [
                    'RegID' => $vehicle->RegID,
                    'Model' => $vehicle->Model,
                    'AssignedTo' => $vehicle->latestAssignment->user->name ?? 'Unassigned',
                    'Status' => $vehicle->status,
                    'Location' => $vehicle->branch->location ?? 'Unknown',
                ];
            });

        return view('dashboard.shared.reportings', compact('vehicles'));
    }



    public function vehicleStatus()
    {
        $vehicles = Vehicle::with([
            'assignments' => function ($query) {
                $query->whereNull('returned_date')
                    ->with('user');
            },
            'maintenanceRecords' => function ($query) {
                $query->where('status', 'in_progress');
            }
        ])->get()->map(function ($vehicle) {
            return [
                'RegID' => $vehicle->RegID,
                'Model' => $vehicle->Model,
                'AssignedTo' => optional($vehicle->assignments->first()->user ?? null)->name,
                'status' => $vehicle->status,
                'Region' => $vehicle->branch->location,
                'under_maintenance' => $vehicle->maintenanceRecords->isNotEmpty(),
            ];
        });
        return view("dashboard.shared.vehicle-status-report", compact("vehicles"));
    }

    public function MaintenanceReport()
    {
        $records = VehicleMaintenance::with([
            'vehicle.branch' // eager load vehicle and its branch
        ])
            ->whereNotNull('completed_at') // only show completed maintenance
            ->orderByDesc('completed_at')
            ->get()
            ->map(function ($record) {
                return [
                    'RegID' => $record->vehicle->RegID ?? 'N/A',
                    'Date' => optional($record->completed_at)->format('Y-m-d'),
                    'Cost' => number_format($record->actual_cost, 2),
                    'Items' => $record->maintenance_notes,
                    'Location' => $record->vehicle->branch->location ?? 'N/A',
                ];
            });
        return view("dashboard.shared.maintenance-report", compact("records"));
    }
}
