@extends('layouts.app')
@section('content')

    <div class="w-full min-h-screen grid place-items-center">
        <div class="w-[80%] h-full sm:h-[85vh] overflow-y-auto rounded-lg bg-white backdrop:bg-gray/50">
            <div class="flex flex-col w-full p-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold">Vehicle Status Report</h1>
                </div>

                <div class="flex justify-between">
                    <div class="mb-6">
                        <input type="text" placeholder="Search Reg.id" class="border border-gray-300 rounded px-4 py-2">
                    </div>
                    <div class="mb-6">
                        <select class="border border-gray-300 rounded px-4 py-2">
                            <option>Last Month</option>
                            <option>Last 3 Months</option>
                            <option>All Time</option>
                        </select>
                    </div>
                </div>
                <!-- name of each tab group should be unique -->
                <div class="tabs tabs-lift">
                    <input type="radio" name="my_tabs_3" class="tab"
                        aria-label="Available" checked="checked" />
                    <div class="tab-content bg-base-100 border-base-300 p-6">

                        @php
                            $headers = ['Reg ID', 'Model', 'Status', 'Region'];

                            $rows = $vehicles->filter(function ($vehicle) {
                                // Only include vehicles with no current assignment
                                return empty($vehicle['AssignedTo']);
                            })->map(function ($vehicle) {
                                return [
                                    $vehicle['RegID'],
                                    $vehicle['Model'],
                                    $vehicle['status'] ?? 'Available',
                                    $vehicle['Region'],
                                ];
                            })->toArray();
                        @endphp

                        <x-table :headers="$headers" :rows="$rows" />

                    </div>

                    <input type="radio" name="my_tabs_3" class="tab" aria-label="Assigned"  />
                    <div class="tab-content bg-base-100 border-base-300 p-6">

                        @php
                            $headers = ['Reg ID', 'Model', 'Assigned To', 'Status', 'Region'];
                            $rows = $vehicles->filter(function ($vehicle) {
                                return !empty($vehicle['AssignedTo']);
                            })->map(function ($vehicle) {
                                return [
                                    $vehicle['RegID'],
                                    $vehicle['Model'],
                                    $vehicle['AssignedTo'],
                                    'Assigned',
                                    $vehicle['Region'],
                                ];
                            })->toArray();
                        @endphp
                        <x-table :headers="$headers" :rows="$rows" />

                    </div>

                    <input type="radio" name="my_tabs_3" class="tab" aria-label="Under Maintenance" />
                    <div class="tab-content bg-base-100 border-base-300 p-6">

                        @php
                            $headers = ['Reg ID', 'Model', 'Assigned To', 'Status', 'Region'];
                            $rows = $vehicles->filter(function ($vehicle) {
                                return $vehicle['under_maintenance'] === true;
                            })->map(function ($vehicle) {
                                return [
                                    $vehicle['RegID'],
                                    $vehicle['Model'],
                                    $vehicle['AssignedTo'] ?? '-',
                                    'Under Maintenance',
                                    $vehicle['Region'],
                                ];
                            })->toArray();
                        @endphp
                        <x-table :headers="$headers" :rows="$rows" />
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection