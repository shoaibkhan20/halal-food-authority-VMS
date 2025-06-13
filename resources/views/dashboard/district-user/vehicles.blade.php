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
                <div class="min-h-[450px] min-w-[70%]">
                        <p>{{Auth::user()->branch->district}}</p>

                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-3xl font-bold">Vehicle Information</h1>
                    </div>
                    <div class="mb-6 relative w-full">
                        <form action="{{ route('district-user.vehicles') }}" method="GET">
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
                    @if($regIds->isEmpty())
                        <h1 class="text-center mt-5">No vehicles found </h1>
                    @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 ">
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
                    @endif
                </div>
            </div>
        </div>
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