@extends('layouts.app')

@section('content')
    <div class="w-full min-h-screen flex items-center justify-center">
        <div class="w-[85%] h-full bg-white p-8 rounded-lg shadow-xl relative">
            <h2 class="text-2xl font-bold text-center mb-6">Vehicle Details</h2>
            @if (session('success'))
                <div class="bg-green-200 text-green-800 p-4 mb-4 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-200 text-red-800 p-4 mb-4 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif
            <table class="w-full text-sm min-h-[450px]">
                <tbody>
                    <tr class="border-b">
                        <td class="py-3 font-semibold w-1/2">Reg. ID</td>
                        <td class="py-3 w-1/2">{{ $vehicle->RegID }}</td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-3 font-semibold">Model</td>
                        <td class="py-3">{{ $vehicle->Model ?? 'N/A'}}</td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-3 font-semibold">Fuel Type</td>
                        <td class="py-3">{{ $vehicle->Fuel_type ??'N/A'}}</td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-3 font-semibold">Vehicle Type</td>
                        <td class="py-3">{{ $vehicle->Vehicle_Type ??'N/A'}}</td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-3 font-semibold">Average Mileage</td>
                        <td class="py-3">{{ $vehicle->Average_mileage ??'N/A'}}</td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-3 font-semibold">Region</td>
                        <td class="py-3">{{ $vehicle->branch->district ?? 'N/A'}}</td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-3 font-semibold">Branch ID</td>
                        <td class="py-3">{{ $vehicle->branch_id ?? 'N/A'}}</td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-3 font-semibold">Status</td>
                        <td class="py-3 capitalize">{{ $vehicle->status }}</td>
                    </tr>

                    @if ($vehicle->status === 'Assigned')
                        <tr class="border-b">
                            <td class="py-3 font-semibold">Assigned To</td>
                            <td class="py-3">{{ $assignment->user->name }}</td>
                        </tr>
                        <tr class="border-b">
                            <td class="py-3 font-semibold">Assignment Start</td>
                            <td class="py-3">{{ $assignment->assigned_date ?? 'N / A' }}</td>
                        </tr>
                        <tr class="border-b">
                            <td class="py-3 font-semibold">Assignment End</td>
                            <td class="py-3">{{ $assignment->returned_date ?? 'Ongoing' }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>

            @if(Auth::user()->role->role_name === 'super-admin')
                <div class="flex justify-end mt-6 gap-3">
                    @if ($vehicle->status === 'Assigned' && $assignment)
                    <form action="{{ route('vehicle.deallocate', $vehicle->RegID) }}" method="POST"
                        onsubmit="return confirm('Are you sure you want to de-allocate this vehicle?');" style="display:inline;">
                        @csrf
                        @method('PUT')
                        <button type="submit" class=" text-green-800 border-2 border-green-800 px-4 py-2 rounded hover:bg-green-800 hover:text-white">
                            De-allocate
                        </button>
                    </form>
                    @endif
                    <button onclick='openEditVehicleModal(`{!! addslashes(json_encode($vehicle)) !!}`)'
                        class="bg-green-800 text-white px-4 py-2 rounded hover:bg-green-700">
                        Edit
                    </button>
                    <form action="{{ route('vehicles.destroy', $vehicle->RegID) }}" method="POST"
                        onsubmit="return confirm('Are you sure you want to delete this vehicle?');" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-500">
                            Delete
                        </button>
                    </form>
                    

                </div>
            @endif

            <button onclick="window.history.back();"
                class="cursor-pointer absolute top-4 right-4 text-red-600 text-xl hover:text-red-800">
                &times;
            </button>
        </div>
    </div>

    <dialog id="edit_vehicle_modal" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box w-full max-w-2xl rounded-lg shadow-lg bg-white p-6">
            <form method="dialog">
                <button class="absolute right-4 top-4 text-gray-500 hover:text-red-500 text-xl">&times;</button>
            </form>
            <h3 class="text-2xl font-semibold text-green-900 mb-6 text-center">Update Vehicle</h3>

            <form id="update-vehicle-form" method="POST" action="{{ route('vehicles.update', $vehicle->RegID) }}">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-2 gap-4">
                    <!-- Registration ID (Read-only) -->
                    <div>
                        <label class="block mb-1 text-sm text-gray-700">Registration ID</label>
                        <input type="text" id="update-regid" name="RegID" readonly
                            class="input input-bordered w-full bg-gray-100" />
                    </div>

                    <!-- Model -->
                    <div>
                        <label class="block mb-1 text-sm text-gray-700">Model</label>
                        <input type="text" id="update-model" name="Model" required class="input input-bordered w-full" />
                    </div>

                    <!-- Fuel Type -->
                    <div>
                        <label class="block mb-1 text-sm text-gray-700">Fuel Type</label>
                        <select name="Fuel_type" id="update-fuel" required class="select select-bordered w-full">
                            <option value="">Select</option>
                            <option value="Petrol">Petrol</option>
                            <option value="Diesel">Diesel</option>
                            <option value="Diesel">CNG GAS</option>
                            <option value="Electric">Electric</option>
                            <option value="Hybrid">Hybrid</option>
                        </select>
                    </div>

                    <!-- Vehicle Type -->
                    <div>
                        <label class="block mb-1 text-sm text-gray-700">Vehicle Type</label>
                        <input list="types" type="text" id="update-type" name="Vehicle_Type" required
                            class="input input-bordered w-full" />
                        <datalist id="types">
                            @foreach ($vehicleTypes as $type)
                                <option value={{ $type->name }}>{{$type->name }}</option>
                            @endforeach
                        </datalist>
                    </div>

                    <!-- Branch -->
                    <div>
                        <label class="block mb-1 text-sm text-gray-700">Branch</label>
                        <select name="branch_id" id="update-branch" class="select select-bordered w-full">
                            <option disabled selected>Select Branch</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Mileage -->
                    <div>
                        <label class="block mb-1 text-sm text-gray-700">Average Mileage</label>
                        <input type="number" step="0.01" name="Average_mileage" id="update-mileage"
                            class="input input-bordered w-full" />
                    </div>
                </div>

                <!-- Submit -->
                <div class="text-right pt-4">
                    <button type="submit" class="btn bg-green-800 text-white">Update Vehicle</button>
                </div>
            </form>
        </div>
    </dialog>

    <script>
        function openEditVehicleModal(vehicleJson) {
            const vehicle = JSON.parse(vehicleJson);

            const form = document.getElementById('update-vehicle-form');

            document.getElementById('update-regid').value = vehicle.RegID || '';
            document.getElementById('update-model').value = vehicle.Model || '';
            document.getElementById('update-fuel').value = vehicle.Fuel_type || '';
            document.getElementById('update-type').value = vehicle.Vehicle_Type || '';
            document.getElementById('update-branch').value = vehicle.branch_id || '';
            document.getElementById('update-mileage').value = vehicle.Average_mileage || '';

            document.getElementById('edit_vehicle_modal').showModal();
        }
    </script>

@endsection