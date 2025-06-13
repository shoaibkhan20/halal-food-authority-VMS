<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceRequest extends Model
{
    protected $fillable = [
        'vehicle_id',
        'applied_by',
        'issue',
        'estimated_cost',
        'status',

        'director_status',
        'director_reviewed_by',
        'director_rejection_message',

        'committee_status',
        'committee_reviewed_by',
        'committee_rejection_message',

        'director_final_status',
        'director_final_approved_by',
        'director_final_rejection_message',
    ];

    protected static function booted()
    {
        static::saving(function ($request) {
            // ✅ Final decision overrides all
            if ($request->director_final_status === 'approved') {
                $request->status = 'final_approved';
            } elseif ($request->director_final_status === 'rejected') {
                $request->status = 'final_rejected';
            }
            // ✅ If director approved and no final stage — mark as final
            elseif ($request->director_status === 'approved') {
                $request->status = 'final_approved';
            }
            // ✅ If director rejected, move to committee review
            elseif ($request->director_status === 'rejected') {
                // Only then allow committee to change status
                $request->status = 'final_rejected';
            }
            elseif($request->committee_status === 'approved'){
                $request->status = 'committee_approved';
            }
            else if ($request->committee_status === 'rejected') {
                $request->status = 'committee_rejected';
            } 
        });
    }

    // Relationships
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id', 'RegID');
    }

    public function appliedBy()
    {
        return $this->belongsTo(User::class, 'applied_by')->withTrashed();
    }

    public function directorReviewer()
    {
        return $this->belongsTo(User::class, 'director_reviewed_by')->withTrashed();
    }


    public function committeeReviewer()
    {
        return $this->belongsTo(User::class, 'committee_reviewed_by')->withTrashed();
    }

    public function finalDirectorApprover()
    {
        return $this->belongsTo(User::class, 'director_final_approved_by')->withTrashed();
    }

    public function supervisorReports()
    {
        return $this->hasMany(VehicleSupervisorReport::class);
    }

    public function maintenance()
    {
        return $this->hasMany(VehicleMaintenance::class);
    }
    public function vehicleMaintenance()
    {
        return $this->hasOne(VehicleMaintenance::class);
    }
}
