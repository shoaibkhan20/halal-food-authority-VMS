<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleSupervisorReport extends Model
{
    protected $fillable = [
        'vehicle_maintenance_id',
        'generated_by',
        'maintenance_notes',
        'mechanic_info',
        'report_file_path',
    ];

    public function vehicleMaintenance()
    {
        return $this->belongsTo(VehicleMaintenance::class);
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
