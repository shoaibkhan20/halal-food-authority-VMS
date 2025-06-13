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
            self::syncVehicleStatus($assignment->vehicle_id);
        });
        static::updated(function ($assignment) {
            self::syncVehicleStatus($assignment->vehicle_id);
        });
        static::deleted(function ($assignment) {
            self::syncVehicleStatus($assignment->vehicle_id);
        });
    }

    protected static function syncVehicleStatus($vehicleId)
    {
        $vehicle = Vehicle::find($vehicleId);
        if (!$vehicle)
            return;
        // Get latest assignment
        $latestAssignment = $vehicle->assignments()
            ->orderByDesc('assigned_date') // or 'created_at'
            ->first();
        if (!$latestAssignment || $latestAssignment->returned_date) {
            $vehicle->update(['status' => 'Available']);
        } else {
            $vehicle->update(['status' => 'Assigned']);
        }
    }

    protected $fillable = [
        'vehicle_id',
        'user_id',
        'assigned_date',
        'returned_date',
    ];
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id', 'RegID')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }
}
