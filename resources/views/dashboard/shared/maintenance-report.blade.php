@extends('layouts.app')
@section('content')

    <div class="w-full min-h-screen grid place-items-center">
        <div class="w-full h-full rounded-lg bg-white backdrop:bg-gray/50">
            <div class="flex flex-col w-full p-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold">Maintenance Report</h1>
                </div>

                <div class="flex justify-between">
                    <form action="{{ route('report.maintenance') }}" method="GET" class="mb-6 flex gap-4 flex-wrap">

                        {{-- Reg ID Search --}}
                        <div class="relative">
                            <input type="text" name="reg_id" placeholder="Report by ID"
                                class="border border-gray-300 rounded px-4 py-1 pr-8" value="{{ request('reg_id') }}">
                            @if(request('reg_id'))
                                <a href="{{ route('report.maintenance', array_filter(request()->except('reg_id'))) }}"
                                    class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
                                    aria-label="Clear Reg ID Search">
                                    &times;
                                </a>
                            @endif
                        </div>

                        {{-- Date Search --}}
                        <div class="relative">
                            <input type="text" name="date" placeholder="Report by Date (YYYY-MM-DD)"
                                class="border border-gray-300 rounded px-4 py-1 pr-8" value="{{ request('date') }}">
                            @if(request('date'))
                                <a href="{{ route('report.maintenance', array_filter(request()->except('date'))) }}"
                                    class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
                                    aria-label="Clear Date Search">
                                    &times;
                                </a>
                            @endif
                        </div>

                        {{-- Optional: Search button if you don't want live submit --}}
                        <button type="submit" class="bg-green-800 text-white px-4 py-1 rounded">Search</button>
                    </form>
                </div>
                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
                {{--
                <pre>{{ $records }}</pre> --}}
                @php
                    $headers = ['Reg ID', 'Total Cost', 'Division','District', ''];
                    $rows = [];

                    foreach ($groupedRecords as $group) {
                        $regId = $group['vehicle_id'];
                        $cost =  number_format($group['total_cost'], 2);
                        $division = $group['division'] ?? 'N/A';
                        $district = $group['district'] ?? 'N/A';

                        $button = '<form method="POST" class="w-full flex justify-end" action="' . route('maintenance.report.pdf') . '" target="_blank">'
                            . csrf_field()
                            . '<input type="hidden" name="vehicle_id" value="' . $regId . '">'
                            . '<button type="submit" class="btn btn-sm bg-green-800 text-white">Generate Report</button>'
                            . '</form>';

                        $rows[] = [$regId, $cost, $division,$district, $button];
                    }
                @endphp

                <x-table :headers="$headers" :rows="$rows" />


            </div>
        </div>
    </div>

@endsection