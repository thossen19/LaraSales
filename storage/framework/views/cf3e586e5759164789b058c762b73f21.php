<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Sales ERP System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl w-full space-y-8">
        <!-- Logo and Title -->
        <div class="text-center">
            <div class="mx-auto h-16 w-16 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-chart-line text-white text-2xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-gray-900">Create Your Account</h2>
            <p class="mt-2 text-sm text-gray-600">Start your 30-day free trial of Sales ERP</p>
        </div>

        <!-- Registration Form -->
        <div class="bg-white shadow-xl rounded-lg overflow-hidden">
            <div class="grid md:grid-cols-2">
                <!-- Company Information -->
                <div class="bg-gradient-to-br from-blue-600 to-indigo-700 p-8 text-white">
                    <h3 class="text-2xl font-bold mb-4">Company Information</h3>
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <i class="fas fa-building mr-3 text-blue-200"></i>
                            <div>
                                <p class="font-semibold">Multi-Company Support</p>
                                <p class="text-sm text-blue-200">Manage multiple companies</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-users mr-3 text-blue-200"></i>
                            <div>
                                <p class="font-semibold">User Management</p>
                                <p class="text-sm text-blue-200">Role-based permissions</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-chart-line mr-3 text-blue-200"></i>
                            <div>
                                <p class="font-semibold">Complete ERP</p>
                                <p class="text-sm text-blue-200">All business modules included</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-lock mr-3 text-blue-200"></i>
                            <div>
                                <p class="font-semibold">Secure</p>
                                <p class="text-sm text-blue-200">Enterprise-grade security</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Registration Form -->
                <div class="p-8">
                    <form id="registerForm" class="space-y-6">
                        <?php if($errors->any()): ?>
                            <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-md">
                                <div class="text-sm">
                                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <p><?php echo e($error); ?></p>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- User Information -->
                        <div>
                            <h4 class="text-lg font-medium text-gray-900 mb-4">Personal Information</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                                    <div class="mt-1 relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-user text-gray-400"></i>
                                        </div>
                                        <input id="name" name="name" type="text" required
                                               class="pl-10 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                               placeholder="John Doe">
                                    </div>
                                </div>
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
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
                                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                                    <div class="mt-1 relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-phone text-gray-400"></i>
                                        </div>
                                        <input id="phone" name="phone" type="tel"
                                               class="pl-10 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                               placeholder="+1 (555) 123-4567">
                                    </div>
                                </div>
                                <div>
                                    <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                                    <div class="mt-1 relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-map-marker-alt text-gray-400"></i>
                                        </div>
                                        <input id="address" name="address" type="text"
                                               class="pl-10 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                               placeholder="123 Main St">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Password Fields -->
                        <div>
                            <h4 class="text-lg font-medium text-gray-900 mb-4">Security</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                                    <div class="mt-1 relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-lock text-gray-400"></i>
                                        </div>
                                        <input id="password" name="password" type="password" autocomplete="new-password" required
                                               class="pl-10 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                               placeholder="••••••••••">
                                    </div>
                                </div>
                                <div>
                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                                    <div class="mt-1 relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-lock text-gray-400"></i>
                                        </div>
                                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
                                               class="pl-10 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                               placeholder="••••••••••">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Company Information -->
                        <div>
                            <h4 class="text-lg font-medium text-gray-900 mb-4">Company Details</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="company_name" class="block text-sm font-medium text-gray-700">Company Name</label>
                                    <div class="mt-1 relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-building text-gray-400"></i>
                                        </div>
                                        <input id="company_name" name="company_name" type="text" required
                                               class="pl-10 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                               placeholder="ACME Corporation">
                                    </div>
                                </div>
                                <div>
                                    <label for="company_email" class="block text-sm font-medium text-gray-700">Company Email</label>
                                    <div class="mt-1 relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-envelope text-gray-400"></i>
                                        </div>
                                        <input id="company_email" name="company_email" type="email" required
                                               class="pl-10 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                               placeholder="info@acme.com">
                                    </div>
                                </div>
                                <div>
                                    <label for="company_phone" class="block text-sm font-medium text-gray-700">Company Phone</label>
                                    <div class="mt-1 relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-phone text-gray-400"></i>
                                        </div>
                                        <input id="company_phone" name="company_phone" type="tel"
                                               class="pl-10 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                               placeholder="+1 (555) 987-6543">
                                    </div>
                                </div>
                                <div>
                                    <label for="company_address" class="block text-sm font-medium text-gray-700">Company Address</label>
                                    <div class="mt-1 relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-map-marker-alt text-gray-400"></i>
                                        </div>
                                        <input id="company_address" name="company_address" type="text"
                                               class="pl-10 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                               placeholder="456 Business Ave">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Terms and Conditions -->
                        <div>
                            <label class="flex items-center">
                                <input id="terms" name="terms" type="checkbox" required
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <span class="ml-2 block text-sm text-gray-900">
                                    I agree to the <a href="#" class="text-indigo-600 hover:text-indigo-500">Terms of Service</a> and <a href="#" class="text-indigo-600 hover:text-indigo-500">Privacy Policy</a>
                                </span>
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <div>
                            <button type="submit"
                                    class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                                <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                    <i class="fas fa-user-plus group-hover:text-indigo-400"></i>
                                </span>
                                Create Account
                            </button>
                        </div>

                        <!-- Login Link -->
                        <div class="mt-6 text-center">
                            <p class="text-sm text-gray-600">
                                Already have an account? 
                                <a href="<?php echo e(route('login')); ?>" class="font-medium text-indigo-600 hover:text-indigo-500">Sign in</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
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
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            
            // Show loading state
            submitButton.innerHTML = '<span class="absolute left-0 inset-y-0 flex items-center pl-3"><i class="fas fa-spinner fa-spin"></i></span> Creating Account...';
            submitButton.disabled = true;
            
            const data = {
                name: formData.get('name'),
                email: formData.get('email'),
                phone: formData.get('phone'),
                address: formData.get('address'),
                password: formData.get('password'),
                password_confirmation: formData.get('password_confirmation'),
                company_name: formData.get('company_name'),
                company_email: formData.get('company_email'),
                company_phone: formData.get('company_phone'),
                company_address: formData.get('company_address')
            };
            
            fetch('/api/register', {
                method: 'POST',
                body: JSON.stringify(data),
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
                    alert('Registration failed: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Registration error:', error);
                alert('Registration failed. Please try again.');
            })
            .finally(() => {
                submitButton.innerHTML = originalText;
                submitButton.disabled = false;
            });
        });
    </script>
</body>
</html>
<?php /**PATH C:\Users\Lupu\Desktop\laravel\fa-saas\resources\views/auth/register.blade.php ENDPATH**/ ?>