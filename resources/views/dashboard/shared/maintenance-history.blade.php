@extends('layouts.app')
@section('content')

    <div class="w-full min-h-screen grid place-items-center">
        <div class="w-[80%] h-full sm:h-[85vh] overflow-y-auto rounded-lg bg-white backdrop:bg-gray/50">
            <div class="flex flex-col w-full p-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold">Maintenance History</h1>
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

                {{-- Maintenance History Table --}}
                @php
                    $headers = ['Reg ID', 'Date', 'Cost', 'Items', 'Location', 'Actions'];
                    $rows = $maintenanceRecords->map(function ($record) {
                        return [
                            $record->vehicle_id,
                            $record->started_at ?? 'N/A',
                            $record->actual_cost ? '$' . number_format($record->actual_cost, 2) : 'N/A',
                            $record->maintenance_notes ?? '—',
                            $record->vehicle->Region ?? 'N/A',
                            '<label for="modal-' . $record->id . '" class="btn btn-sm">Bill</label>'
                        ];
                    })->toArray();
                @endphp
                <x-table :headers="$headers" :rows="$rows" />

            </div>
        </div>
    </div>

    {{-- Modal for each maintenance record --}}
    @foreach($maintenanceRecords as $record)
        <input type="checkbox" id="modal-{{ $record->id }}" class="modal-toggle" />
        <div class="modal" role="dialog">
            <div class="modal-box">
                <h3 class="font-bold text-lg mb-2">Maintenance Bill - {{ $record->vehicle_id }}</h3>
                <div class="space-y-1">
                    <p><strong>Date:</strong> {{ optional($record->started_at)->format('Y-m-d') ?? 'N/A' }}</p>
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

@endsection
