<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\Vehicle;
use App\Models\Location;
class VehicleController extends Controller
{
    //
    public function vehicles()
    {
        $regIds = Vehicle::select('RegID')->limit(6)->get();
        return view('dashboard.shared.vehiclesinfo', compact('regIds'));
    }
    public function details($regid)
    {
        $vehicle = Vehicle::where('RegID',$regid)->first();
        return view('dashboard.shared.vehicledetails',['vehicle' => $vehicle]);
    }

    public function tracking()
    {

        $locations = Location::orderBy('timestamp', 'desc')->take(6)->get();
        // dd('', $locations);
        return view('dashboard.shared.vehicle-tracking',compact('locations'));
    }
}
