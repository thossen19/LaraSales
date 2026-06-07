@extends('layouts.app')

@section('title', 'Customer Reports - Sales ERP')

@section('content')
    <div class="flex gap-6">
        @include('components.sales-sidebar')
        
        <div class="flex-1">
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900">Customer Reports</h2>
                <p class="mt-2 text-gray-600">Generate comprehensive customer analysis and performance reports.</p>
            </div>

            <!-- Report Parameters -->
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Report Parameters</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Report Type</label>
                        <select class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="customer_performance">Customer Performance</option>
                            <option value="customer_aging">Customer Aging</option>
                            <option value="customer_ranking">Customer Ranking</option>
                            <option value="customer_summary">Customer Summary</option>
                            <option value="customer_activity">Customer Activity</option>
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
                        <label class="block text-sm font-medium text-gray-700 mb-2">Customer Group</label>
                        <select class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">All Groups</option>
                            <option value="1">Regular Customers</option>
                            <option value="2">VIP Customers</option>
                            <option value="3">Wholesale</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sales Person</label>
                        <select class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">All Sales Persons</option>
                            <option value="1">John Smith</option>
                            <option value="2">Jane Doe</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end space-x-4">
                    <button type="button" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-cog mr-2"></i>Advanced Options
                    </button>
                    <button type="button" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                        <i class="fas fa-chart-bar mr-2"></i>Generate Report
                    </button>
                </div>
            </div>

            <!-- Customer Performance Report -->
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-medium text-gray-900">Customer Performance Report</h3>
                    <div class="text-sm text-gray-600">Period: January 2024</div>
                </div>

                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-100 text-sm">Total Customers</p>
                                <p class="text-3xl font-bold">45</p>
                            </div>
                            <div class="text-blue-200">
                                <i class="fas fa-users text-4xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-green-100 text-sm">Active Customers</p>
                                <p class="text-3xl font-bold">38</p>
                            </div>
                            <div class="text-green-200">
                                <i class="fas fa-user-check text-4xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-purple-100 text-sm">Avg. Order Value</p>
                                <p class="text-3xl font-bold">$2,450</p>
                            </div>
                            <div class="text-purple-200">
                                <i class="fas fa-dollar-sign text-4xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-orange-100 text-sm">Total Revenue</p>
                                <p class="text-3xl font-bold">$93,150</p>
                            </div>
                            <div class="text-orange-200">
                                <i class="fas fa-chart-line text-4xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Customer Ranking Table -->
                <div class="mb-8">
                    <h4 class="text-md font-medium text-gray-900 mb-4">Top Customers by Revenue</h4>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rank</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orders</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Revenue</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg. Order</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Growth</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">1</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">ABC Corporation</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">12</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$28,750.00</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$2,395.83</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">+15.2%</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">2</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">XYZ Industries</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">8</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$18,200.00</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$2,275.00</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">+8.5%</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">3</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Global Tech Ltd</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">15</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$15,450.00</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$1,030.00</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">-3.2%</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">4</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Innovate Solutions</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">6</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$12,800.00</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$2,133.33</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">+22.1%</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">5</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Tech Components Inc</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">10</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$10,500.00</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$1,050.00</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">+5.8%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h4 class="text-md font-medium text-gray-900 mb-4">Revenue by Customer Segment</h4>
                        <div class="h-64 flex items-center justify-center text-gray-500">
                            <div class="text-center">
                                <i class="fas fa-chart-pie text-4xl mb-2"></i>
                                <p>Customer segment distribution chart</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h4 class="text-md font-medium text-gray-900 mb-4">Monthly Customer Trends</h4>
                        <div class="h-64 flex items-center justify-center text-gray-500">
                            <div class="text-center">
                                <i class="fas fa-chart-line text-4xl mb-2"></i>
                                <p>Customer acquisition and retention trends</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Aging Report -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Customer Aging Analysis</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Aging Summary -->
                    <div>
                        <h4 class="text-md font-medium text-gray-900 mb-4">Aging Summary</h4>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Current (0-30 days)</span>
                                <div class="flex items-center">
                                    <div class="w-32 bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-green-600 h-2 rounded-full" style="width: 65%"></div>
                                    </div>
                                    <span class="text-sm font-medium">$45,200.00</span>
                                </div>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">31-60 days</span>
                                <div class="flex items-center">
                                    <div class="w-32 bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-yellow-600 h-2 rounded-full" style="width: 25%"></div>
                                    </div>
                                    <span class="text-sm font-medium">$17,400.00</span>
                                </div>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">61-90 days</span>
                                <div class="flex items-center">
                                    <div class="w-32 bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-orange-600 h-2 rounded-full" style="width: 8%"></div>
                                    </div>
                                    <span class="text-sm font-medium">$5,550.00</span>
                                </div>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">90+ days</span>
                                <div class="flex items-center">
                                    <div class="w-32 bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-red-600 h-2 rounded-full" style="width: 2%"></div>
                                    </div>
                                    <span class="text-sm font-medium">$1,400.00</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Aging Table -->
                    <div>
                        <h4 class="text-md font-medium text-gray-900 mb-4">Customer Aging Details</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">31-60</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">61-90</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">90+</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">ABC Corporation</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$8,500.00</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$3,200.00</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$0.00</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$0.00</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">$11,700.00</td>
                                    </tr>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">XYZ Industries</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$12,000.00</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$0.00</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$2,800.00</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$0.00</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">$14,800.00</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Report Actions -->
            <div class="flex justify-end space-x-4 mt-6">
                <button type="button" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-save mr-2"></i>Save Report
                </button>
                <button type="button" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-download mr-2"></i>Export PDF
                </button>
                <button type="button" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-print mr-2"></i>Print
                </button>
                <button type="button" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-envelope mr-2"></i>Email
                </button>
            </div>
        </div>
    </div>
@endsection
