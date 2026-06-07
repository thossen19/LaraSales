<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sales ERP System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full space-y-8">
        <!-- Logo and Title -->
        <div class="text-center">
            <div class="mx-auto h-16 w-16 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-chart-line text-white text-2xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-gray-900">Sales ERP</h2>
            <p class="mt-2 text-sm text-gray-600">Sign in to your account</p>
        </div>

        <!-- Login Form -->
        <div class="bg-white shadow-xl rounded-lg p-8">
            <form id="loginForm" class="space-y-6">
                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-md">
                        <div class="text-sm">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-md">
                        <p class="text-sm">{{ session('error') }}</p>
                    </div>
                @endif

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                    <div class="mt-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input id="email" name="email" type="email" autocomplete="email" required
                               class="pl-10 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="you@example.com">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <div class="mt-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                               class="pl-10 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="••••••••••">
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox" 
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="remember" class="ml-2 block text-sm text-gray-900">Remember me</label>
                    </div>
                    <div class="text-sm">
                        <a href="#" class="font-medium text-indigo-600 hover:text-indigo-500">Forgot password?</a>
                    </div>
                </div>

                <div>
                    <button type="submit" 
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class="fas fa-sign-in-alt group-hover:text-indigo-400"></i>
                        </span>
                        Sign in
                    </button>
                </div>

                <div class="mt-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500">New to Sales ERP?</span>
                        </div>
                    </div>

                    <div class="mt-6 text-center">
                        <a href="{{ route('register') }}" 
                           class="font-medium text-indigo-600 hover:text-indigo-500 transition duration-150 ease-in-out">
                            Create your account
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Help Section -->
        <div class="mt-8 text-center">
            <p class="text-sm text-gray-500">
                Need help? <a href="#" class="font-medium text-indigo-600 hover:text-indigo-500">Contact Support</a>
            </p>
        </div>
    </div>

    <script>
        // Form submission handling
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            
            // Show loading state
            submitButton.innerHTML = '<span class="absolute left-0 inset-y-0 flex items-center pl-3"><i class="fas fa-spinner fa-spin"></i></span> Signing in...';
            submitButton.disabled = true;
            
            fetch('/api/login', {
                method: 'POST',
                body: JSON.stringify({
                    email: formData.get('email'),
                    password: formData.get('password')
                }),
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.token) {
                    // Store token and redirect
                    localStorage.setItem('auth_token', data.token);
                    window.location.href = '/dashboard';
                } else {
                    alert('Login failed: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Login error:', error);
                alert('Login failed. Please try again.');
            })
            .finally(() => {
                submitButton.innerHTML = originalText;
                submitButton.disabled = false;
            });
        });
    </script>
</body>
</html>
