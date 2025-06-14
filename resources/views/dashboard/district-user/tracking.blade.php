<!-- resources/views/super-admin/vehicle-info.blade.php -->
@extends('layouts.app')
@section('content')
    @if (request('search'))
        <div class="w-full min-h-screen flex items-start justify-center">
            <div class="w-[85%] h-full bg-white p-8 rounded-lg shadow-xl relative">
                <h2 class="text-2xl font-bold text-center mb-6">Seached Vehicles</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                    @foreach($vehicles as $vehicle)
                        @if($vehicle->latestLocation)
                            <button
                               onclick="showLiveLocationModal('{{ $vehicle->RegID }}', '{{ $vehicle->latestLocation->latitude }}', '{{ $vehicle->latestLocation->longitude }}', '{{ $vehicle->latestLocation->speed }}')"

                                class="cursor-pointer bg-green-800 text-white rounded-lg p-6 flex flex-col items-center shadow w-50">
                                @if ($vehicle->Vehicle_Type === 'Mobile_lab')
                                    <img src="{{  asset('images/truckicon.png') }}" alt="icon" class="max-w-20 h-auto">
                                @else
                                    <img src="{{  asset('images/caricon.png') }}" alt="icon" class="max-w-20 h-auto">
                                @endif
                                <span class="text-md  ">ID: {{ $vehicle->RegID }}</span>
                            </button>
                            @else
                            <button
                               onclick="alert('No LiveLocation Found')"
                                class="cursor-pointer bg-green-800 text-white rounded-lg p-6 flex flex-col items-center shadow w-50">
                                @if ($vehicle->Vehicle_Type === 'Mobile_lab')
                                    <img src="{{  asset('images/truckicon.png') }}" alt="icon" class="max-w-20 h-auto">
                                @else
                                    <img src="{{  asset('images/caricon.png') }}" alt="icon" class="max-w-20 h-auto">
                                @endif
                                <span class="text-md  ">ID: {{ $vehicle->RegID }}</span>
                            </button>
                            @endif
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
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-3xl font-bold">Vehicle Information</h1>
                    </div>
                    <div class="mb-6 relative w-full">
                        <form action="{{ route('divisional-user.vehicles.tracking') }}" method="GET">
                            <input id="searchInput" name="search" type="text" autocomplete="off" placeholder="Search Reg.id"
                                class="w-full border border-gray-300 rounded px-4 py-2" oninput="filterSearchDropdown()">
                            <ul id="searchDropdown"
                                class="absolute z-10 bg-white border border-gray-300 rounded mt-1 w-full max-h-40 overflow-y-auto shadow hidden">
                                @foreach ($vehicles as $vehicle)
                                    <li class="px-4 py-2 hover:bg-gray-100 cursor-pointer"
                                        onclick="selectSearchItem('{{ $vehicle->RegID }}')">
                                        {{ $vehicle->RegID }}
                                    </li>
                                @endforeach
                            </ul>
                        </form>
                    </div>
                    {{-- vehicle info boxes --}}
                    @if($vehicles->isEmpty())
                        <h1 class="text-center mt-5">No vehicles found </h1>
                    @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                        @foreach($vehicles->take(6) as $vehicle)
                            @if($vehicle->latestLocation)
                            <button
                               onclick="showLiveLocationModal('{{ $vehicle->RegID }}', '{{ $vehicle->latestLocation->latitude }}', '{{ $vehicle->latestLocation->longitude }}', '{{ $vehicle->latestLocation->speed }}')"

                                class="cursor-pointer bg-green-800 text-white rounded-lg p-6 flex flex-col items-center shadow w-50">
                                @if ($vehicle->Vehicle_Type === 'Mobile_lab')
                                    <img src="{{  asset('images/truckicon.png') }}" alt="icon" class="max-w-20 h-auto">
                                @else
                                    <img src="{{  asset('images/caricon.png') }}" alt="icon" class="max-w-20 h-auto">
                                @endif
                                <span class="text-md  ">ID: {{ $vehicle->RegID }}</span>
                            </button>
                            @else
                            <button
                               onclick="alert('No LiveLocation Found')"
                                class="cursor-pointer bg-green-800 text-white rounded-lg p-6 flex flex-col items-center shadow w-50">
                                @if ($vehicle->Vehicle_Type === 'Mobile_lab')
                                    <img src="{{  asset('images/truckicon.png') }}" alt="icon" class="max-w-20 h-auto">
                                @else
                                    <img src="{{  asset('images/caricon.png') }}" alt="icon" class="max-w-20 h-auto">
                                @endif
                                <span class="text-md  ">ID: {{ $vehicle->RegID }}</span>
                            </button>
                            @endif
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
    <dialog id="my_modal_3" class="modal fixed top-0 hidden">
        <!-- Close Button Outside Modal Box -->
        <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost bg-green-800 text-white absolute right-2 top-2 z-50">✕</button>
        </form>
        <div class="modal-box" style="width: 800px; height: 550px; max-width: none;">
            <!-- Map Section -->
            <div id="map" class="w-full rounded" style="height: 350px;"></div>

            <!-- Bottom Section: Info Table -->
            <div class="" style="height: 150px;">
                <table class="table table-zebra w-full text-sm">
                    <thead>
                        <tr class="bg-gray-200 text-gray-800">
                            <th>Latitude</th>
                            <th>Longitude</th>
                            <th>Speed (km/h)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td id="lat">-</td>
                            <td id="lng">-</td>
                            <td id="speed">-</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </dialog>

    <!-- Google Maps API Script (replace YOUR_API_KEY) -->
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}"></script>
    <script>
        let map;
        let marker;
        let liveInterval = null;

        // Initialize map and marker
        function initMap(lat, lng) {
            const position = { lat: parseFloat(lat), lng: parseFloat(lng) };
            map = new google.maps.Map(document.getElementById("map"), {
                zoom: 15,
                center: position,
            });
            marker = new google.maps.Marker({
                position: position,
                map: map,
            });
        }

        // Update marker without recreating map
        function updateMap(lat, lng) {
            const newPos = { lat: parseFloat(lat), lng: parseFloat(lng) };
            if (marker) {
                marker.setPosition(newPos);
                map.setCenter(newPos);
            }
        }

        // Fetch latest location
        async function fetchLiveLocation(vehicleId) {
            console.log(vehicleId)
            try {
                const res = await fetch(`/api/vehicle/${vehicleId}/location`);
                const data = await res.json();
                if (data.status === 'success') {
                    return data.data;
                }
            } catch (err) {
                console.error('Error fetching location:', err);
            }
        }

        // Open modal and start real-time tracking
        function showLiveLocationModal(vehicleId, lat, lng, speed) {
            // Display initial values
            document.getElementById("lat").textContent = lat;
            document.getElementById("lng").textContent = lng;
            document.getElementById("speed").textContent = speed;
            initMap(lat, lng);

            const modal = document.getElementById("my_modal_3");
            modal.classList.remove('hidden');
            if (modal.showModal) {
                modal.showModal();
            }

            // Clear any previous interval
            if (liveInterval) clearInterval(liveInterval);

            // Start polling every 2 seconds
            liveInterval = setInterval(async () => {
                const data = await fetchLiveLocation(vehicleId);
                if (data) {
                    document.getElementById("lat").textContent = data.latitude;
                    document.getElementById("lng").textContent = data.longitude;
                    document.getElementById("speed").textContent = data.speed;
                    updateMap(data.latitude, data.longitude);
                }
            }, 2000);
        }

        // Handle modal close on outside click
        const modal = document.getElementById('my_modal_3');
        modal.addEventListener('click', function (event) {
            const rect = modal.querySelector('.modal-box').getBoundingClientRect();
            const isInDialog = (
                event.clientX >= rect.left &&
                event.clientX <= rect.right &&
                event.clientY >= rect.top &&
                event.clientY <= rect.bottom
            );
            if (!isInDialog) {
                modal.close(); // Close modal
                modal.classList.add('hidden');
                if (liveInterval) {
                    clearInterval(liveInterval);
                    liveInterval = null;
                }
            }
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