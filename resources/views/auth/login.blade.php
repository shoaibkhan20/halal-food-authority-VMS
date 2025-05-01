<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Food Safety and Halal Food Authority</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @vite('resources/css/app.css')
</head>

<body class="relative min-h-screen">

    <!-- Background Image -->
    <div class="absolute top-0 left-0 w-full h-full bg-cover bg-center"
        style="background-image: url('{{ asset('images/map-background.jpg') }}');"></div>

    <!-- Overlay -->
    <div class="absolute top-0 left-0 w-full h-full bg-green-900 opacity-80"></div>

    <!-- Content -->
    <div class="relative flex items-center justify-center min-h-screen">
        <div class="bg-white rounded-2xl shadow-lg p-10 w-full max-w-md">
            <div class="flex justify-center mb-8">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-40 w-auto">
            </div>

            @if ($errors->has('login'))
                <div class="mb-4 p-3 rounded bg-red-100 text-red-700 text-sm">
                    {{ $errors->first('login') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf
                <div>
                    <input type="text" name="username" placeholder="Username" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none placeholder:text-gray-300 focus:ring-2 focus:ring-green-500">
                </div>

                <div class="relative">
                    <input type="password" name="password" placeholder="Password" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-md placeholder:text-gray-300  focus:outline-none focus:ring-2 focus:ring-green-500">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </div>
                </div>
                <div>
                    <button type="submit"
                        class="w-full bg-yellow-400 hover:bg-yellow-500 text-white font-semibold py-3 rounded-md shadow-md">
                        Login
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>

</html>