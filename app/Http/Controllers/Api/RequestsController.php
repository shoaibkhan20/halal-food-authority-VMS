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

            // Check for existing request with same regId and status != final_approved
            $existing = MaintenanceRequest::where('vehicle_id', $validated['regId'])
                ->where('status', '!=', 'final_approved')
                ->exists();

            if ($existing) {
                return response()->json([
                    'message' => 'A maintenance request for this vehicle already exists and is not final approved.',
                ], 409); // 409 Conflict
            }
            // Create maintenance request
            $maintenance = MaintenanceRequest::create([
                'vehicle_id' => $validated['regId'],
                'applied_by' => auth()->id(),
                'issue' => $validated['issue'],
                'estimated_cost' => $validated['estimatedCost'] ?? null,
                'request_description' => $validated['comment'] ?? null,
            ]);

            return response()->json([
                'message' => 'Maintenance request created successfully.',
                'data' => $maintenance,
                'status' => 'pending'
            ], 201);
        }
        // Handle validation failures
        catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error: ' . json_encode($e->errors()),
            ], 422);
        } catch (QueryException $e) {
            Log::error('Database error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Database error: ' . $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            Log::error('Unexpected error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Unexpected error: ' . $e->getMessage(),
            ], 500);
        }

    }
}


