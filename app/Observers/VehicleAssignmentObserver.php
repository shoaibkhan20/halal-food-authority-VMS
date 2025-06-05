<?php

namespace App\Observers;

use App\Models\VehicleAssignment;
use App\Models\Vehicle;

class VehicleAssignmentObserver
{
    public function created(VehicleAssignment $assignment)
    {
        $this->syncVehicleStatus($assignment->vehicle_id);
    }

    public function updated(VehicleAssignment $assignment)
    {
        $this->syncVehicleStatus($assignment->vehicle_id);
    }

    public function deleted(VehicleAssignment $assignment)
    {
        $this->syncVehicleStatus($assignment->vehicle_id);
    }

    private function syncVehicleStatus($vehicleId)
    {
        $vehicle = Vehicle::find($vehicleId);

        if (!$vehicle) return;

        // Check if there are any active (non-returned) assignments
        $hasActiveAssignment = $vehicle->assignments()
            ->whereNull('returned_date')
            ->exists();

        $vehicle->update([
            'status' => $hasActiveAssignment ? 'Assigned' : 'Available',
        ]);
    }
}
