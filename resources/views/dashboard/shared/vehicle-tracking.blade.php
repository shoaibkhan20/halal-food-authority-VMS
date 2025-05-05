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
                    @foreach($locations as $vehicle)
                        <button onclick="showLiveLocationModal({{ $vehicle->latitude }}, {{ $vehicle->longitude }}, {{ $vehicle->speed }})"
                            class="cursor-pointer bg-green-800 text-white rounded-lg p-6 flex flex-col items-center shadow w-50">
                            <img src="{{ asset('images/truckicon.png') }}" alt="icon" class="max-w-20 h-auto">
                            <span class="text-md  ">ID: {{ $vehicle->vehicle_id }}</span>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <dialog id="my_modal_3" class="modal relative">
        <!-- Close Button Outside Modal Box -->
        <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost bg-green-800 absolute right-2 top-2 z-50">✕</button>
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
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyATGxyJb74dBUuIy5ibEOUdqJgcfU71jQI"></script>

    <script>
        let map;
        let marker;
        function initMap(lat, lng) {
            const position = { lat: parseFloat(lat), lng: parseFloat(lng) };
            if (!map) {
                map = new google.maps.Map(document.getElementById("map"), {
                    zoom: 15,
                    center: position,
                });
                marker = new google.maps.Marker({
                    position: position,
                    map: map,
                });
            } else {
                map.setCenter(position);
                marker.setPosition(position);
            }
        }
        // Call this function dynamically when opening modal
        function showLiveLocationModal(lat, lng, speed) {
            document.getElementById("lat").textContent = lat;
            document.getElementById("lng").textContent = lng;
            document.getElementById("speed").textContent = speed;
            initMap(lat, lng);
            // Show modal
            const modal = document.getElementById("my_modal_3");
            if (modal.showModal) modal.showModal();
        }
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
                modal.close(); // Close modal on outside click
            }
        });

    </script>





@endsection