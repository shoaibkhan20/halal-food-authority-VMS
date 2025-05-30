<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\VehicleMaintenance;
use App\Models\VehicleSupervisorReport;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    //
    public function index(Request $request)
    {
        $search = $request->query('search');

        $vehiclesQuery = Vehicle::with(['currentAssignment.user', 'branch']);

        if ($search) {
            $vehiclesQuery->where(function ($query) use ($search) {
                $query->where('RegID', 'like', '%' . $search . '%')
                    ->orWhere('Model', 'like', '%' . $search . '%');
            });
        }

        $vehicles = $vehiclesQuery->get()->map(function ($vehicle) {
            return [
                'RegID' => $vehicle->RegID,
                'Model' => $vehicle->Model,
                'AssignedTo' => $vehicle->latestAssignment->user->name ?? 'Unassigned',
                'Status' => $vehicle->status,
                'Location' => $vehicle->branch->district ?? 'Unknown',
            ];
        });

        return view('dashboard.shared.reportings', compact('vehicles'));
    }



    public function vehicleStatus(Request $request)
    {
        $search = $request->query('search');

        $vehiclesQuery = Vehicle::with(['assignments.user', 'maintenanceRecords', 'branch']);

        if ($search) {
            $vehiclesQuery->where(function ($query) use ($search) {
                $query->where('RegID', 'like', '%' . $search . '%')
                    ->orWhere('Model', 'like', '%' . $search . '%');
            });
        }

        $vehicles = $vehiclesQuery->get()->map(function ($vehicle) {
            // Get the current assignment where returned_date is null
            $currentAssignment = $vehicle->assignments->firstWhere('returned_date', null);
            return [
                'RegID' => $vehicle->RegID,
                'Model' => $vehicle->Model,
                'AssignedTo' => $currentAssignment?->user->name ?? null,
                'status' => $vehicle->status,
                'Region' => $vehicle->branch->district ?? 'Unknown',
                'under_maintenance' => $vehicle->maintenanceRecords->where('status', 'in_progress')->isNotEmpty(),
            ];
        });

        return view("dashboard.shared.vehicle-status-report", compact("vehicles"));
    }


    public function MaintenanceReport(Request $request)
    {
        $regId = $request->query('reg_id');
        $date = $request->query('date');
        $query = VehicleMaintenance::with(['vehicle.branch'])
            ->whereNotNull('completed_at');
        if ($regId) {
            $query->whereHas('vehicle', function ($q) use ($regId) {
                $q->where('RegID', 'like', '%' . $regId . '%');
            });
        }
        if ($date) {
            // Ensure we filter on exact date (no time)
            $query->whereDate('completed_at', $date);
        }
        $records = $query->orderByDesc('completed_at')
            ->get()
            ->map(function ($record) {
                return [
                    'RegID' => $record->vehicle->RegID ?? 'N/A',
                    'Date' => $record->completed_at ? Carbon::parse($record->completed_at)->format('Y-m-d') : 'N/A',
                    'Cost' => number_format($record->actual_cost, 2),
                    'Items' => $record->supervisorReports()->first()->maintenance_notes ?? 'N/A',
                    'PerformedBy' => $record->supervisorReports->first()->supervisor->name ?? 'N/A',
                    'Location' => $record->vehicle->branch->district ?? 'N/A',
                ];
            });

        return view("dashboard.shared.maintenance-report", compact("records"));
    }


    public function createSupervisorReport(Request $request, $id)
    {
        // Validate input
        $validated = $request->validate([
            'maintenance_notes' => 'nullable|string|max:1000',
            'performed_by' => 'nullable|sring|max:255',
            'attachment' => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
        ]);

        // Find the maintenance record
        $maintenance = VehicleMaintenance::findOrFail($id);

        // Handle file upload if present
        $filePath = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filePath = $file->store('supervisor_reports', 'public');
        }

        // Update maintenance record
        $maintenance->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        // Create supervisor report
        VehicleSupervisorReport::create([
            'vehicle_maintenance_id' => $maintenance->id,
            'generated_by' => auth()->id(), // Assuming the logged-in user is the supervisor
            'maintenance_notes' => $validated['maintenance_notes'],
            'mechanic_info' => $validated['performed_by'],
            'report_file_path' => $filePath ?? '',
        ]);

        return redirect()->back()->with('success', 'Maintenance marked as completed and report saved.');
    }

}
