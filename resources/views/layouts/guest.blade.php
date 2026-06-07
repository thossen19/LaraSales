<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Sales ERP') }}</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom Styles -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .bg-gradient-to-br {
            background-image: linear-gradient(to bottom right, var(--tw-gradient-from), var(--tw-gradient-to));
        }
    </style>

    <!-- Scripts -->
    @stack('scripts')
        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @endstack
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100">
    <div id="app">
        <!-- Main Content -->
        <main>
            @yield('content')
        </main>
    </div>
</body>
</html>
