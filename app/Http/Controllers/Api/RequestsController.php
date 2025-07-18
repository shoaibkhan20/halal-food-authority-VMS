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
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
            // Check for existing request with same regId and status = pending
            $existing = MaintenanceRequest::where('vehicle_id', $validated['regId'])
                ->where('status', 'pending')
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
                'paymentMethod'=>'nullable|string'
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
                'payment_method' => $request->paymentMethod,
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





    public function getUserRequests(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                throw new AuthenticationException('User not authenticated or invalid token.');
            }

            // Wrap DB operations in a try-catch
            try {
                $fuelRequests = FuelRequest::where('user_id', $user->id)
                    ->latest()
                    ->get();

                $maintenanceRequests = MaintenanceRequest::where('applied_by', $user->id)
                    ->latest()
                    ->get();
            } catch (QueryException $e) {
                Log::error('Database query error: ' . $e->getMessage());
                return response()->json(['message' => 'Database error occurred.'], 500);
            }

            return response()->json([
                'message' => 'Requests retrieved successfully',
                'data' => [
                    'fuelRequests' => $fuelRequests,
                    'maintenanceRequests' => $maintenanceRequests,
                ]
            ]);

        } catch (AuthenticationException $e) {
            return response()->json(['message' => $e->getMessage()], 401);

        } catch (HttpException $e) {
            return response()->json(['message' => 'HTTP Error: ' . $e->getMessage()], $e->getStatusCode());

        } catch (\Exception $e) {
            Log::error('Unexpected error: ' . $e->getMessage());
            return response()->json(['message' => 'An unexpected error occurred.'], 500);
        }
    }

    public function deleteMaintenance($ReqId)
    {
        try {
            // Find the maintenance request
            $request = MaintenanceRequest::findOrFail($ReqId);

            // Check if status is 'pending'
            if ($request->status !== 'pending') {
                return response()->json(['message' => 'Only pending requests can be deleted'], 400);
            }
            // Attempt deletion
            $request->delete();
            return response()->json(['message' => 'Maintenance request deleted successfully'], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Maintenance request not found'], 404);

        } catch (\PDOException $e) {
            Log::error('Database error while deleting maintenance request: ' . $e->getMessage());
            return response()->json(['message' => 'Database error occurred'], 500);

        } catch (Exception $e) {
            Log::error('Unexpected error while deleting maintenance request: ' . $e->getMessage());
            return response()->json(['message' => 'Unexpected error occurred'], 500);
        }
    }


    public function deleteFuelRequest($ReqId)
    {
        try {
            // Find the fuel request
            $request = FuelRequest::findOrFail($ReqId);

            // Check status
            if ($request->status !== 'pending') {
                return response()->json(['message' => 'Only pending fuel requests can be deleted'], 400);
            }
            // Attempt deletion
            $request->delete();

            return response()->json(['message' => 'Fuel request deleted successfully'], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Fuel request not found'], 404);

        } catch (\PDOException $e) {
            Log::error('Database error while deleting fuel request: ' . $e->getMessage());
            return response()->json(['message' => 'Database error occurred'], 500);

        } catch (Exception $e) {
            Log::error('Unexpected error while deleting fuel request: ' . $e->getMessage());
            return response()->json(['message' => 'Unexpected error occurred'], 500);
        }
    }





}