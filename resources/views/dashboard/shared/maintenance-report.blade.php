@extends('layouts.app')
@section('content')

    <div class="w-full min-h-screen grid place-items-center">
        <div class="w-full h-full rounded-lg bg-white backdrop:bg-gray/50">
            <div class="flex flex-col w-full p-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold">Maintenance Report</h1>
                </div>

                <div class="flex justify-between">
                    <div class="mb-6">
                        <input type="text" placeholder="Report by ID" class="border border-gray-300 rounded px-4 py-2">
                        <input type="text" placeholder="Report by Date" class="border border-gray-300 rounded px-4 py-2">
                    </div>

                    {{-- <div class="mb-6">
                        <select class="border border-gray-300 rounded px-4 py-2">
                            <option>Last Month</option>
                            <option>Last 3 Months</option>
                            <option>All Time</option>
                        </select>
                    </div> --}}
                </div>
        
                @php
                    $headers = ['Reg ID', 'Date', 'Cost', 'Items', 'Location'];

                    $rows = $records->map(function ($record) {
                        return [
                            $record['RegID'],
                            $record['Date'],
                            '$' . $record['Cost'],
                            $record['Items'],
                            $record['Location'],
                        ];
                    })->toArray();
                @endphp

                <x-table :headers="$headers" :rows="$rows" />
            </div>
        </div>
    </div>

@endsection