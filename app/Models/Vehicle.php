<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    //
    protected $primaryKey = 'RegID';
    public $incrementing = false;
    protected $keyType = 'string';

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function assignments()
    {
        return $this->hasMany(VehicleAssignment::class, 'vehicle_id');
    }

    public function locations()
    {
        return $this->hasMany(Location::class, 'vehicle_id');
    }

    public function fuelRequests()
    {
        return $this->hasMany(FuelRequest::class, 'vehicle_id');
    }

    public function maintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class, 'vehicle_id');
    }

}
