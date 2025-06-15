<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FuelRequest extends Model
{
    //
    protected $fillable = [
        'vehicle_id',
        'user_id',
        'liter',
        'price_per_liter',
        'fuel_amount',
        'status',
        'invoice',
        'fuel_date',
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
