<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

use App\Models\MaintenanceRequest;
use App\Observers\MaintenanceRequestObserver;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        // You can register events and listeners here if you want
    ];

    /**
     * Register any events for your application.
     */
    public function boot()
    {
        // Register your observers here
        MaintenanceRequest::observe(MaintenanceRequestObserver::class);
    }
}
