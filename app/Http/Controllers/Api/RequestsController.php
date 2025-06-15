<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use App\Models\MaintenanceRequest;
use App\Models\FuelRequest;
use Illuminate\Support\Facades\Validator;
use Exception;
use Carbon\Carbon;
class RequestsController extends Controller
{
    public function MaintenanceRequest(Request $request)
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

    public function fuelRequest(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'regId' => 'required|exists:vehicles,RegID',
                'liter' => 'required|numeric|min:0',
                'pricePerLiter' => 'required|numeric|min:0',
                'total' => 'required|numeric|min:0',
                'date' => 'required|date_format:d-m-Y',
                'billImage' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            // 🚫 Prevent if there's already a pending request for this regId
            $pendingRequest = FuelRequest::where('vehicle_id', $request->regId)
                ->where('status', 'pending')
                ->first();

            if ($pendingRequest) {
                return response()->json([
                    'message' => 'Please wait, your application is already in process.'
                ], 409); // 409 = Conflict
            }

            $invoicePath = null;
            if ($request->hasFile('billImage')) {
                try {
                    $invoicePath = asset('storage/' . $request->file('billImage')->store('invoices', 'public'));
                } catch (Exception $e) {
                    Log::error('File upload failed: ' . $e->getMessage());
                    return response()->json(['error' => 'Failed to upload invoice image.'], 500);
                }
            }

            $fuelRequest = FuelRequest::create([
                'vehicle_id' => $request->regId,
                'user_id' => $request->user()->id,
                'liter' => $request->liter,
                'price_per_liter' => $request->pricePerLiter,
                'fuel_amount' => $request->total,
                'status' => 'pending',
                'invoice' => $invoicePath,
                'fuel_date' => Carbon::createFromFormat('d-m-Y', $request->date)->format('Y-m-d'),
            ]);

            return response()->json([
                'message' => 'Fuel request created successfully',
                'fuel_request' => $fuelRequest,
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $e->errors()
            ], 422);

        } catch (QueryException $e) {
            Log::error('Database error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Database error',
                'message' => $e->getMessage()
            ], 500);

        } catch (Exception $e) {
            Log::error('Unexpected error: ' . $e->getMessage());
            return response()->json([
                'error' => 'An unexpected error occurred',
                'message' => $e->getMessage()
            ], 500);
        }
    }




}


