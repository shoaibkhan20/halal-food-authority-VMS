<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\VehicleAssignment;
class AuthController extends Controller
{


    public function login(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validation->fails()) {
            return response()->json(['message' => 'Username and password are required'], 422);
        }

        $user = User::where('username', $request->username)->first();

        if (!$user || $request->password!= $user->password) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Load current assignment and vehicle
        $user->load(['currentVehicleAssignments.vehicle']);

        $assignment = $user->currentVehicleAssignments;
        $vehicle = $assignment?->vehicle;
        $branch = $assignment?->vehicle->branch;

        // Build custom response
        $response = [
            'regId' => $vehicle?->RegID,
            'name' => $user->name,
            'model' => $vehicle?->Model,
            'driver' => $user->name,
            'fuelType' => $vehicle?->Fuel_type,
            'vehicleType' => $vehicle?->Vehicle_Type,
            'averageMileage' => $vehicle?->Average_mileage,
            'district' => $branch->district??NULL, // Or modify if you have a related district
            'token' => $user->createToken('api_token')->plainTextToken,
            'contact' => $user->contact,

        ];

        return response()->json($response);
    }



}
