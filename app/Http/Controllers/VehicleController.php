<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Requests;


use App\Models\VehicleType;
use App\Models\Vehicle;
use App\Models\Location;
use App\Models\VehicleMaintenance;
use App\Models\Branch;
use App\Models\User;
use App\Models\VehicleAssignment;

class VehicleController extends Controller
{
    //
    public function vehicles()
    {
        $branches = Branch::all();
        $vehicleTypes = VehicleType::all()->unique();
        $availableVehicles = Vehicle::where('status', 'Available')->get();
        $users = User::all();
        $regIds = Vehicle::select('RegID', 'Vehicle_Type')->limit(6)->get();
        return view('dashboard.shared.vehiclesinfo', compact('regIds', 'branches', 'vehicleTypes', 'users', 'availableVehicles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'RegID' => 'required|unique:vehicles,RegID',
            'Model' => 'required|string',
            'Fuel_type' => 'required|string',
            'Vehicle_Type' => 'required|string',
            'branch_id' => 'nullable|exists:branches,id',
            'Average_mileage' => 'nullable|numeric',
        ]);

        Vehicle::create($validated);

        return redirect()->back()->with('success', 'Vehicle added successfully!');
    }

    public function assignVehicle(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,RegID',
            'user_id' => 'required|exists:users,id',
        ]);

        VehicleAssignment::create([
            'vehicle_id' => $request->vehicle_id,
            'user_id' => $request->user_id,
            'assigned_date' => now(),
            'returned_date' => null, // Not yet returned
        ]);

        return redirect()->back()->with('success', 'Vehicle assigned successfully.');
    }


    public function details($regid)
    {
        $vehicle = Vehicle::with(['latestAssignment.user', 'maintenanceRecords', 'branch'])->where('RegID', $regid)->firstOrFail();
        // Determine the vehicle status
        if ($vehicle->latestAssignment) {
            $status = 'assigned';
            $assignmentDetails = $vehicle->latestAssignment;
        } elseif ($vehicle->isUnderMaintenance()) {
            $status = 'under maintenance';
            $assignmentDetails = null;
        } else {
            $status = 'available';
            $assignmentDetails = null;
        }
        $branches = Branch::all();
        $vehicleTypes = VehicleType::all()->unique();
        return view('dashboard.shared.vehicledetails', [
            'vehicle' => $vehicle,
            'status' => $status,
            'assignment' => $assignmentDetails,
            'branches' => $branches,
            'vehicleTypes' => $vehicleTypes,
        ]);
    }

    public function update(Request $request, $RegID)
    {
        $vehicle = Vehicle::findOrFail($RegID);
        $request->validate([
            'Model' => 'required|string',
            'Fuel_type' => 'required|string',
            'Vehicle_Type' => 'required|string',
            'branch_id' => 'nullable|exists:branches,id',
            'Average_mileage' => 'nullable|numeric',
        ]);

        $vehicle->update($request->only([
            'Model',
            'Fuel_type',
            'Vehicle_Type',
            'branch_id',
            'Average_mileage'
        ]));
        
        return redirect()->back()->with('success', 'Vehicle updated successfully.');
    }



    public function tracking()
    {
        $vehicles = Vehicle::with('latestLocation')->has('locations')->get();

        return view('dashboard.shared.vehicle-tracking', compact('vehicles'));
    }



}
