<!-- resources/views/super-admin/vehicle-info.blade.php -->
@extends('layouts.app')

@section('content')

    @if (request('search'))
        <div class="w-full min-h-screen flex items-start justify-center">
            <div class="w-[85%] h-full bg-white p-8 rounded-lg shadow-xl relative">
                <h2 class="text-2xl font-bold text-center mb-6">Seached Vehicles</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                    @foreach($regIds as $vehicle)
                        <div>
                            <button onclick="window.location='{{ route('vehicle.details', ['regid' => $vehicle->RegID]) }}'"
                                class="cursor-pointer bg-green-800 text-white rounded-lg p-6 flex flex-col items-center shadow w-50">
                                @if ($vehicle->Vehicle_Type === 'Mobile_lab')
                                    <img src="{{  asset('images/truckicon.png') }}" alt="icon" class="max-w-20 h-auto">
                                @else
                                    <img src="{{  asset('images/caricon.png') }}" alt="icon" class="max-w-20 h-auto">
                                @endif
                                <span class="text-md  ">ID: {{ $vehicle->RegID }}</span>
                            </button>
                        </div>
                    @endforeach
                </div>
                <button onclick="window.history.back()"
                    class="cursor-pointer absolute top-4 right-4 text-red-600 text-xl hover:text-red-800">
                    &times;
                </button>
            </div>
        </div>
    @else
        <div class="w-full min-h-screen grid place-items-center">
            <div class="w-full h-full grid place-items-center rounded-lg bg-white">
                <div class="min-h-[450px]">
                    <div class="flex justify-between items-center mb-6">
                        
                        <h1 class="text-3xl font-bold">Vehicle Information</h1>
                        @if(Auth::user()->role->role_name === 'super-admin')
                            <div>
                                <button onclick="assign_modal.showModal()"
                                    class="border cursor-pointer bg-white text-black px-4 py-2 rounded">
                                    Assign Vehicle
                                </button>
                                <button onclick="my_modal_3.showModal()"
                                    class="cursor-pointer bg-green-800 text-white px-4 py-2 rounded">
                                    Add New Vehicle
                                </button>
                            </div>
                        @endif
                    </div>
                    <div class="mb-6 relative w-full">
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
                        <form action="{{ route('vehicles.info') }}" method="GET">
                            <input id="searchInput" name="search" type="text" autocomplete="off" placeholder="Search Reg.id"
                                class="w-full border border-gray-300 rounded px-4 py-2" oninput="filterSearchDropdown()">
                            <ul id="searchDropdown"
                                class="absolute z-10 bg-white border border-gray-300 rounded mt-1 w-full max-h-40 overflow-y-auto shadow hidden">
                                @foreach ($regIds as $vehicle)
                                    <li class="px-4 py-2 hover:bg-gray-100 cursor-pointer"
                                        onclick="selectSearchItem('{{ $vehicle->RegID }}')">
                                        {{ $vehicle->RegID }}
                                    </li>
                                @endforeach
                            </ul>
                        </form>

                    </div>
                    {{-- vehicle info boxes --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                        @foreach($regIds->take(6) as $vehicle)
                            <div onclick="window.location='{{ route('vehicle.details', ['regid' => $vehicle->RegID]) }}'"
                                class="cursor-pointer bg-green-800 text-white rounded-lg p-6 flex flex-col items-center shadow w-50">
                                @if ($vehicle->Vehicle_Type === 'Mobile_lab')
                                    <img src="{{  asset('images/truckicon.png') }}" alt="icon" class="max-w-20 h-auto">
                                @else
                                    <img src="{{  asset('images/caricon.png') }}" alt="icon" class="max-w-20 h-auto">
                                @endif
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

                <form method="POST" action="{{ route('vehicles.store') }}" class="space-y-4">
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
                                <option>CNG Gas</option>
                                <option>Electric</option>
                                <option>Hybrid</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Vehicle Type</label>
                            <select name="Vehicle_Type" class="select select-bordered w-full">
                                <option value="" hidden>Select Type</option>
                                @foreach($vehicleTypes as $type)
                                    <option value="{{ $type->name }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Branch</label>
                            <select name="branch_id" class="select select-bordered w-full">
                                <option value="" hidden>Select Branch</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }} ({{ $branch->location }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Average Mileage (km/l)</label>
                            <input name="Average_mileage" type="number" step="0.01" class="input input-bordered w-full" />
                        </div>
                    </div>
                    <div class="text-right pt-4">
                        <button type="submit" class="btn bg-green-800 text-white">Add Vehicle</button>
                    </div>
                </form>
            </div>
        </dialog>

        <dialog id="assign_modal" class="modal">
            <div class="modal-box max-w-2xl">
                <!-- Close Button -->
                <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                </form>

                <h3 class="text-2xl font-semibold mb-4 text-center">Assign Vehicle to User</h3>

                <form method="POST" action="{{ route('vehicle.assign') }}" class="space-y-6">
                    @csrf

                    <!-- Vehicle Searchable Dropdown -->
                    <div class="relative">
                        <label class="block text-sm font-medium mb-1">Vehicle (RegID)</label>
                        <input id="vehicleInput" name="vehicle_id" type="text" required autocomplete="off"
                            placeholder="Search vehicle..." class="input input-bordered w-full"
                            oninput="filterDropdown(this, 'vehicleList')">
                        <ul id="vehicleList"
                            class="z-10 w-full absolute dropdown-list hidden bg-white border border-gray-200 mt-1 rounded shadow-md max-h-40 overflow-y-auto">
                            @foreach($availableVehicles as $vehicle)
                                <li class="dropdown-item px-4 py-2 hover:bg-gray-100 cursor-pointer"
                                    onclick="selectDropdown('vehicleInput', '{{ $vehicle->RegID }}')">
                                    {{ $vehicle->RegID }} - {{ $vehicle->Model }}
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- User Searchable Dropdown -->
                    <div class="relative ">
                        <label class="block text-sm font-medium mb-1">Assign To (User ID)</label>
                        <input id="userInput" name="user_id" type="text" required autocomplete="off"
                            placeholder="Search user..." class="input input-bordered w-full"
                            oninput="filterDropdown(this, 'userList')">
                        <ul id="userList"
                            class="w-full dropdown-list hidden bg-white border border-gray-200 mt-1 rounded shadow-md max-h-40 overflow-y-auto">
                            @foreach($users as $user)
                                <li class="dropdown-item px-4 py-2 hover:bg-gray-100 cursor-pointer"
                                    onclick="selectDropdown('userInput', '{{ $user->id }}')">
                                    {{ $user->id }} - {{ $user->name }}
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="text-right pt-2">
                        <button type="submit" class="btn bg-green-800 text-white">Assign</button>
                    </div>
                </form>
            </div>
        </dialog>
    @endif
    <!-- Tailwind-friendly minimal JS -->
    <script>
        function filterDropdown(input, listId) {
            const filter = input.value.toLowerCase();
            const dropdown = document.getElementById(listId);
            const items = dropdown.querySelectorAll('li');

            let hasVisible = false;
            items.forEach(item => {
                const match = item.textContent.toLowerCase().includes(filter);
                item.style.display = match ? 'block' : 'none';
                if (match) hasVisible = true;
            });

            dropdown.classList.toggle('hidden', !hasVisible);
        }

        function selectDropdown(inputId, value) {
            const input = document.getElementById(inputId);
            input.value = value;
            const dropdown = input.nextElementSibling;
            dropdown.classList.add('hidden');
        }

        // Close dropdown if clicked outside
        document.addEventListener('click', function (event) {
            const dropdowns = document.querySelectorAll('.dropdown-list');
            dropdowns.forEach(dropdown => {
                if (!dropdown.previousElementSibling.contains(event.target)) {
                    dropdown.classList.add('hidden');
                }
            });
        });

        //
        const searchInput = document.getElementById('searchInput');
        const searchDropdown = document.getElementById('searchDropdown');
        function filterSearchDropdown() {
            const filter = searchInput.value.toLowerCase();
            const items = searchDropdown.querySelectorAll('li');
            let anyVisible = false;

            items.forEach(item => {
                const text = item.textContent.toLowerCase();
                const visible = text.includes(filter);
                item.style.display = visible ? 'block' : 'none';
                if (visible) anyVisible = true;
            });
            searchDropdown.classList.toggle('hidden', !anyVisible);
        }
        function selectSearchItem(value) {
            searchInput.value = value;
            searchDropdown.classList.add('hidden');
            document.getElementById('searchForm').submit(); // auto-submit form
        }

        // Hide dropdown on click outside
        document.addEventListener('click', function (e) {
            if (!searchInput.contains(e.target) && !searchDropdown.contains(e.target)) {
                searchDropdown.classList.add('hidden');
            }
        });
    </script>




@endsection