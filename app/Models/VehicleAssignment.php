<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleAssignment extends Model
{
    protected static function booted()
    {
        static::created(function ($assignment) {
            Vehicle::where('RegID', $assignment->vehicle_id)
                   ->update(['status' => 'Assigned']);
        });

        static::updated(function ($assignment) {
            if ($assignment->returned_date) {
                Vehicle::where('RegID', $assignment->vehicle_id)
                       ->update(['status' => 'Free']);
            }
        });
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id', 'RegID');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
