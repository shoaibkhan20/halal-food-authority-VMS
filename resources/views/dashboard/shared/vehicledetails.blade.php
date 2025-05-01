@extends('layouts.app')

@section('content')
    <div class="w-full min-h-screen flex items-center justify-center">
        <div class="w-[85%] h-full bg-white p-8 rounded-lg shadow-xl  relative">
            <h2 class="text-2xl font-bold text-center mb-6">Vehicle Details</h2>

            <table class="w-full text-sm">
                <tbody>
                    <tr class="border-b">
                        <td class="py-3 font-semibold w-1/2">Reg. ID</td>
                        <td class="py-3 w-1/2">{{ $vehicle->RegID }}</td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-3 font-semibold">Model</td>
                        <td class="py-3">{{ $vehicle->Model }}</td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-3 font-semibold">Fuel Type</td>
                        <td class="py-3">{{ $vehicle->Fuel_type }}</td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-3 font-semibold">Vehicle Type</td>
                        <td class="py-3">{{ $vehicle->Vehicle_Type }}</td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-3 font-semibold">Status</td>
                        <td class="py-3">{{ $vehicle->status }}</td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-3 font-semibold">Region</td>
                        <td class="py-3">{{ $vehicle->Region }}</td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-3 font-semibold">Branch ID</td>
                        <td class="py-3">{{ $vehicle->branch_id }}</td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-3 font-semibold">Average Mileage</td>
                        <td class="py-3">{{ $vehicle->Average_mileage }}</td>
                    </tr>
                </tbody>
            </table>

            @if(Auth::user()->role->role_name === 'super-admin')
                {{-- Your Blade elements go here --}}
                <div class="flex justify-end mt-6 gap-3">
                    <button class="bg-green-800 text-white px-4 py-2 rounded hover:bg-green-700">
                        Edit
                    </button>
                    <button class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-500">
                        Delete
                    </button>
                </div>
            @endif


            <button onclick="history.back()"
                class="cursor-pointer absolute top-4 right-4 text-red-600 text-xl hover:text-red-800">
                &times;
            </button>
        </div>
    </div>
@endsection