<!-- resources/views/super-admin/vehicle-info.blade.php -->
@extends('layouts.app')

@section('content')

    <div class="w-full min-h-screen grid place-items-center">
        <div class="w-[80%] h-full sm:h-[85vh] grid place-items-center rounded-lg bg-white backdrop:bg-gray/50">
            <div>
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold">Vehicle Information</h1>
                    @if(Auth::user()->role->role_name === 'super-admin')
                        <button onclick="my_modal_3.showModal()"
                            class="cursor-pointer bg-green-800 text-white px-4 py-2 rounded">
                            AddVehicle
                        </button>
                    @endif
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

    <dialog id="my_modal_3" class="modal">
        <div class="modal-box max-w-2xl">
            <!-- Close Button -->
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
            </form>

            <h3 class="text-2xl font-semibold mb-4 text-center">Add New Vehicle</h3>

            <form method="POST" class="space-y-4">
                @csrf

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Registration ID</label>
                        <input name="RegID" required type="text" class="input input-bordered w-full" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Model</label>
                        <input name="Model" required type="text" class="input input-bordered w-full" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Fuel Type</label>
                        <select name="Fuel_type" required class="select select-bordered w-full">
                            <option value="">Select</option>
                            <option>Petrol</option>
                            <option>Diesel</option>
                            <option>Electric</option>
                            <option>Hybrid</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Vehicle Type</label>
                        <input name="Vehicle_Type" required type="text" class="input input-bordered w-full" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Region</label>
                        <input name="Region" required type="text" class="input input-bordered w-full" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Branch ID</label>
                        <input name="branch_id" type="number" class="input input-bordered w-full" />
                    </div>

                    <div class="col-span-2">
                        <label class="block text-sm font-medium mb-1">Average Mileage</label>
                        <input name="Average_mileage" type="number" step="0.01" class="input input-bordered w-full" />
                    </div>
                </div>

                <div class="text-right pt-4">
                    <button type="submit" class="btn bg-green-800 text-white">Add Vehicle</button>
                </div>
            </form>
        </div>
    </dialog>


    {{-- <button class="btn" onclick="my_modal_3.showModal()">open modal</button>
    <dialog id="my_modal_3" class="modal">
        <div class="modal-box">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
            </form>
            <h2 class="text-xl font-bold mb-4">Vehicle Details</h2>
            <ul class="text-gray-700 space-y-2 text-sm">
                <li><strong>Registration ID:</strong> <span id="regId"></span></li>
                <li><strong>Model:</strong> <span id="model"></span></li>
                <li><strong>Fuel Type:</strong> <span id="fuel"></span></li>
                <li><strong>Vehicle Type:</strong> <span id="type"></span></li>
                <li><strong>Region:</strong> <span id="region"></span></li>
                <li><strong>Average Mileage:</strong> <span id="mileage"></span></li>
            </ul>
        </div>
    </dialog>

    {{-- Script to handle modal --}}

    {{--
    <script>
        function openVehicleModal(vehicle) {
            const modal = document.getElementById('my_modal_3');
            document.getElementById('regId').textContent = vehicle.reg_id;
            document.getElementById('model').textContent = vehicle.model;
            document.getElementById('fuel').textContent = vehicle.fuel;
            document.getElementById('type').textContent = vehicle.type;
            document.getElementById('region').textContent = vehicle.region;
            document.getElementById('mileage').textContent = vehicle.mileage;
            modal.showModal();
        }
    </script> --}}


@endsection