<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleMaintenance extends Model
{
    protected $table = 'vehicle_maintenance';

    protected $fillable = [
        'vehicle_id',
        'maintenance_request_id',
        'status',
        'started_at',
        'completed_at',
        'actual_cost',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'started_at' => 'datetime',
        'actual_cost' => 'float',
    ];

    /**
     * Allowed maintenance statuses
     */
    public static function allowedStatuses()
    {
        return ['in_progress', 'completed'];
    }

    /**
     * 🔗 Belongs to a vehicle
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id', 'RegID')->withTrashed();
    }

    /**
     * 🔗 Belongs to a maintenance request (legacy name)
     */
    public function request()
    {
        return $this->belongsTo(MaintenanceRequest::class, 'maintenance_request_id');
    }

    /**
     * 🔗 Also belongs to a maintenance request (used elsewhere)
     */
    public function maintenanceRequest()
    {
        return $this->belongsTo(MaintenanceRequest::class);
    }

    /**
     * 🔗 One-to-many: supervisor reports related to this maintenance
     */
    public function supervisorReports()
    {
        return $this->hasMany(VehicleSupervisorReport::class, 'vehicle_maintenance_id');
    }

    /**
     * ⚠️ Old relationship, now excluded from table:
     * performed_by field no longer exists in the new table,
     * so this will be excluded unless you choose to re-add it.
     */
    // public function performed_by_user()
    // {
    //     return $this->belongsTo(User::class, 'performed_by');
    // }
}
