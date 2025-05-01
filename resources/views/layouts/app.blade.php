<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Super Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="flex min-h-screen bg-gray-100">

    <!-- Sidebar -->
    <aside class="w-64 bg-green-900 text-white flex flex-col items-center py-6">
        <img src="{{ asset('images/logo.png') }}" class="h-20 mb-6" alt="Logo">
        <nav class="space-y-4 font-medium w-full px-6">
            <a href="{{ route('dashboard') }}" class="block py-2 px-4 rounded bg-white text-green-800">Vehicle Information</a>
            <a href="#" class="block py-2 px-4 hover:bg-green-800 rounded">Vehicle Tracking</a>
            <a href="#" class="block py-2 px-4 hover:bg-green-800 rounded">Maintenance</a>
            <a href="#" class="block py-2 px-4 hover:bg-green-800 rounded">User Role Management</a>
            <a href="#" class="block py-2 px-4 hover:bg-green-800 rounded">Reports</a>

            <form method="POST" action="{{ route('logout') }}" class="block">
                @csrf
                <button type="submit" class="w-full text-left py-2 px-4 hover:bg-green-800 rounded">
                    Logout
                </button>
            </form> 
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1">
        <!-- Background Image -->
        <div class="w-full h-full bg-cover bg-bottom relative overflow-y-auto"
            style="background-image: url('{{ asset('images/map-background.jpg') }}');">
            @yield('content')
        </div>
    </main>

    
</body>
<script src="{{ asset('js/daisyui.js') }}">
    <script src="https://kit.fontawesome.com/e28fed60aa.js" crossorigin="anonymous"></script>
</html>