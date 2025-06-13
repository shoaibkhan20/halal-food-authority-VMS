<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleType extends Model
{
    use HasFactory;
    protected $fillable = ['name'];  // You can adjust this depending on your table's fields
    // If you want a reverse relationship with Vehicles, add this:
    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'vehicle_type', 'name');
    }
}
