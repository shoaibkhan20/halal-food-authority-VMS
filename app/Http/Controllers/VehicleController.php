<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\Vehicle;
class VehicleController extends Controller
{
    //
    public function index()
    {
        $regIds = Vehicle::select('RegID')->limit(6)->get();
        return view('dashboard.shared.vehiclesinfo', compact('regIds'));
    }
    
    public function details($regid)
    {

        $vehicle = Vehicle::where('RegID',$regid)->first();
        return view('dashboard.shared.vehicledetails',['vehicle' => $vehicle]);
    }
}
