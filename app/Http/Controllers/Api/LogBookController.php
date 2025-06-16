<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Logbook;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Carbon\Carbon;

class LogBookController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'regId' => 'required|exists:vehicles,RegID',
                'LocationFrom' => 'required|string|max:255',
                'LocationTo' => 'required|string|max:255',
                'description' => 'nullable|string',
                'distance_covered' => 'nullable|string',
                'fuelConsumeInLtr' => 'nullable|string',
                'date' => 'required|date_format:d-m-Y',
            ]);
            $userId = $request->user()->id;
            if (!$userId) {
                return response()->json(['message' => 'Unauthenticated'], 401);
            }

            $log = Logbook::create([
                'vehicle_id' => $validated['regId'],
                'user_id' => $userId,
                'trip_from' => $validated['LocationFrom'],
                'trip_to' => $validated['LocationTo'],
                'description' => $validated['description'] ?? null,
                'distance_covered' => $validated['distance_covered'] ?? null,
                'fuel_used' => $validated['fuelConsumeInLtr'] ?? null,
                'trip_date' => Carbon::createFromFormat('d-m-Y', $request->date)->format('Y-m-d'),
            ]);

            return response()->json([
                'message' => 'Logbook entry created successfully',
                'data' => $log
            ], 201);

        } catch (ValidationException $e) {
            $errors = collect($e->errors())->flatten()->implode(' | '); // Combine all error messages
            return response()->json([
                'message' => 'Validation failed: ' . $errors
            ], 422);
        } catch (QueryException $e) {
            Log::error('Database error: ' . $e->getMessage());
            return response()->json([
                'message' => 'A database error occurred. Please try again later.'
            ], 500);

        } catch (HttpException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getStatusCode());

        } catch (\Exception $e) {
            Log::error('Unexpected error: ' . $e->getMessage());
            return response()->json([
                'message' => 'An unexpected error occurred. Please try again later.'
            ], 500);
        }
    }

    public function getLogbook(Request $request)
    {
        $userId = $request->user()->id;
        if (!$userId) {
            return response()->json(['message' => 'invalid user token']);
        }
        $logbook = Logbook::where('user_id', $userId)
            ->
            get();
        if (!$logbook) {
            return response()->json(['message' => 'no logbooks by user']);
        }
        return response()->json(['message' => 'log books retrived', 'data' => $logbook], 200);
    }

}
