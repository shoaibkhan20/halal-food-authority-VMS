<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Vehicle extends Model
{
    use SoftDeletes;
    //
    protected $primaryKey = 'RegID';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'RegID',
        'Model',
        'Fuel_type',
        'Vehicle_Type',
        'Average_mileage',
        'status',
        'branch_id',
    ];
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
    // Optional: cleaner access to location
    public function getLocationAttribute()
    {
        return $this->branch->location ?? 'Unknown';
    }
    public function currentAssignment()
    {
        return $this->hasOne(VehicleAssignment::class, 'vehicle_id', 'RegID')
            ->whereNull('returned_date');
    }
    public function assignments()
    {
        return $this->hasMany(VehicleAssignment::class, 'vehicle_id');
    }
    public function latestAssignment()
    {
        return $this->hasOne(VehicleAssignment::class, 'vehicle_id')->latestOfMany();
    }

    public function locations()
    {
        return $this->hasMany(Location::class, 'vehicle_id');
    }
    public function latestLocation()
    {
        return $this->hasOne(Location::class, 'vehicle_id')->latestOfMany();
    }

    public function fuelRequests()
    {
        return $this->hasMany(FuelRequest::class, 'vehicle_id');
    }

    public function maintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class, 'vehicle_id');
    }
    public function maintenanceRecords()
    {
        return $this->hasMany(VehicleMaintenance::class, 'vehicle_id', 'RegID');
    }
    public function isUnderMaintenance()
    {
        return $this->maintenanceRecords()
            ->where('status', 'in_progress')
            ->exists();
    }

    public function scopeUnderMaintenance($query)
    {
        return $query->whereIn('RegID', function ($sub) {
            $sub->select('vehicle_id')
                ->from('vehicle_maintenance')
                ->where('status', 'in_progress');
        });
    }

}
