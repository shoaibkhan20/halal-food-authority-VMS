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
use App\Models\Logbook;
use Illuminate\Support\Facades\Log;
class VehicleController extends Controller
{

    public function vehicles(Request $request)
    {
        $search = $request->input('search');
        $regIds = Vehicle::select('RegID', 'Vehicle_Type')
            ->when($search, function ($query, $search) {
                return $query->where('RegID', 'LIKE', '%' . $search . '%');
            }, function ($query) {
                return $query->limit(6);
            })
            ->get();
        $noResults = $search && $regIds->isEmpty();
        $branches = Branch::all();
        $vehicleTypes = VehicleType::all()->unique();
        $availableVehicles = Vehicle::where('status', 'Available')->get();
        $users = User::all();

        if (Auth::user()->role->role_name === 'district-user') {
            return view('dashboard.district-user.vehicles', compact(
                'regIds',
                'branches',
                'vehicleTypes',
                'users',
                'availableVehicles',
                'noResults'
            ));
        } else {
            return view('dashboard.shared.vehiclesinfo', compact(
                'regIds',
                'branches',
                'vehicleTypes',
                'users',
                'availableVehicles',
                'noResults'
            ));
        }

    }

    public function vehiclesByDistrict(Request $request)
    {
        $search = $request->input('search');
        // Get the current user's district via their branch
        $user = Auth::user();
        $userDistrict = optional($user->branch)->district;

        // Filter vehicles by the district of the current user
        $regIds = Vehicle::select('RegID', 'Vehicle_Type')
            ->whereHas('branch', function ($query) use ($userDistrict) {
                $query->where('district', $userDistrict);
            })
            ->when($search, function ($query, $search) {
                return $query->where('RegID', 'LIKE', '%' . $search . '%');
            }, function ($query) {
                return $query->limit(6);
            })
            ->get();

        $noResults = $search && $regIds->isEmpty();

        // You may still want to filter these by district too if relevant
        $branches = Branch::where('district', $userDistrict)->get();
        $vehicleTypes = VehicleType::all()->unique();
        $availableVehicles = Vehicle::where('status', 'Available')
            ->whereHas('branch', function ($query) use ($userDistrict) {
                $query->where('district', $userDistrict);
            })->get();
        $users = User::whereHas('branch', function ($query) use ($userDistrict) {
            $query->where('district', $userDistrict);
        })->get();

        return view('dashboard.district-user.vehicles', compact(
            'regIds',
            'branches',
            'vehicleTypes',
            'users',
            'availableVehicles',
            'noResults'
        ));
    }

    public function vehiclesByDivision(Request $request)
    {
        $search = $request->input('search');
        // Get the current user's division via their branch
        $user = Auth::user();
        $userDivision = optional($user->branch)->division;
        // Filter vehicles by the division of the current user
        $regIds = Vehicle::select('RegID', 'Vehicle_Type')
            ->whereHas('branch', function ($query) use ($userDivision) {
                $query->where('division', $userDivision);
            })
            ->when($search, function ($query, $search) {
                return $query->where('RegID', 'LIKE', '%' . $search . '%');
            }, function ($query) {
                return $query->limit(6);
            })
            ->get();
        $noResults = $search && $regIds->isEmpty();
        $branches = Branch::where('division', $userDivision)->get();
        $vehicleTypes = VehicleType::all()->unique();
        $availableVehicles = Vehicle::where('status', 'Available')
            ->whereHas('branch', function ($query) use ($userDivision) {
                $query->where('division', $userDivision);
            })->get();
        $users = User::whereHas('branch', function ($query) use ($userDivision) {
            $query->where('division', $userDivision);
        })->get();

        return view('dashboard.division-user.vehicles', compact(
            'regIds',
            'branches',
            'vehicleTypes',
            'users',
            'availableVehicles',
            'noResults'
        ));
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

        \DB::transaction(function () use ($request) {
            // Close any previous active assignments
            VehicleAssignment::where('vehicle_id', $request->vehicle_id)
                ->whereNull('returned_date')
                ->update(['returned_date' => now()]);

            // Create new assignment
            VehicleAssignment::create([
                'vehicle_id' => $request->vehicle_id,
                'user_id' => $request->user_id,
                'assigned_date' => now(),
                'returned_date' => null,
            ]);
            // Observer will update vehicle status automatically
        });

        return redirect()->back()->with('success', 'Vehicle reassigned successfully.');
    }



    public function deallocateVehicle(Request $request, $regid)
    {
        \DB::transaction(function () use ($regid) {
            $activeAssignments = VehicleAssignment::where('vehicle_id', $regid)
                ->whereNull('returned_date')
                ->get();
            foreach ($activeAssignments as $assignment) {
                $assignment->returned_date = now();
                $assignment->save(); // This triggers updated() event in observer
            }
        });
        return redirect()->back()->with('success', 'Vehicle deallocated successfully.');
    }

    public function details($regid)
    {
        $vehicle = Vehicle::with(['latestAssignment.user', 'maintenanceRecords', 'branch'])->where('RegID', $regid)->firstOrFail();
        // Determine the vehicle status
        if ($vehicle->latestAssignment) {
            $assignmentDetails = $vehicle->latestAssignment;
        } elseif ($vehicle->isUnderMaintenance()) {
            $assignmentDetails = null;
        } else {
            $assignmentDetails = null;
        }
        $branches = Branch::all();
        $vehicleTypes = VehicleType::all()->unique();
        return view('dashboard.shared.vehicledetails', [
            'vehicle' => $vehicle,
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


    public function tracking(Request $request)
    {
        // Start the query to get vehicles with their latest location
        $query = Vehicle::with('latestLocation');
        // If search query is provided, filter based on RegID
        if ($search = $request->input('search')) {
            $query->where('RegID', 'LIKE', '%' . $search . '%');
        }
        // Fetch the filtered vehicles
        $vehicles = $query->get();
        // Return the filtered results to the vie
        return view('dashboard.shared.vehicle-tracking', compact('vehicles'));
    }

    public function districtVehiclesTracking(Request $request)
    {
        $user = Auth::user();
        $userDistrict = optional($user->branch)->district;

        $query = Vehicle::with('latestLocation')
            ->whereHas('branch', function ($q) use ($userDistrict) {
                $q->where('district', $userDistrict);
            });
        if ($search = $request->input('search')) {
            $query->where('RegID', 'LIKE', '%' . $search . '%');
        }
        $vehicles = $query->get();
        return view('dashboard.district-user.tracking', compact('vehicles'));
    }


    public function divisionVehiclesTracking(Request $request)
    {
        $user = Auth::user();
        $userDivision = optional($user->branch)->division;
        $query = Vehicle::with('latestLocation')
            ->whereHas('branch', function ($q) use ($userDivision) {
                $q->where('division', $userDivision);
            });

        if ($search = $request->input('search')) {
            $query->where('RegID', 'LIKE', '%' . $search . '%');
        }
        $vehicles = $query->get();
        return view('dashboard.division-user.tracking', compact('vehicles'));
    }

    public function searchVehicle(Request $request)
    {
        $search = $request->input('search');
        $vehicles = Vehicle::where('RegID', 'like', '%' . $search . '%')->get();
        return view('dashboard.shared.vehicleSearch', compact('vehicles'));
    }

    public function destroy($id)
    {
        $vehicle = Vehicle::find($id);

        if (!$vehicle) {
            return redirect()->back()->with('error', 'Vehicle not found.');
        }

        // ✅ End all active assignments by setting returned_date
        VehicleAssignment::where('vehicle_id', $vehicle->RegID)
            ->whereNull('returned_date')
            ->update(['returned_date' => now()]);

        // ✅ Soft-delete the vehicle
        $vehicle->delete();

        return redirect()->route('vehicles.info')->with('success', 'Vehicle deleted and active assignment(s) ended.');
    }






    public function showLogbooks()
    {
        try {
            $logbooks = Logbook::with(['user', 'vehicle']) // optional if you want names
                ->latest()
                ->get();

            return view('dashboard.super-admin.logbook', compact('logbooks'));

        } catch (\Exception $e) {
            Log::error('Error loading logbooks: ' . $e->getMessage());
            return back()->with('error', 'Unable to load logbook data.');
        }
    }



}
