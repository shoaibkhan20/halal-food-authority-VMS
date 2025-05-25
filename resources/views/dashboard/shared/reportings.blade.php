@extends('layouts.app')
@section('content')

<div class="w-full min-h-screen grid place-items-center">
    <div class="w-full h-full rounded-lg bg-white backdrop:bg-gray/50">
        <div class="flex flex-col w-full p-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold">Reporting</h1>
            </div>
            <div class="flex justify-between">
                <div class="mb-6 relative w-full max-w-xs">
                        <form action="{{ route('reports') }}" method="GET">
                            <input 
                                type="text" 
                                id="filterSearch" 
                                name="search" 
                                placeholder="Search Reg.id" 
                                class="border border-gray-300 rounded px-4 py-2 w-full pr-8" 
                                value="{{ request('search') }}"
                            >
                            @if(request('search'))
                            <a href="{{ route('reports') }}" 
                            class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
                            aria-label="Clear search"
                            >
                                &times;
                            </a>
                            @endif
                        </form>
                    </div>

                <div class="mb-6">
                    <select class="border border-gray-300 rounded px-4 py-2">
                        <option>Last Month</option>
                        <option>Last 3 Months</option>
                        <option>All Time</option>
                    </select>
                </div>
            </div>
            {{-- Reporting Table --}}
          
            @php
                $headers = ['Reg ID', 'Model', 'Assigned To', 'Status', 'Location'];
                $rows = $vehicles->toArray();
            @endphp

            <x-table :headers="$headers" :rows="$rows" />

        </div>
    </div>
</div>

@endsection