<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VehicleMaintenance;
use App\Models\MaintenanceRequest;
use Illuminate\Support\Facades\Auth;

class MaintenanceController extends Controller
{
    public function index()
    {
        $maintenanceHistory = VehicleMaintenance::with('vehicle', 'performed_by_user')->get();
        // Show all requests that are not finally approved/rejected
        $pendingRequests = MaintenanceRequest::with('vehicle', 'appliedBy', 'directorReviewer', 'committeeReviewer', 'finalDirectorApprover')
            ->whereIn('status', [
                'pending',
                'under_committee_review',
                'committee_approved',
                'committee_rejected',
                'final_approved',
                'final_rejected'
            ])
            ->get();
        if (Auth::User()->role->role_name === 'committe-user') {
            return view('dashboard.committe-user.maintenance', compact('maintenanceHistory', 'pendingRequests'));
        } else {
            return view('dashboard.shared.maintenance-history', compact('maintenanceHistory', 'pendingRequests'));
        }
    }

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
            if ($maintenanceRequest->director_status !== 'pending' && $maintenanceRequest->committee_status === 'pending') {
                $maintenanceRequest->committee_status = 'approved';
                $maintenanceRequest->committee_reviewed_by = $user->id;
            }
        }
        $maintenanceRequest->save();
        return redirect()->back()->with('success', 'Maintenance request approved.');
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

}
