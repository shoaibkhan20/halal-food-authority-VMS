<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FuelRequest;
class FuelRequestsController extends Controller
{
    public function index()
    {
        $pending = FuelRequest::with('vehicle', 'user')
            ->where('status', 'pending')
            ->get();
        $approved = FuelRequest::with('vehicle', 'user')
            ->where('status', 'approved')
            ->get();

        $rejected = FuelRequest::with('vehicle', 'user')
            ->where('status', 'rejected')
            ->get();

        return view('dashboard.shared.fuel-requests', compact('pending', 'approved', 'rejected'));
    }

    public function approveFuelRequest($id)
    {
        try {
            $request = FuelRequest::find($id);
            if (!$request) {
                return response()->json(['message' => 'Fuel request not found'], 404);
            }
            if ($request->status !== 'pending') {
                return response()->json(['message' => 'Only pending requests can be approved'], 400);
            }
            $request->status = 'approved';
            $request->save();
            return back()->with('success', 'Request Approved');
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while approving the request',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function rejectFuelRequest($id)
    {
        try {
            $request = FuelRequest::find($id);

            if (!$request) {
                return redirect()->back()->with('error', 'Fuel request not found.');
            }

            if ($request->status !== 'pending') {
                return redirect()->back()->with('error', 'Only pending fuel requests can be rejected.');
            }

            $request->status = 'rejected';
            $request->save();

            return redirect()->back()->with('success', 'Fuel request rejected successfully.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while rejecting the fuel request.');
        }
    }



}
