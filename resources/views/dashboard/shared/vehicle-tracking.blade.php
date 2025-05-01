<!-- resources/views/super-admin/vehicle-info.blade.php -->
@extends('layouts.app')

@section('content')

    <div class="w-full min-h-screen grid place-items-center">
        <div class="w-[80%] h-full sm:h-[85vh] grid place-items-center rounded-lg bg-white backdrop:bg-gray/50">
            <div>
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold">Vehicle Information</h1>
                </div>
                <div class="mb-6">
                    <input type="text" placeholder="Search Reg.id" class="w-full border border-gray-300 rounded px-4 py-2">
                </div>
                {{-- vehicle info boxes --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                    @foreach($regIds as $vehicle)
                        <div onclick="window.location='{{ route('vehicle.details', ['regid' => $vehicle->RegID]) }}'"
                            class="cursor-pointer bg-green-800 text-white rounded-lg p-6 flex flex-col items-center shadow w-50">
                            <img src="{{ asset('images/truckicon.png') }}" alt="icon" class="max-w-20 h-auto">
                            <span class="text-md  ">ID: {{ $vehicle->RegID }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

@endsection