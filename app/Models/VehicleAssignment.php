<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Vehicle;
class VehicleAssignment extends Model
{
    protected static function boot()
    {
        parent::boot();
        static::created(function ($assignment) {
            // Automatically update vehicle status when assigned
            $vehicle = Vehicle::find($assignment->vehicle_id);
            if ($vehicle && $vehicle->status !== 'Assigned') {
                $vehicle->update(['status' => 'Assigned']);
            }
        });

        static::deleted(function ($assignment) {
            // Optional: revert to Available if the assignment is deleted
            $vehicle = Vehicle::find($assignment->vehicle_id);
            if ($vehicle) {
                $vehicle->update(['status' => 'Available']);
            }
        });
    }
    protected $fillable = [
        'vehicle_id',
        'user_id',
        'assigned_date',
        'returned_date',
    ];
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id', 'RegID');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
