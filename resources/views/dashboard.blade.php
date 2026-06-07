@extends('layouts.app')

@section('title', 'Dashboard - Sales ERP')

@section('content')
        <!-- Page Header -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900">Dashboard</h2>
            <p class="mt-2 text-gray-600">Welcome back! Here's what's happening with your business today.</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Sales -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                            <i class="fas fa-dollar-sign text-white"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Sales</dt>
                                <dd class="text-lg font-medium text-gray-900">$45,231.89</dd>
                            </dl>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-5 py-3">
                        <div class="text-sm">
                            <span class="text-green-600 font-medium">12%</span>
                            <span class="text-gray-500"> from last month</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Purchases -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                            <i class="fas fa-shopping-cart text-white"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Purchases</dt>
                                <dd class="text-lg font-medium text-gray-900">$28,456.12</dd>
                            </dl>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-5 py-3">
                        <div class="text-sm">
                            <span class="text-blue-600 font-medium">8%</span>
                            <span class="text-gray-500"> from last month</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inventory Value -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                            <i class="fas fa-boxes text-white"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Inventory Value</dt>
                                <dd class="text-lg font-medium text-gray-900">$125,678.90</dd>
                            </dl>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-5 py-3">
                        <div class="text-sm">
                            <span class="text-purple-600 font-medium">5%</span>
                            <span class="text-gray-500"> from last month</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Users -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-orange-500 rounded-md p-3">
                            <i class="fas fa-users text-white"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Active Users</dt>
                                <dd class="text-lg font-medium text-gray-900">24</dd>
                            </dl>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-5 py-3">
                        <div class="text-sm">
                            <span class="text-orange-600 font-medium">2</span>
                            <span class="text-gray-500">new this month</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Sales Chart -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Sales Overview</h3>
                <div class="h-64">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>

            <!-- Inventory Status -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Inventory Status</h3>
                <div class="h-64">
                    <canvas id="inventoryChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg font-medium text-gray-900">Recent Activities</h3>
            </div>
            <div class="overflow-hidden">
                <ul class="divide-y divide-gray-200">
                    <li class="px-4 py-4 sm:px-6">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center">
                                    <i class="fas fa-shopping-cart text-green-600 text-sm"></i>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-900">
                                    New sales order <span class="font-medium">SO-1-000001</span> created
                                </p>
                                <p class="text-sm text-gray-500">
                                    Customer: ABC Corporation - $2,500.00
                                </p>
                            </div>
                            <div class="text-sm text-gray-500">
                                <span>2 hours ago</span>
                            </div>
                        </div>
                    </li>
                    <li class="px-4 py-4 sm:px-6">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                    <i class="fas fa-truck text-blue-600 text-sm"></i>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-900">
                                    Purchase order <span class="font-medium">PO-1-000001</span> received
                                </p>
                                <p class="text-sm text-gray-500">
                                    Supplier: Tech Components Inc - $5,200.00
                                </p>
                            </div>
                            <div class="text-sm text-gray-500">
                                <span>4 hours ago</span>
                            </div>
                        </div>
                    </li>
                    <li class="px-4 py-4 sm:px-6">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <div class="h-8 w-8 rounded-full bg-purple-100 flex items-center justify-center">
                                    <i class="fas fa-industry text-purple-600 text-sm"></i>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-900">
                                    Production order <span class="font-medium">PROD-1-000001</span> completed
                                </p>
                                <p class="text-sm text-gray-500">
                                    Product: Business Laptop - 100 units
                                </p>
                            </div>
                            <div class="text-sm text-gray-500">
                                <span>6 hours ago</span>
                            </div>
                        </div>
                    </li>
                    <li class="px-4 py-4 sm:px-6">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <div class="h-8 w-8 rounded-full bg-orange-100 flex items-center justify-center">
                                    <i class="fas fa-user-plus text-orange-600 text-sm"></i>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-900">
                                    New employee <span class="font-medium">Sarah Johnson</span> added
                                </p>
                                <p class="text-sm text-gray-500">
                                    Position: Marketing Manager
                                </p>
                            </div>
                            <div class="text-sm text-gray-500">
                                <span>8 hours ago</span>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
@endsection

@push('scripts')
    <script>
        const salesData = {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Sales',
                data: [12000, 19000, 15000, 25000, 22000, 30000],
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.1
            }]
        };

        const inventoryData = {
            labels: ['Electronics', 'Furniture', 'Office Supplies', 'Raw Materials', 'Finished Goods'],
            datasets: [{
                label: 'Stock Levels',
                data: [450, 120, 890, 340, 670],
                backgroundColor: [
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(147, 51, 234, 0.8)',
                    'rgba(234, 179, 8, 0.8)',
                    'rgba(244, 114, 182, 0.8)',
                    'rgba(34, 197, 94, 0.8)'
                ]
            }]
        };

        new Chart(document.getElementById('salesChart').getContext('2d'), {
            type: 'line',
            data: salesData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { callback: v => '$' + v.toLocaleString() } } }
            }
        });

        new Chart(document.getElementById('inventoryChart').getContext('2d'), {
            type: 'bar',
            data: inventoryData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    </script>
@endpush
