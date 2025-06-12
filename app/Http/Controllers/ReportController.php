<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\VehicleMaintenance;
use App\Models\VehicleSupervisorReport;
use Illuminate\Support\Carbon;
// use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;
use TCPDF;
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
                'Status' => $vehicle->latestAssignment ? 'Assigned' : 'Availible',
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
        $query = VehicleMaintenance::with([
            'vehicle.branch',
            'supervisorReports.supervisor'
        ])->whereNotNull('completed_at');
        if ($regId) {
            $query->whereHas('vehicle', function ($q) use ($regId) {
                $q->where('RegID', 'like', '%' . $regId . '%');
            });
        }
        if ($date) {
            $query->whereDate('completed_at', $date);
        }
        $records = $query->orderByDesc('completed_at')->get();
        // Group by vehicle and summarize
        $groupedRecords = $records->groupBy('vehicle_id')->map(function ($group) {
            $vehicle = $group->first()->vehicle;
            return [
                'vehicle_id' => $vehicle->RegID,
                'division' => optional($vehicle->branch)->division,
                'district' => optional($vehicle->branch)->district,
                'total_cost' => $group->sum('actual_cost'),
                'vehicle' => $vehicle,
                'maintenance_history' => $group,
            ];
        })->values();
        return view('dashboard.shared.maintenance-report', compact('groupedRecords'));
    }


    public function generateVehicleReport(Request $request)
    {
        try {
            $vehicleId = $request->input('vehicle_id');

            if (!$vehicleId) {
                abort(400, 'Vehicle ID is required.');
            }

            $vehicle = Vehicle::with('branch')->where('RegID', $vehicleId)->withTrashed()->firstOrFail();

            $maintenanceRecords = VehicleMaintenance::with('supervisorReports.supervisor')
                ->where('vehicle_id', $vehicleId)
                ->whereNotNull('completed_at')
                ->orderByDesc('completed_at')
                ->get();

            $totalCost = $maintenanceRecords->sum('actual_cost');

            // Generate logo image as base64
            $logoPath = public_path('images/logo.png');
            $base64logo = '';
            if (file_exists($logoPath)) {
                $base64logo = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
            }

            // Generate the HTML content manually or using a view:
            $html = view('pdf.maintenance-report-tcpdf', [
                'vehicle' => $vehicle,
                'records' => $maintenanceRecords,
                'totalCost' => $totalCost,
                'base64logo' => $base64logo
            ])->render();

            // Initialize TCPDF
            $pdf = new \TCPDF();
            $pdf->SetCreator('Laravel TCPDF');
            $pdf->SetAuthor('VMS');
            $pdf->SetTitle("Maintenance Report - $vehicleId");
            $pdf->SetMargins(10, 1, 10);
            $pdf->AddPage();

            // Write the HTML content
            $pdf->writeHTML($html, true, false, true, false, '');

            // Return as response
            return response($pdf->Output("maintenance-report-{$vehicleId}.pdf", 'S'))
                ->header('Content-Type', 'application/pdf');
        } catch (\Throwable $e) {
            \Log::error('TCPDF generation failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'An error occurred while generating the PDF.',
                'details' => $e->getMessage()
            ], 500);
        }
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
