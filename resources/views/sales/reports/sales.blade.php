@extends('layouts.app')

@section('title', 'Sales Reports - Sales ERP')

@section('content')
    <div class="flex gap-6">
        @include('components.sales-sidebar')
        
        <div class="flex-1">
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900">Sales Reports</h2>
                <p class="mt-2 text-gray-600">Generate comprehensive sales analysis and performance reports.</p>
            </div>

            <!-- Report Parameters -->
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Report Parameters</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Report Type</label>
                        <select class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="sales_performance">Sales Performance</option>
                            <option value="sales_trend">Sales Trend Analysis</option>
                            <option value="product_sales">Product Sales Analysis</option>
                            <option value="salesperson_performance">Sales Person Performance</option>
                            <option value="regional_sales">Regional Sales</option>
                            <option value="sales_summary">Sales Summary</option>
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
                        <label class="block text-sm font-medium text-gray-700 mb-2">Comparison</label>
                        <select class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">No Comparison</option>
                            <option value="previous_month">Previous Month</option>
                            <option value="previous_quarter">Previous Quarter</option>
                            <option value="previous_year">Previous Year</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Group By</label>
                        <select class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="day">Daily</option>
                            <option value="week">Weekly</option>
                            <option value="month">Monthly</option>
                            <option value="quarter">Quarterly</option>
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

            <!-- Sales Performance Report -->
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-medium text-gray-900">Sales Performance Report</h3>
                    <div class="text-sm text-gray-600">Period: January 2024</div>
                </div>

                <!-- Executive Summary -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-100 text-sm">Total Sales</p>
                                <p class="text-3xl font-bold">$156,890</p>
                            </div>
                            <div class="text-blue-200">
                                <i class="fas fa-dollar-sign text-4xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-green-100 text-sm">Orders</p>
                                <p class="text-3xl font-bold">248</p>
                            </div>
                            <div class="text-green-200">
                                <i class="fas fa-shopping-cart text-4xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-purple-100 text-sm">Avg. Order</p>
                                <p class="text-3xl font-bold">$632</p>
                            </div>
                            <div class="text-purple-200">
                                <i class="fas fa-receipt text-4xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-orange-100 text-sm">Growth</p>
                            <p class="text-3xl font-bold">+18.5%</p>
                            </div>
                            <div class="text-orange-200">
                                <i class="fas fa-chart-line text-4xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sales Trend Chart -->
                <div class="mb-8">
                    <h4 class="text-md font-medium text-gray-900 mb-4">Sales Trend Analysis</h4>
                    <div class="h-80 flex items-center justify-center text-gray-500">
                        <div class="text-center">
                            <i class="fas fa-chart-line text-4xl mb-2"></i>
                            <p>Sales trend chart showing monthly performance</p>
                        </div>
                    </div>
                </div>

                <!-- Sales by Category -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <div>
                        <h4 class="text-md font-medium text-gray-900 mb-4">Sales by Product Category</h4>
                        <div class="h-64 flex items-center justify-center text-gray-500">
                            <div class="text-center">
                                <i class="fas fa-chart-bar text-4xl mb-2"></i>
                                <p>Product category sales distribution</p>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-md font-medium text-gray-900 mb-4">Sales by Region</h4>
                        <div class="h-64 flex items-center justify-center text-gray-500">
                            <div class="text-center">
                                <i class="fas fa-globe text-4xl mb-2"></i>
                                <p>Regional sales distribution</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top Products Table -->
                <div class="mb-8">
                    <h4 class="text-md font-medium text-gray-900 mb-4">Top Performing Products</h4>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Units Sold</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">% of Total</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Growth</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Laptop Computer</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">45</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$45,000.00</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">28.7%</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">+22.5%</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Office Chair</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">120</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$18,000.00</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">11.5%</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">+15.8%</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Software License</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">85</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$25,500.00</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">16.3%</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">+35.2%</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Monitor 24"</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">32</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$12,800.00</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">8.2%</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">-5.3%</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Keyboard</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-sm text-gray-900">78</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$7,800.00</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">5.0%</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">+8.9%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Sales Person Performance -->
                <div class="mb-8">
                    <h4 class="text-md font-medium text-gray-900 mb-4">Sales Person Performance</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sales Person</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orders</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Target</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">% Achieved</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">John Smith</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">85</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$68,450.00</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$60,000.00</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">114.1%</td>
                                        </tr>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Jane Doe</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">72</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$52,800.00</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$55,000.00</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">96.0%</td>
                                        </tr>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Mike Johnson</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">63</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$35,640.00</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$40,000.00</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">89.1%</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div>
                            <h5 class="text-sm font-medium text-gray-900 mb-3">Sales Performance Metrics</h5>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Conversion Rate</span>
                                    <span class="text-sm font-medium text-green-600">68.5%</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Average Deal Size</span>
                                    <span class="text-sm font-medium">$632</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Sales Cycle</span>
                                <span class="text-sm font-medium">21 days</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Quotation to Order</span>
                                    <span class="text-sm font-medium text-green-600">42%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Forecast vs Actual -->
                <div class="mb-8">
                    <h4 class="text-md font-medium text-gray-900 mb-4">Forecast vs Actual Performance</h4>
                    <div class="h-64 flex items-center justify-center text-gray-500">
                        <div class="text-center">
                            <i class="fas fa-chart-area text-4xl mb-2"></i>
                            <p>Forecast vs actual sales comparison</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Report Actions -->
            <div class="flex justify-end space-x-4">
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
