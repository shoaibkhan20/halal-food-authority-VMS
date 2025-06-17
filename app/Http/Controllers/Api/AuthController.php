<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\VehicleAssignment;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Exception;
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
        if (!$user || $request->password != $user->password) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
        
        // delete previous tokens
        $user->tokens()->delete();
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
            'district' => $branch->district ?? NULL, // Or modify if you have a related district
            'token' => $user->createToken('api_token')->plainTextToken,
            'contact' => $user->contact,
        ];
        return response()->json($response);
    }

    public function me(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                throw new UnauthorizedHttpException('Bearer', 'Invalid user or token, kindly login first');
            }

            // Eager load related data
            $user->load(['currentVehicleAssignments.vehicle']);

            $assignment = $user->currentVehicleAssignments;
            $vehicle = $assignment?->vehicle;
            $branch = $vehicle?->branch;

            $response = [
                'regId' => $vehicle?->RegID,
                'name' => $user->name,
                'model' => $vehicle?->Model,
                'driver' => $user->name,
                'fuelType' => $vehicle?->Fuel_type,
                'vehicleType' => $vehicle?->Vehicle_Type,
                'averageMileage' => $vehicle?->Average_mileage,
                'district' => $branch?->district ?? null,
                'token' => $user->createToken('api_token')->plainTextToken,
                'contact' => $user->contact,
            ];

            return response()->json($response, 200);

        } catch (UnauthorizedHttpException $e) {
            return response()->json(['message' => $e->getMessage()], 401);

        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Related model not found.'], 404);

        } catch (Exception $e) {
            Log::error('Exception in me(): ' . $e->getMessage());
            return response()->json(['message' => 'Something went wrong. Please try again later.'], 500);
        }
    }

    public function logout(Request $request)
{
    try {
        // Revoke the current user's token
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully'], 200);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Logout failed'], 500);
    }
}





}
