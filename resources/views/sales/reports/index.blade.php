@extends('layouts.app')

@section('title', 'Sales Reports - Sales ERP')

@section('content')
    <div class="flex gap-6">
        @include('components.sales-sidebar')
        
        <div class="flex-1">
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900">Sales Reports</h2>
                <p class="mt-2 text-gray-600">Access comprehensive sales analytics and reporting tools.</p>
            </div>

            <!-- Report Categories -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-white shadow rounded-lg p-6 hover:shadow-lg transition-shadow cursor-pointer">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                            <i class="fas fa-users text-white text-xl"></i>
                        </div>
                        <div class="ml-5">
                            <h3 class="text-lg font-medium text-gray-900">Customer Reports</h3>
                            <p class="text-sm text-gray-600">Customer performance, aging, and analysis reports</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('sales.reports.customer') }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                            View Customer Reports →
                        </a>
                    </div>
                </div>

                <div class="bg-white shadow rounded-lg p-6 hover:shadow-lg transition-shadow cursor-pointer">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                            <i class="fas fa-chart-line text-white text-xl"></i>
                        </div>
                        <div class="ml-5">
                            <h3 class="text-lg font-medium text-gray-900">Sales Reports</h3>
                            <p class="text-sm text-gray-600">Sales performance, trends, and team metrics</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('sales.reports.sales') }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                            View Sales Reports →
                        </a>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                            <i class="fas fa-dollar-sign text-white"></i>
                        </div>
                        <div class="ml-5">
                            <p class="text-sm font-medium text-gray-500 truncate">Total Sales (MTD)</p>
                            <p class="text-2xl font-bold text-gray-900">$156,890</p>
                            <p class="text-sm text-green-600">+18.5%</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                            <i class="fas fa-shopping-cart text-white"></i>
                        </div>
                        <div class="ml-5">
                            <p class="text-sm font-medium text-gray-500 truncate">Orders (MTD)</p>
                            <p class="text-2xl font-bold text-gray-900">248</p>
                            <p class="text-sm text-green-600">+12.3%</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                            <i class="fas fa-users text-white"></i>
                        </div>
                        <div class="ml-5">
                            <p class="text-sm font-medium text-gray-500 truncate">Active Customers</p>
                            <p class="text-2xl font-bold text-gray-900">45</p>
                            <p class="text-sm text-green-600">+3 new</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-orange-500 rounded-md p-3">
                            <i class="fas fa-percentage text-white"></i>
                        </div>
                        <div class="ml-5">
                            <p class="text-sm font-medium text-gray-500 truncate">Conversion Rate</p>
                            <p class="text-2xl font-bold text-gray-900">68.5%</p>
                            <p class="text-sm text-green-600">+5.2%</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Reports -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Reports Generated</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                        <div>
                            <div class="text-sm font-medium text-gray-900">Customer Performance Report</div>
                            <div class="text-sm text-gray-600">Generated: Jan 28, 2024 at 2:30 PM</div>
                        </div>
                        <div class="flex space-x-2">
                            <button class="text-indigo-600 hover:text-indigo-900 text-sm">View</button>
                            <button class="text-gray-600 hover:text-gray-900 text-sm">Download</button>
                        </div>
                    </div>
                    <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                        <div>
                            <div class="text-sm font-medium text-gray-900">Sales Trend Analysis</div>
                            <div class="text-sm text-gray-600">Generated: Jan 27, 2024 at 4:15 PM</div>
                        </div>
                        <div class="flex space-x-2">
                            <button class="text-indigo-600 hover:text-indigo-900 text-sm">View</button>
                            <button class="text-gray-600 hover:text-gray-900 text-sm">Download</button>
                        </div>
                    </div>
                    <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                        <div>
                            <div class="text-sm font-medium text-gray-900">Customer Aging Report</div>
                            <div class="text-sm text-gray-600">Generated: Jan 26, 2024 at 10:00 AM</div>
                        </div>
                        <div class="flex space-x-2">
                            <button class="text-indigo-600 hover:text-indigo-900 text-sm">View</button>
                            <button class="text-gray-600 hover:text-gray-900 text-sm">Download</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Report Generation Options -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Generate Custom Report</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Report Type</label>
                        <select class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Select Report Type</option>
                            <option value="customer_performance">Customer Performance</option>
                            <option value="sales_summary">Sales Summary</option>
                            <option value="product_analysis">Product Analysis</option>
                            <option value="team_performance">Team Performance</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                        <select class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="month">This Month</option>
                            <option value="quarter">This Quarter</option>
                            <option value="year">This Year</option>
                            <option value="custom">Custom Range</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Format</label>
                        <select class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="pdf">PDF</option>
                            <option value="excel">Excel</option>
                            <option value="web">Web View</option>
                        </select>
                    </div>
                </div>
                <div class="mt-6 flex justify-end">
                    <button type="button" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                        <i class="fas fa-chart-bar mr-2"></i>Generate Report
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
