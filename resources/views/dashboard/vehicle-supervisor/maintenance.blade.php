@extends('layouts.app')
@section('content')

    <div class="w-full min-h-screen grid place-items-center">
        <div class="w-full h-full overflow-y-auto rounded-lg bg-white backdrop:bg-gray/50">
            <div class="flex flex-col w-full p-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold">Maintenance</h1>
                </div>


                <div class="flex justify-between">
                    <div class="mb-6 relative w-full max-w-xs">
                        <form action="{{ route('vehicle-supervisor.maintenance') }}" method="GET">
                            <input type="text" id="filterSearch" name="search" placeholder="Search Reg.id"
                                class="border border-gray-300 rounded px-4 py-1 w-full pr-8"
                                value="{{ request('search') }}">
                            @if(request('search'))
                                <a href="{{ route('vehicle-supervisor.maintenance') }}"
                                    class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
                                    aria-label="Clear search">
                                    &times;
                                </a>
                            @endif
                        </form>
                    </div>

                    <div class="mb-6">
                        <select class="border border-gray-300 rounded px-4 py-1">
                            <option>Last Month</option>
                            <option>Last 3 Months</option>
                            <option>All Time</option>
                        </select>
                    </div>
                </div>
                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="tabs tabs-lift">

                    <input type="radio" name="my_tabs_3" class="tab" aria-label="In progress" checked="checked" />
                    <div class="tab-content bg-base-100 border-base-300 p-6">
                        @php
                            $headers = ['Reg ID', 'Date', 'Cost', 'Location', 'Status', 'Actions'];
                            $rows =
                                $maintenanceHistory->filter(fn($r) => in_array($r->status, ['in_progress']))
                                    ->map(function ($record) {
                                        $regId = $record->vehicle_id;
                                        $date = $record->started_at ?? 'N/A';
                                        $cost = $record->actual_cost ? '$' . number_format($record->actual_cost, 2) : 'N/A';
                                        // $items = $record->maintenance_notes ?? '—';
                                        $location = $record->vehicle->branch->location ?? 'N/A';
                                        $status = ucfirst($record->status);
                                        $actions = '';
                                        $actions .= '
                                                                                                                                <label for="reject-modal-' . $record->id . '" class="btn btn-sm bg-green-800 text-white hover:bg-green-700 transition">Completion Report</label>
                                                                                                                            ';
                                        // Modal content
                                        $actions .= '
                                                                                                                                <input type="checkbox" id="reject-modal-' . $record->id . '" class="modal-toggle" />
                                                                                                                                <div class="modal">
                                                                                                                                    <div class="modal-box w-full max-w-lg">
                                                                                                                                        <h3 class="text-xl font-semibold text-gray-800 mb-4">Reject Maintenance Request</h3>
                                                                                                                                        <form method="POST" action="' . route('vehicle-maintenance.complete', ['id' => $record->id]) . '" class="space-y-4" enctype="multipart/form-data">
                                                                                                                                            ' . csrf_field() . '

                                                                                                                                            <!-- Maintenance Notes Input -->
                                                                                                                                            <div>
                                                                                                                                                <label for="maintenance_notes" class="block mb-1 font-semibold text-sm text-gray-700">Maintenance Notes</label>
                                                                                                                                                <textarea  name="maintenance_notes"  class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent" placeholder="Enter maintenance notes..."></textarea>
                                                                                                                                            </div>

                                                                                                                                            <!-- Performed By Input -->
                                                                                                                                            <div>
                                                                                                                                                <label for="performed_by" class="block mb-1 font-semibold text-sm text-gray-700">Mechanic info</label>
                                                                                                                                                <input type="text" name="performed_by"  class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent" placeholder="Performed By">
                                                                                                                                            </div>

                                                                                                                                            <!-- File Upload Input -->
                                                                                                                                            <div>
                                                                                                                                                <label for="attachment" class="block mb-1 font-semibold text-sm text-gray-700">Attach File</label>
                                                                                                                                                <input 
                                                                                                                                                    required
                                                                                                                                                    type="file" 
                                                                                                                                                    name="attachment" 
                                                                                                                                                    id="attachment" 
                                                                                                                                                    accept=".pdf,.doc,.docx,.xls,.xlsx"
                                                                                                                                                    class="w-full px-4 py-2 border border-gray-300 rounded-md bg-white file:mr-4 file:py-2 file:px-4 file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent"
                                                                                                                                                >
                                                                                                                                            </div>

                                                                                                                                            <!-- Submit/Cancel Buttons -->
                                                                                                                                            <div class="flex justify-end gap-2 mt-6">
                                                                                                                                                <button type="submit" class="btn bg-red-800 text-white hover:bg-red-700 transition">Submit</button>
                                                                                                                                                <label for="reject-modal-' . $record->id . '" class="btn btn-outline">Cancel</label>
                                                                                                                                            </div>
                                                                                                                                        </form>


                                                                                                                                    </div>
                                                                                                                                </div>';
                                        return [$regId, $date, $cost, $location, $status, $actions];
                                    })->toArray();
                        @endphp
                        <x-table :headers="$headers" :rows="$rows" />
                    </div>

                    <input type="radio" name="my_tabs_3" class="tab" aria-label="Maintenance History" />
                    <div class="tab-content bg-base-100 border-base-300 p-6">
                        {{-- Maintenance History Table --}}
                        @php
                            $headers = ['Reg ID', 'Date', 'Cost', 'Items', 'Location', 'Status', 'Actions'];
                            $rows =
                                $maintenanceHistory->filter(fn($r) => in_array($r->status, ['completed']))
                                    ->map(function ($record) {
                                        $regId = $record->vehicle_id;
                                        $date = $record->started_at ?? 'N/A';
                                        $cost = $record->actual_cost ? '$' . number_format($record->actual_cost, 2) : 'N/A';
                                        $items = $record->supervisorReports->first()->maintenance_notes ?? '—';
                                        $location = $record->vehicle->branch->location ?? 'N/A';
                                        $status = ucfirst($record->status);
                                        $actions = '';
                                        $report = $record->supervisorReports->first();

                                        if ($report && $report->report_file_path) {
                                            $actions = '<a href="' . asset('storage/' . $report->report_file_path) . '" target="_blank" class="btn btn-sm bg-green-800 text-white">Report</a>';
                                        } else {
                                            $actions = '<span class="text-gray-500 text-sm">No Report</span>';
                                        }
                                        return [$regId, $date, $cost, $items, $location, $status, $actions];
                                    })->toArray();
                        @endphp
                        <x-table :headers="$headers" :rows="$rows" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal for each maintenance bill --}}
    @foreach($maintenanceHistory as $record)
        <input type="checkbox" id="modal-{{ $record->id }}" class="modal-toggle" />
        <div class="modal" role="dialog">
            <div class="modal-box">
                <h1 class="font-bold text-lg mb-2">Maintenance Report - {{ $record->vehicle_id }}</h1>
                <div class="space-y-1">
                    <p><strong>Date:</strong> {{ $record->started_at ?? 'N/A' }}</p>
                    <p><strong>Status:</strong> {{ ucfirst($record->status) }}</p>
                    <p><strong>Performed By:</strong> {{ $record->performed_by_user->name ?? 'N/A' }}</p>
                    <p><strong>Estimated Cost:</strong> ${{ number_format($record->estimated_cost, 2) }}</p>
                    <p><strong>Actual Cost:</strong> ${{ number_format($record->actual_cost, 2) }}</p>
                    <p><strong>Notes:</strong> {{ $record->maintenance_notes }}</p>
                </div>
                <div class="modal-action">
                    <label for="modal-{{ $record->id }}" class="btn">Close</label>
                </div>
            </div>
        </div>
    @endforeach

    <script>
        const maintenanceHistory = @json($maintenanceHistory);
    </script>
@endsection