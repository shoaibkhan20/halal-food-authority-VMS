@extends('layouts.app')

@section('content')
    <div class="w-full min-h-screen flex items-start justify-center">
        <div class="w-[85%] h-full bg-white p-8 rounded-lg shadow-xl relative">
            <h2 class="text-2xl font-bold text-center mb-6">Seached Vehicles</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                @foreach($vehicles as $vehicle)
                 <div>
                    <button
                        class="cursor-pointer bg-green-800 text-white rounded-lg p-6 flex flex-col items-center shadow w-50">
                        @if ($vehicle->Vehicle_Type === 'Mobile_lab')
                            <img src="{{  asset('images/truckicon.png') }}" alt="icon" class="max-w-20 h-auto">
                        @else
                            <img src="{{  asset('images/caricon.png') }}" alt="icon" class="max-w-20 h-auto">
                        @endif
                        <span class="text-md  ">ID: {{ $vehicle->RegID }}</span>
                    </button>
                    <details class="dropdown">
                            <summary class="btn m-1">view</summary>
                            <ul class="menu dropdown-content bg-base-100 rounded-box z-1 w-52 p-2 shadow-sm text-black">
                                <li><a href="{{ route('vehicle.details', ['regid' => $vehicle->RegID]) }}">Details</a></li>
                                <li><a href="{{ route('vehicle.tracking', ['regid' => $vehicle->RegID]) }}">Live Location</a></li>
                            </ul>
                        </details>
                    </div>
                @endforeach
            </div>
            <button onclick="window.history.back()"
                class="cursor-pointer absolute top-4 right-4 text-red-600 text-xl hover:text-red-800">
                &times;
            </button>
        </div>
    </div>



    <script>

    </script>

@endsection