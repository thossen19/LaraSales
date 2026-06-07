<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found - Sales ERP System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <main class="flex-grow flex flex-col items-center justify-center px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <div class="mx-auto h-24 w-24 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-full flex items-center justify-center mb-8">
                <i class="fas fa-exclamation-triangle text-white text-4xl"></i>
            </div>
            <h1 class="text-4xl font-bold text-gray-900">404</h1>
            <p class="mt-2 text-gray-600">Page not found</p>
            <p class="mt-4 text-gray-500">Sorry, we couldn't find the page you're looking for.</p>
            <div class="mt-8">
                <a href="{{ url('/') }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Go back home
                </a>
            </div>
        </div>
    </main>
</body>
</html>
