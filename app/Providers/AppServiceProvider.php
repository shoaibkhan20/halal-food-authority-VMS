<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\VehicleAssignment;
use App\Observers\VehicleAssignmentObserver;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        VehicleAssignment::observe(VehicleAssignmentObserver::class);
    }
}
