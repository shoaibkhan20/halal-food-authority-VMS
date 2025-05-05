<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Super Admin Dashboard</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="flex min-h-screen bg-gray-100">

    <!-- Sidebar -->
    <aside class="w-64 bg-green-900 text-white flex flex-col items-center py-6">
        <a href="{{ route('dashboard') }}"> <img src="{{ asset('images/logo.png') }}" class="h-20 mb-6" alt="Logo"></a>
        <nav class="space-y-4 font-medium w-full px-6">
            <a href="{{ route('vehicles.info') }}"
                class="border-b block py-2 px-4  {{ request()->routeIs('vehicles.info') || request()->routeIs('vehicle.details') ? 'bg-white text-green-800' : 'hover:bg-green-800' }}">Vehicle
                Information</a>
            <a href="{{ route('vehicle.tracking') }}"
                class="border-b block py-2 px-4  {{ request()->routeIs('vehicle.tracking') || request()->routeIs('vehicle.liveLocation') ? 'bg-white text-green-800' : 'hover:bg-green-800' }}">Vehicle
                Tracking</a>
            <a href="{{ route('vehicle.maintenance') }}"
                class="border-b block py-2 px-4  {{ request()->routeIs('vehicle.maintenance') ? 'bg-white text-green-800' : 'hover:bg-green-800' }}">Maintenance
                History</a>
            <a href="{{ route('users.role-management') }}"
                class="border-b block py-2 px-4  {{ request()->routeIs('users.role-management') ? 'bg-white text-green-800' : 'hover:bg-green-800' }}">User
                Role Management</a>
            {{-- <a href="#" class="block py-2 px-4 hover:bg-green-800 ">Reports</a> --}}

            @php
                $isReportOpen = request()->routeIs('report.vehicle-status') || request()->routeIs('report.maintenance') || request()->routeIs('reports');
            @endphp

            <div class="w-full ">
                <a href="{{ route('reports') }}"
                    class="cursor-pointer border-b w-full text-left py-2 px-4  font-medium flex justify-between items-center {{ $isReportOpen ? 'bg-white text-green-800 hover:bg-white' : 'hover:bg-green-800 ' }}">
                    Reports
                    <svg class="w-4 h-4 ml-2 transform transition-transform cursor-pointer " id="report-arrow" onclick="document.getElementById('report-submenu').classList.toggle('hidden')"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </a>
                <div id="report-submenu" class="space-y-1 mt-1 ml-2 {{ $isReportOpen ? '' : 'hidden' }}">
                    <a href="{{ route('report.vehicle-status') }}"
                        class="block py-2 px-4  hover:bg-green-800 text-sm {{ request()->routeIs('report.vehicle-status') ? 'bg-white text-green-800' : '' }}">
                        Vehicle Status Report
                    </a>
                    <a href="{{ route('report.maintenance') }}"
                        class="block py-2 px-4  hover:bg-green-800 text-sm {{ request()->routeIs('report.maintenance') ? 'bg-white text-green-800' : '' }}">
                        Maintenance Report
                    </a>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="block">
                @csrf
                <button type="submit" class="border-b w-full text-left py-2 px-4 hover:bg-green-800 ">
                    Logout
                </button>
            </form>
        </nav>
    </aside>
    <!-- Main Content -->
    <main class="flex-1">
        <!-- Background Image -->
        <div class="w-full h-full bg-cover bg-bottom relative overflow-y-auto"
            >
            @yield('content')
        </div>
    </main>
</body>
<script src="{{ asset('js/daisyui.js') }}">
</html >