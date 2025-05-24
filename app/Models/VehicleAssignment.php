<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Vehicle;
class VehicleAssignment extends Model
{
    protected static function booted()
    {
        parent::booted();
        static::observe(\App\Observers\VehicleAssignmentObserver::class);
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
