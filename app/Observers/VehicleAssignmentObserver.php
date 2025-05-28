<?php

namespace App\Observers;

use App\Models\VehicleAssignment;
use App\Models\Vehicle;

class VehicleAssignmentObserver
{
    public function created(VehicleAssignment $assignment)
    {
        if (is_null($assignment->returned_date)) {
            Vehicle::where('RegID', $assignment->vehicle_id)->update(['status' => 'Assigned']);
        }
    }
    public function updated(VehicleAssignment $assignment)
    {
        // When vehicle is returned
        if (!is_null($assignment->returned_date)) {
            // Check if there are other active assignments
            $isStillAssigned = VehicleAssignment::where('vehicle_id', $assignment->vehicle_id)
                ->whereNull('returned_date')
                ->exists();
            Vehicle::where('RegID', $assignment->vehicle_id)
                ->update(['status' => $isStillAssigned ? 'Assigned' : 'Available']);
        }
    }
}
