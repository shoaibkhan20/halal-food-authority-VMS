<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleMaintenance extends Model
{
    protected $table = 'vehicle_maintenance';

    public static function allowedStatuses()
    {
        return ['in_progress', 'completed'];
    }
    protected $casts = [
        'completed_at' => 'datetime',
    ];
    protected $fillable = [
        'vehicle_id',
        'maintenance_request_id',
        'status',
        'started_at',
        'completed_at',
        'actual_cost',
        'maintenance_notes',
        'performed_by',
    ];

    // 🔗 Belongs to a vehicle
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id', 'RegID');
    }
    // 🔗 Belongs to a maintenance request
    public function request()
    {
        return $this->belongsTo(MaintenanceRequest::class, 'maintenance_request_id');
    }
    public function maintenanceRequest()
    {
        return $this->belongsTo(MaintenanceRequest::class);
    }

    // 🔗 Performed by a user
    public function performed_by_user()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
