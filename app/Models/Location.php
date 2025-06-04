<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{

    protected $fillable = [
        'vehicle_id',
        'latitude',
        'longitude',
        'speed',
        'timestamp'
    ];
    protected $casts = [
        'timestamp' => 'datetime',
    ];
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id', 'RegID')->withTrashed();
    }

}
