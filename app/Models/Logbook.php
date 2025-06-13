<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Logbook extends Model
{
    //
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id', 'RegID')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

}
