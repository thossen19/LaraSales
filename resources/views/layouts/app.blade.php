<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name', 'Sales ERP'))</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @stack('styles')
</head>
<body class="bg-gray-50">
    <div class="flex min-h-screen">
        @include('components.sidebar')

        <div class="flex-1 flex flex-col">
            @include('components.header')

            <main class="flex-1 p-6 overflow-auto">
                @if(session('success'))
                    <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                        <button type="button" class="absolute top-0 right-0 px-4 py-3" onclick="this.parentElement.remove();">
                            <span class="text-green-500">&times;</span>
                        </button>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                        <button type="button" class="absolute top-0 right-0 px-4 py-3" onclick="this.parentElement.remove();">
                            <span class="text-red-500">&times;</span>
                        </button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
