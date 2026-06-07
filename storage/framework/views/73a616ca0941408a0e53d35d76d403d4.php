<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales ERP System - API Documentation</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-gradient-to-r from-blue-600 to-blue-800 text-white shadow-lg">
            <div class="container mx-auto px-6 py-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-4xl font-bold flex items-center">
                            <i class="fas fa-chart-line mr-3"></i>
                            Sales ERP System
                        </h1>
                        <p class="mt-2 text-blue-100">Complete Enterprise Resource Planning Solution</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="<?php echo e(route('login')); ?>" class="px-4 py-2 bg-white text-blue-700 rounded-lg hover:bg-blue-50 transition font-medium text-sm">
                            <i class="fas fa-sign-in-alt mr-1"></i>Login
                        </a>
                        <a href="<?php echo e(route('register')); ?>" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-400 transition font-medium text-sm">
                            <i class="fas fa-user-plus mr-1"></i>Register
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="container mx-auto px-6 py-12">
            <!-- Overview Section -->
            <section class="mb-12">
                <div class="bg-white rounded-lg shadow-md p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                        System Overview
                    </h2>
                    <div class="grid md:grid-cols-2 gap-8">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-700 mb-3">Core Modules</h3>
                            <ul class="space-y-2 text-gray-600">
                                <li class="flex items-center"><i class="fas fa-shopping-cart text-green-500 mr-2"></i> Sales Management</li>
                                <li class="flex items-center"><i class="fas fa-truck text-orange-500 mr-2"></i> Purchase Management</li>
                                <li class="flex items-center"><i class="fas fa-boxes text-purple-500 mr-2"></i> Items & Inventory</li>
                                <li class="flex items-center"><i class="fas fa-industry text-blue-500 mr-2"></i> Manufacturing</li>
                                <li class="flex items-center"><i class="fas fa-building text-red-500 mr-2"></i> Fixed Assets</li>
                                <li class="flex items-center"><i class="fas fa-sitemap text-indigo-500 mr-2"></i> Dimensions</li>
                                <li class="flex items-center"><i class="fas fa-users text-pink-500 mr-2"></i> Human Resources</li>
                                <li class="flex items-center"><i class="fas fa-calculator text-teal-500 mr-2"></i> Banking & Ledger</li>
                            </ul>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-700 mb-3">Key Features</h3>
                            <ul class="space-y-2 text-gray-600">
                                <li class="flex items-center"><i class="fas fa-server text-blue-500 mr-2"></i> Multi-Company Support</li>
                                <li class="flex items-center"><i class="fas fa-shield-alt text-green-500 mr-2"></i> Role-Based Permissions</li>
                                <li class="flex items-center"><i class="fas fa-database text-orange-500 mr-2"></i> RESTful API</li>
                                <li class="flex items-center"><i class="fas fa-chart-bar text-purple-500 mr-2"></i> Financial Reporting</li>
                                <li class="flex items-center"><i class="fas fa-cogs text-red-500 mr-2"></i> Automation Workflows</li>
                                <li class="flex items-center"><i class="fas fa-history text-indigo-500 mr-2"></i> Audit Trails</li>
                                <li class="flex items-center"><i class="fas fa-file-export text-pink-500 mr-2"></i> Data Export</li>
                                <li class="flex items-center"><i class="fas fa-mobile-alt text-teal-500 mr-2"></i> Mobile Ready</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <!-- API Endpoints Section -->
            <section class="mb-12">
                <div class="bg-white rounded-lg shadow-md p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">
                        <i class="fas fa-code text-blue-600 mr-2"></i>
                        API Endpoints
                    </h2>
                    <div class="grid md:grid-cols-3 gap-6">
                        <div class="border-l-4 border-green-500 pl-4">
                            <h3 class="font-semibold text-gray-700 mb-2">Authentication</h3>
                            <ul class="text-sm text-gray-600 space-y-1">
                                <li><code class="bg-gray-100 px-2 py-1 rounded">POST /api/login</code></li>
                                <li><code class="bg-gray-100 px-2 py-1 rounded">POST /api/register</code></li>
                                <li><code class="bg-gray-100 px-2 py-1 rounded">POST /api/logout</code></li>
                            </ul>
                        </div>
                        <div class="border-l-4 border-blue-500 pl-4">
                            <h3 class="font-semibold text-gray-700 mb-2">Sales & Purchases</h3>
                            <ul class="text-sm text-gray-600 space-y-1">
                                <li><code class="bg-gray-100 px-2 py-1 rounded">GET /api/sales-orders</code></li>
                                <li><code class="bg-gray-100 px-2 py-1 rounded">GET /api/purchase-orders</code></li>
                                <li><code class="bg-gray-100 px-2 py-1 rounded">GET /api/customers</code></li>
                                <li><code class="bg-gray-100 px-2 py-1 rounded">GET /api/suppliers</code></li>
                            </ul>
                        </div>
                        <div class="border-l-4 border-purple-500 pl-4">
                            <h3 class="font-semibold text-gray-700 mb-2">Inventory & Items</h3>
                            <ul class="text-sm text-gray-600 space-y-1">
                                <li><code class="bg-gray-100 px-2 py-1 rounded">GET /api/items</code></li>
                                <li><code class="bg-gray-100 px-2 py-1 rounded">GET /api/warehouses</code></li>
                                <li><code class="bg-gray-100 px-2 py-1 rounded">GET /api/inventory/current</code></li>
                                <li><code class="bg-gray-100 px-2 py-1 rounded">POST /api/inventory/adjust</code></li>
                            </ul>
                        </div>
                        <div class="border-l-4 border-orange-500 pl-4">
                            <h3 class="font-semibold text-gray-700 mb-2">Manufacturing</h3>
                            <ul class="text-sm text-gray-600 space-y-1">
                                <li><code class="bg-gray-100 px-2 py-1 rounded">GET /api/manufacturing/bom</code></li>
                                <li><code class="bg-gray-100 px-2 py-1 rounded">GET /api/manufacturing/production-orders</code></li>
                                <li><code class="bg-gray-100 px-2 py-1 rounded">GET /api/manufacturing/work-centers</code></li>
                            </ul>
                        </div>
                        <div class="border-l-4 border-red-500 pl-4">
                            <h3 class="font-semibold text-gray-700 mb-2">Assets & HR</h3>
                            <ul class="text-sm text-gray-600 space-y-1">
                                <li><code class="bg-gray-100 px-2 py-1 rounded">GET /api/fixed-assets</code></li>
                                <li><code class="bg-gray-100 px-2 py-1 rounded">GET /api/dimensions</code></li>
                                <li><code class="bg-gray-100 px-2 py-1 rounded">GET /api/hr/employees</code></li>
                                <li><code class="bg-gray-100 px-2 py-1 rounded">GET /api/hr/payrolls</code></li>
                            </ul>
                        </div>
                        <div class="border-l-4 border-teal-500 pl-4">
                            <h3 class="font-semibold text-gray-700 mb-2">Accounting</h3>
                            <ul class="text-sm text-gray-600 space-y-1">
                                <li><code class="bg-gray-100 px-2 py-1 rounded">GET /api/accounting/accounts</code></li>
                                <li><code class="bg-gray-100 px-2 py-1 rounded">GET /api/accounting/journal-entries</code></li>
                                <li><code class="bg-gray-100 px-2 py-1 rounded">GET /api/accounting/trial-balance</code></li>
                                <li><code class="bg-gray-100 px-2 py-1 rounded">GET /api/accounting/balance-sheet</code></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Quick Start Section -->
            <section class="mb-12">
                <div class="bg-white rounded-lg shadow-md p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">
                        <i class="fas fa-rocket text-blue-600 mr-2"></i>
                        Quick Start
                    </h2>
                    <div class="grid md:grid-cols-2 gap-8">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-700 mb-3">1. Register Company</h3>
                            <div class="bg-gray-900 text-gray-100 rounded-lg p-4 text-sm">
                                <pre><code>curl -X POST http://localhost:8000/api/register \
-H "Content-Type: application/json" \
-d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password",
    "password_confirmation": "password",
    "company_name": "ACME Corporation",
    "company_email": "info@acme.com"
}'</code></pre>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-700 mb-3">2. Login & Get Token</h3>
                            <div class="bg-gray-900 text-gray-100 rounded-lg p-4 text-sm">
                                <pre><code>curl -X POST http://localhost:8000/api/login \
-H "Content-Type: application/json" \
-d '{
    "email": "john@example.com",
    "password": "password"
}'</code></pre>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-700 mb-3">3. Create Sales Order</h3>
                            <div class="bg-gray-900 text-gray-100 rounded-lg p-4 text-sm">
                                <pre><code>curl -X POST http://localhost:8000/api/sales-orders \
-H "Content-Type: application/json" \
-H "Authorization: Bearer YOUR_TOKEN" \
-d '{
    "customer_id": 1,
    "order_date": "2024-01-15",
    "items": [{
        "item_id": 1,
        "warehouse_id": 1,
        "quantity": 10,
        "unit_price": 100
    }]
}'</code></pre>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-700 mb-3">4. View Orders</h3>
                            <div class="bg-gray-900 text-gray-100 rounded-lg p-4 text-sm">
                                <pre><code>curl -X GET http://localhost:8000/api/sales-orders \
-H "Authorization: Bearer YOUR_TOKEN"</code></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Tech Stack Section -->
            <section>
                <div class="bg-white rounded-lg shadow-md p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">
                        <i class="fas fa-layer-group text-blue-600 mr-2"></i>
                        Technology Stack
                    </h2>
                    <div class="grid md:grid-cols-4 gap-6">
                        <div class="text-center">
                            <div class="bg-red-100 rounded-lg p-4 mb-3">
                                <i class="fab fa-laravel text-4xl text-red-600"></i>
                            </div>
                            <h3 class="font-semibold text-gray-700">Laravel 11</h3>
                            <p class="text-sm text-gray-600">PHP Framework</p>
                        </div>
                        <div class="text-center">
                            <div class="bg-blue-100 rounded-lg p-4 mb-3">
                                <i class="fas fa-database text-4xl text-blue-600"></i>
                            </div>
                            <h3 class="font-semibold text-gray-700">MySQL</h3>
                            <p class="text-sm text-gray-600">Database</p>
                        </div>
                        <div class="text-center">
                            <div class="bg-green-100 rounded-lg p-4 mb-3">
                                <i class="fas fa-code text-4xl text-green-600"></i>
                            </div>
                            <h3 class="font-semibold text-gray-700">RESTful API</h3>
                            <p class="text-sm text-gray-600">Architecture</p>
                        </div>
                        <div class="text-center">
                            <div class="bg-purple-100 rounded-lg p-4 mb-3">
                                <i class="fas fa-shield-alt text-4xl text-purple-600"></i>
                            </div>
                            <h3 class="font-semibold text-gray-700">Sanctum</h3>
                            <p class="text-sm text-gray-600">Authentication</p>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="bg-gray-800 text-white py-8">
            <div class="container mx-auto px-6 text-center">
                <div class="mb-4">
                    <i class="fas fa-chart-line text-3xl mb-2"></i>
                    <p class="text-xl font-bold">Sales ERP System</p>
                </div>
                <p class="text-gray-400">© 2024 Sales ERP. Built with Laravel 11.</p>
                <div class="mt-4 flex justify-center space-x-6">
                    <a href="#" class="text-gray-400 hover:text-white transition">
                        <i class="fab fa-github"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition">
                        <i class="fas fa-book"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition">
                        <i class="fas fa-code"></i>
                    </a>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
<?php /**PATH C:\Users\Lupu\Desktop\laravel\fa-saas\resources\views/welcome.blade.php ENDPATH**/ ?>