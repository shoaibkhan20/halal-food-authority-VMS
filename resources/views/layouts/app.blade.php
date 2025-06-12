<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- <title>{{Auth::User()->role()->role_name()}} Dashboard</title> --}}
    <title>{{ ucfirst(Auth::user()?->role?->role_name) ?? 'Dashboard' }} Dashboard</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="flex min-h-screen bg-gray-100">
    <!-- Sidebar -->
    <aside id="menu"
        class="hidden md:flex sticky left-0 top-0 w-64 h-screen bg-green-900 text-white  flex-col items-center py-6">
        <a href="{{ route('home') }}"> <img src="{{ asset('images/logo.png') }}" class="h-20 mb-6" alt="Logo"></a>
        @if(Auth::User()->role->role_name == 'super-admin')
            <nav class="space-y-4 font-medium w-full px-6">
                <a href="{{ route('vehicles.info') }}"
                    class="border-b block py-2 px-4  {{ request()->routeIs('vehicles.info') || request()->routeIs('vehicle.details') ? 'bg-white text-green-800' : 'hover:bg-green-800' }}">Vehicle
                    Information</a>
                <a href="{{ route('vehicle.tracking') }}"
                    class="border-b block py-2 px-4  {{ request()->routeIs('vehicle.tracking') || request()->routeIs('vehicle.liveLocation') ? 'bg-white text-green-800' : 'hover:bg-green-800' }}">Vehicle
                    Tracking</a>
                <a href="{{ route('vehicle.maintenance') }}"
                    class="border-b block py-2 px-4  {{ request()->routeIs('vehicle.maintenance') ? 'bg-white text-green-800' : 'hover:bg-green-800' }}">Maintenance
                </a>

                <a href="{{ route('users.role-management') }}"
                    class="border-b block py-2 px-4  {{ request()->routeIs('users.role-management') ? 'bg-white text-green-800' : 'hover:bg-green-800' }}">User
                    Role Management</a>
                @php
                    $isReportOpen = request()->routeIs('report.vehicle-status') || request()->routeIs('report.maintenance') || request()->routeIs('reports');
                @endphp
                <div class="w-full ">
                    <a href="{{ route('reports') }}"
                        class="cursor-pointer border-b w-full text-left py-2 px-4  font-medium flex justify-between items-center {{ $isReportOpen ? 'bg-white text-green-800 hover:bg-white' : 'hover:bg-green-800 ' }}">
                        Reports
                        <svg class="w-4 h-4 ml-2 transform transition-transform cursor-pointer " id="report-arrow"
                            onclick="document.getElementById('report-submenu').classList.toggle('hidden')"
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
                <form id="logout-form" class="block">
                    @csrf
                    <button onclick="LoginHandler();"
                        class="cursor-pointer border-b w-full text-left py-2 px-4 hover:bg-green-800 ">
                        Logout
                    </button>
                </form>
            </nav>
        @elseif (Auth::User()->role->role_name == 'director-admin')
            <nav class="space-y-4 font-medium w-full px-6">
                <a href="{{ route('vehicles.info') }}"
                    class="border-b block py-2 px-4  {{ request()->routeIs('vehicles.info') || request()->routeIs('vehicle.details') ? 'bg-white text-green-800' : 'hover:bg-green-800' }}">Vehicle
                    Information</a>
                <a href="{{ route('vehicle.tracking') }}"
                    class="border-b block py-2 px-4  {{ request()->routeIs('vehicle.tracking') || request()->routeIs('vehicle.liveLocation') ? 'bg-white text-green-800' : 'hover:bg-green-800' }}">Vehicle
                    Tracking</a>
                <a href="{{ route('vehicle.maintenance') }}"
                    class="border-b block py-2 px-4  {{ request()->routeIs('vehicle.maintenance') ? 'bg-white text-green-800' : 'hover:bg-green-800' }}">Maintenance</a>
                @php
                    $isReportOpen = request()->routeIs('report.vehicle-status') || request()->routeIs('report.maintenance') || request()->routeIs('reports');
                @endphp

                <div class="w-full ">
                    <a href="{{ route('reports') }}"
                        class="cursor-pointer border-b w-full text-left py-2 px-4  font-medium flex justify-between items-center {{ $isReportOpen ? 'bg-white text-green-800 hover:bg-white' : 'hover:bg-green-800 ' }}">
                        Reports
                        <svg class="w-4 h-4 ml-2 transform transition-transform cursor-pointer " id="report-arrow"
                            onclick="document.getElementById('report-submenu').classList.toggle('hidden')"
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
                <form id="logout-form" class="block">
                    @csrf
                    <button onclick="LoginHandler();"
                        class="cursor-pointer border-b w-full text-left py-2 px-4 hover:bg-green-800 ">
                        Logout
                    </button>
                </form>
            </nav>
        @elseif (Auth::User()->role->role_name == 'committe-user')

            <nav class="space-y-4 font-medium w-full px-6">
                <a href="{{ route('vehicles.info') }}"
                    class="border-b block py-2 px-4  {{ request()->routeIs('vehicles.info') || request()->routeIs('vehicle.details') ? 'bg-white text-green-800' : 'hover:bg-green-800' }}">Vehicle
                    Information</a>
                <a href="{{ route('vehicle.tracking') }}"
                    class="border-b block py-2 px-4  {{ request()->routeIs('vehicle.tracking') || request()->routeIs('vehicle.liveLocation') ? 'bg-white text-green-800' : 'hover:bg-green-800' }}">Vehicle
                    Tracking</a>
                <a href="{{ route('vehicle.maintenance') }}"
                    class="border-b block py-2 px-4  {{ request()->routeIs('vehicle.maintenance') ? 'bg-white text-green-800' : 'hover:bg-green-800' }}">Maintenance</a>
                @php
                    $isReportOpen = request()->routeIs('report.vehicle-status') || request()->routeIs('report.maintenance') || request()->routeIs('reports');
                @endphp

                <div class="w-full ">
                    <a href="{{ route('reports') }}"
                        class="cursor-pointer border-b w-full text-left py-2 px-4  font-medium flex justify-between items-center {{ $isReportOpen ? 'bg-white text-green-800 hover:bg-white' : 'hover:bg-green-800 ' }}">
                        Reports
                        <svg class="w-4 h-4 ml-2 transform transition-transform cursor-pointer " id="report-arrow"
                            onclick="document.getElementById('report-submenu').classList.toggle('hidden')"
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
                <form id="logout-form" class="block">
                    @csrf
                    <button onclick="LoginHandler();"
                        class="cursor-pointer border-b w-full text-left py-2 px-4 hover:bg-green-800 ">
                        Logout
                    </button>
                </form>
            </nav>
        @elseif (Auth::User()->role->role_name == 'vehicle-supervisor')
            <nav class="space-y-4 font-medium w-full px-6">
                <a href="{{ route('vehicles.info') }}"
                    class="border-b block py-2 px-4  {{ request()->routeIs('vehicles.info') || request()->routeIs('vehicle.details') ? 'bg-white text-green-800' : 'hover:bg-green-800' }}">Vehicle
                    Information
                </a>
                <a href="{{ route('vehicle-supervisor.maintenance') }}"
                    class="border-b block py-2 px-4  {{ request()->routeIs('vehicle-supervisor.maintenance') ? 'bg-white text-green-800' : 'hover:bg-green-800' }}">Vehicle
                    Maintenance
                </a>

                <form id="logout-form" class="block">
                    @csrf
                    <button onclick="LoginHandler();"
                        class="cursor-pointer border-b w-full text-left py-2 px-4 hover:bg-green-800 ">
                        Logout
                    </button>
                </form>
            </nav>
        @elseif (Auth::User()->role->role_name == 'district-user')
            <nav class="space-y-4 font-medium w-full px-6">
                <a href="{{ route('district-user.vehicles') }}"
                    class="border-b block py-2 px-4  {{ request()->routeIs('district-user.vehicles') || request()->routeIs('vehicle.details') ? 'bg-white text-green-800' : 'hover:bg-green-800' }}">Vehicle
                    Information
                </a>
                <a href="{{ route('district-user.vehicles.tracking') }}"
                    class="border-b block py-2 px-4  {{ request()->routeIs('district-user.vehicles.tracking') || request()->routeIs('vehicle.liveLocation') ? 'bg-white text-green-800' : 'hover:bg-green-800' }}">Vehicle
                    Tracking</a>

                <form id="logout-form" class="block">
                    @csrf
                    <button onclick="LoginHandler();"
                        class="cursor-pointer border-b w-full text-left py-2 px-4 hover:bg-green-800 ">
                        Logout
                    </button>
                </form>
            </nav>
        @elseif (Auth::User()->role->role_name == 'divisional-user')
            <nav class="space-y-4 font-medium w-full px-6">
                <a href="{{ route('divisional-user.vehicles') }}"
                    class="border-b block py-2 px-4  {{ request()->routeIs('divisional-user.vehicles') || request()->routeIs('vehicle.details') ? 'bg-white text-green-800' : 'hover:bg-green-800' }}">Vehicle
                    Information
                </a>
                <a href="{{ route('divisional-user.vehicles.tracking') }}"
                    class="border-b block py-2 px-4  {{ request()->routeIs('divisional-user.vehicles.tracking') || request()->routeIs('vehicle.liveLocation') ? 'bg-white text-green-800' : 'hover:bg-green-800' }}">Vehicle
                    Tracking</a>
                <a href="{{ route('vehicle.maintenance') }}"
                    class="border-b block py-2 px-4  {{ request()->routeIs('vehicle.maintenance') ? 'bg-white text-green-800' : 'hover:bg-green-800' }}">Maintenance
                </a>
                @php
                    $isReportOpen = request()->routeIs('report.vehicle-status') || request()->routeIs('report.maintenance') || request()->routeIs('reports');
                @endphp
                <div class="w-full ">
                    <a href="{{ route('reports') }}"
                        class="cursor-pointer border-b w-full text-left py-2 px-4  font-medium flex justify-between items-center {{ $isReportOpen ? 'bg-white text-green-800 hover:bg-white' : 'hover:bg-green-800 ' }}">
                        Reports
                        <svg class="w-4 h-4 ml-2 transform transition-transform cursor-pointer " id="report-arrow"
                            onclick="document.getElementById('report-submenu').classList.toggle('hidden')"
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
                <form id="logout-form" class="block">
                    @csrf
                    <button onclick="LoginHandler();"
                        class="cursor-pointer border-b w-full text-left py-2 px-4 hover:bg-green-800 ">
                        Logout
                    </button>
                </form>
            </nav>
        @endif
    </aside>
    <!-- Main Content -->
    <main class="flex-1">
        <div class="w-full h-full bg-cover bg-bottom relative overflow-y-auto p-10">
            @yield('content')
        </div>
    </main>
</body>

<!-- Include JS at bottom of body -->
<script src="{{ asset('js/daisyui.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    function LoginHandler() {
        event.preventDefault();
        const confirmation = window.confirm("Are you sure you want to logout?");
        if (!confirmation) return;
        const form = document.getElementById('logout-form');
        form.method = 'POST';
        form.action = '{{ route('logout') }}';
        form.submit();
    }

    function toggleMenu() {
        const menu = document.getElementById('menu');
        menu.classList.toggle('hidden');
    }
</script>
<!-- Inject page-specific scripts -->
@stack('scripts')

</html>