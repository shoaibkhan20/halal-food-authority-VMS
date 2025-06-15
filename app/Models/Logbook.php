<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Logbook extends Model
{
    //
    // app/Models/Logbook.php
    protected $fillable = [
        'vehicle_id',
        'user_id',
        'trip_from',
        'trip_to',
        'description',
        'distance_covered',
        'fuel_used',
        'trip_date',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id', 'RegID')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

}
