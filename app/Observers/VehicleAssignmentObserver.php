<?php

namespace App\Observers;

use App\Models\VehicleAssignment;

class VehicleAssignmentObserver
{
    /**
     * Handle the VehicleAssignment "created" event.
     */
    public function created(VehicleAssignment $vehicleAssignment): void
    {
        //
    }

    /**
     * Handle the VehicleAssignment "updated" event.
     */
    public function updated(VehicleAssignment $vehicleAssignment): void
    {
        //
    }

    /**
     * Handle the VehicleAssignment "deleted" event.
     */
    public function deleted(VehicleAssignment $vehicleAssignment): void
    {
        //
    }

    /**
     * Handle the VehicleAssignment "restored" event.
     */
    public function restored(VehicleAssignment $vehicleAssignment): void
    {
        //
    }

    /**
     * Handle the VehicleAssignment "force deleted" event.
     */
    public function forceDeleted(VehicleAssignment $vehicleAssignment): void
    {
        //
    }
}
