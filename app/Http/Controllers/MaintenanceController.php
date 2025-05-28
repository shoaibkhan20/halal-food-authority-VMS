<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\VehicleMaintenance;
use App\Models\MaintenanceRequest;
use App\Models\VehicleSupervisorReport;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
class MaintenanceController extends Controller
{

    public function index(Request $request)
    {
        $search = $request->query('search');
        // Base query for maintenance history
        $maintenanceHistoryQuery = VehicleMaintenance::with('vehicle', 'supervisorReports');
        if ($search) {
            $maintenanceHistoryQuery->where(function ($query) use ($search) {
                $query->whereHas('vehicle', function ($q) use ($search) {
                    $q->where('RegID', 'like', '%' . $search . '%');
                })
                    ->orWhereDate('created_at', $search)
                    ->orWhereDate('updated_at', $search);
            });
        }
        $maintenanceHistory = $maintenanceHistoryQuery->get();
        // Base query for pending requests
        $pendingRequestsQuery = MaintenanceRequest::with('vehicle.branch', 'appliedBy', 'directorReviewer', 'committeeReviewer', 'finalDirectorApprover')
            ->whereIn('status', [
                'pending',
                'under_committee_review',
                'committee_approved',
                'committee_rejected',
                'final_approved',
                'final_rejected'
            ]);
        if ($search) {
            $pendingRequestsQuery->where(function ($query) use ($search) {
                $query->whereHas('vehicle', function ($q) use ($search) {
                    $q->where('RegID', 'like', '%' . $search . '%');
                })->orWhere('issue', 'like', '%' . $search . '%')
                    ->orWhereHas('appliedBy', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    });
            });
        }
        $pendingRequests = $pendingRequestsQuery->get();
        // Return the appropriate view
        if (Auth::user()->role->role_name === 'committe-user') {
            return view('dashboard.committe-user.maintenance', compact('maintenanceHistory', 'pendingRequests', 'search'));
        } else {
            return view('dashboard.shared.maintenance-history', compact('maintenanceHistory', 'pendingRequests', 'search'));
        }
    }

    // request approve & rejection [committee & director]
    public function approve(Request $request, $id)
    {
        $maintenanceRequest = MaintenanceRequest::findOrFail($id);
        $user = Auth::user();
        $role = $user->role->role_name ?? '';
        if ($role === 'director-admin') {
            if ($maintenanceRequest->director_status === 'pending') {
                // Initial director approval
                $maintenanceRequest->director_status = 'approved';
                $maintenanceRequest->director_reviewed_by = $user->id;
            } elseif ($maintenanceRequest->committee_status === 'approved' && $maintenanceRequest->director_final_status === 'pending') {
                // Final director approval after committee approval
                $maintenanceRequest->director_final_status = 'approved';
                $maintenanceRequest->director_final_approved_by = $user->id;
            }
        } elseif ($role === 'committe-user') {
            // Committee approval, only proceed if director already reviewed
            if ($maintenanceRequest->committee_status === 'pending') {
                $maintenanceRequest->committee_status = 'approved';
                $maintenanceRequest->committee_reviewed_by = $user->id;
            }
        }
        $maintenanceRequest->save();
        // After save, check if status became final_approved
        if ($maintenanceRequest->status === 'final_approved') {
            $alreadyExists = VehicleMaintenance::where('maintenance_request_id', $maintenanceRequest->id)->exists();
            if (!$alreadyExists) {
                $status = 'in_progress';
                if (!in_array($status, VehicleMaintenance::allowedStatuses())) {
                    throw new \InvalidArgumentException("Invalid status: $status");
                }
                VehicleMaintenance::create([
                    'maintenance_request_id' => $maintenanceRequest->id,
                    'vehicle_id' => $maintenanceRequest->vehicle_id,
                    'status' => $status,
                    'started_at' => now(),
                    'actual_cost' => $maintenanceRequest->estimated_cost,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

            }
        }
        return redirect()->back()->with('success', 'Maintenance request approved.');
    }
    public function assign(Request $request, $id)
    {
        $maintenanceRequest = MaintenanceRequest::findOrFail($id);
        $user = Auth::user();
        if ($maintenanceRequest->status === 'pending') {
            $maintenanceRequest->status = 'under_committee_review';
            $maintenanceRequest->director_status = 'waiting_for_committee';
            $maintenanceRequest->director_reviewed_by = $user->id;
            $maintenanceRequest->director_rejection_message = $request->input('rejection_message');
        }
        $maintenanceRequest->save();
        return redirect()->back()->with('success', 'Maintenance request assigned to committee');
    }

    public function reject(Request $request, $id)
    {
        $maintenanceRequest = MaintenanceRequest::findOrFail($id);
        $user = Auth::user();
        $request->validate([
            'rejection_message' => 'required|string|max:1000',
        ]);
        $role = $user->role->role_name ?? '';
        if ($role === 'director-admin') {
            if ($maintenanceRequest->status === 'pending') {
                $maintenanceRequest->director_status = 'rejected';
                $maintenanceRequest->director_reviewed_by = $user->id;
                $maintenanceRequest->director_rejection_message = $request->input('rejection_message');
            } elseif ($maintenanceRequest->status === 'committee_approved') {
                $maintenanceRequest->director_final_status = 'rejected';
                $maintenanceRequest->director_final_approved_by = $user->id;
                $maintenanceRequest->director_final_rejection_message = $request->input('rejection_message');
            }
        } elseif ($role === 'committe-user') {
            if ($maintenanceRequest->status === 'under_committee_review') {
                $maintenanceRequest->committee_status = 'rejected';
                $maintenanceRequest->committee_reviewed_by = $user->id;
                $maintenanceRequest->committee_rejection_message = $request->input('rejection_message');
            }
        }
        $maintenanceRequest->save();
        return redirect()->back()->with('success', 'Maintenance request rejected.');
    }





    // request displaying and completion of report [vehicle-supervisor]
    public function vehicleMaintenance(Request $request)
    {
        $search = $request->query('search');
        $users = User::all();
        // Get all final approved maintenance requests
        // Get all maintenance history
        $maintenanceHistoryQuery = VehicleMaintenance::with('vehicle', 'supervisorReports');
        if ($search) {
            $maintenanceHistoryQuery->where(function ($query) use ($search) {
                $query->whereHas('vehicle', function ($q) use ($search) {
                    $q->where('RegID', 'like', '%' . $search . '%');
                })
                    ->orWhereDate('created_at', $search)
                    ->orWhereDate('updated_at', $search);
            });
        }
        $maintenanceHistory = $maintenanceHistoryQuery->get();
        return view('dashboard.vehicle-supervisor.maintenance', compact('maintenanceHistory', 'search', 'users'));
    }


    


}
