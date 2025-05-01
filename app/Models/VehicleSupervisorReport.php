<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleSupervisorReport extends Model
{
    //
    public function maintenanceRequest()
    {
        return $this->belongsTo(MaintenanceRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
