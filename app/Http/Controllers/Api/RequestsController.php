<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use App\Models\MaintenanceRequest;

class RequestsController extends Controller
{
    public function store(Request $request)
    {
        try {
            // Validate input
            $validated = $request->validate([
                'regId' => 'required|exists:vehicles,RegID',
                'issue' => 'required|string',
                'estimatedCost' => 'nullable|numeric',
                'comment' => 'nullable|string',
            ]);
            // Create maintenance request
            $maintenance = MaintenanceRequest::create([
                'vehicle_id' => $validated['vehicle_id'],
                'applied_by' => auth()->id(),
                'issue' => $validated['issue'],
                'estimated_cost' => $validated['estimated_cost'] ?? null,
                'request_description' => $validated['comment'] ?? null,
            ]);
            return response()->json([
                'message' => 'Maintenance request created successfully.',
                'data' => $maintenance
            ], 201);
        }
        // Handle validation failures
        catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error.',
                'errors' => $e->errors(),
            ], 422);
        }
        // Handle foreign key or DB-level issues
        catch (QueryException $e) {
            Log::error('Database error while creating maintenance request: ' . $e->getMessage());
            return response()->json([
                'message' => 'A database error occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
        // Handle any other unexpected exception
        catch (\Exception $e) {
            Log::error('Unexpected error: ' . $e->getMessage());
            return response()->json([
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
