<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class LocationController extends Controller
{
    public function store(Request $request)
    {
        // Validation without timestamp field, since backend sets it
        $validator = Validator::make($request->all(), [
            'vehicle_id' => 'required|exists:vehicles,RegID',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'speed' => 'nullable|numeric|min:0',
            // no timestamp validation here
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }
        // Use current time for timestamp
        $location = Location::create([
            'vehicle_id' => $request->vehicle_id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'speed' => $request->speed,
            'timestamp' => now(),  // <-- use Laravel helper to get current timestamp
        ]);

        return response()->json(['status' => 'success', 'location_id' => $location->id], 201);
    }



    public function latest($vehicle_id)
    {
        $location = Location::where('vehicle_id', $vehicle_id)
            ->orderByDesc('timestamp')
            ->first();

        if (!$location) {
            return response()->json([
                'status' => 'error',
                'message' => 'No location found for this vehicle.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'latitude' => $location->latitude,
                'longitude' => $location->longitude,
                'speed' => $location->speed,
                'timestamp' => Carbon::parse($location->timestamp)->toDateTimeString(),
            ]
        ]);
    }

}

