@extends('layouts.app')
@section('content')

<div class="w-full min-h-screen grid place-items-center">
    <div class="relative w-full h-full overflow-y-auto rounded-lg bg-white backdrop:bg-gray/50">
        <div class="flex flex-col w-full p-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold">Logbook Records</h1>
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

            @php
                $headers = ['Vehicle ID', 'User Name', 'Trip From', 'Trip To', 'Trip Date', 'Fuel Used (L)', 'Description'];

                $rows = $logbooks->map(function ($logbook) {
                    return [

                        $logbook->vehicle_id,
                        $logbook->user->name ?? 'N/A',
                        $logbook->trip_from,
                        $logbook->trip_to,
                        $logbook->trip_date,
                        $logbook->fuel_used ?? 'N/A',
                        $logbook->description ?? '-',
                    ];
                })->toArray();
            @endphp

            <x-table :headers="$headers" :rows="$rows" :html="false" />
        </div>
    </div>
</div>

@endsection
