<?php

namespace App\Observers;

use App\Models\MaintenanceRequest;
use App\Models\VehicleMaintenance;

class MaintenanceRequestObserver
{
    /**
     * Handle the MaintenanceRequest "updated" event.
     */
    public function updated(MaintenanceRequest $maintenanceRequest)
    {
        // Check if the 'status' attribute was changed and is now 'final_approved'
        if ($maintenanceRequest->isDirty('status') && $maintenanceRequest->status === 'final_approved') {

            // Check if a VehicleMaintenance record for this maintenance_request already exists to avoid duplicates
            $exists = VehicleMaintenance::where('maintenance_request_id', $maintenanceRequest->id)->exists();

            if (!$exists) {
                // Create new VehicleMaintenance record
                VehicleMaintenance::create([
                    'maintenance_request_id' => $maintenanceRequest->id,
                    'vehicle_id' => $maintenanceRequest->vehicle_id,
                    'status' => 'not_started',  // Default starting status
                    'performed_by' => null,     // Will be assigned later when maintenance starts
                    // You can add default values for other fields if needed
                ]);
            }
        }
    }
}
