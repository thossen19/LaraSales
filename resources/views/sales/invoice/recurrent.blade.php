@extends('layouts.app')

@section('title', 'Create and Print Recurrent Invoices - Sales ERP')

@section('content')
    <div>
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900">Create and Print Recurrent Invoices</h2>
            <p class="mt-2 text-gray-600">Generate and print multiple recurring invoices in batch.</p>
        </div>

            <!-- Recurrent Invoice Selection -->
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Select Recurrent Invoices to Generate</h3>
                
                <!-- Filter Options -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Billing Period</label>
                        <select class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="current">Current Period</option>
                            <option value="next">Next Period</option>
                            <option value="previous">Previous Period</option>
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
                        <label class="block text-sm font-medium text-gray-700 mb-2">Invoice Type</label>
                        <select class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">All Types</option>
                            <option value="service">Service Invoices</option>
                            <option value="license">License Invoices</option>
                            <option value="rental">Rental Invoices</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="pending">Pending Generation</option>
                            <option value="generated">Already Generated</option>
                            <option value="all">All</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-between items-center">
                    <button type="button" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                        <i class="fas fa-search mr-2"></i>Filter Invoices
                    </button>
                    <div class="text-sm text-gray-600">
                        Found <span class="font-medium text-indigo-600">15</span> recurrent invoices
                    </div>
                </div>
            </div>

            <!-- Available Recurrent Invoices -->
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Recurrent Invoices</h3>
                    <div class="flex space-x-2">
                        <button type="button" class="text-sm text-gray-600 hover:text-gray-900">
                            <i class="fas fa-check-square mr-1"></i>Select All
                        </button>
                        <button type="button" class="text-sm text-gray-600 hover:text-gray-900">
                            <i class="fas fa-square mr-1"></i>Deselect All
                        </button>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <input type="checkbox" class="rounded">
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Template Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Frequency</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Next Invoice Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Generated</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" class="rounded" checked>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Monthly Software License</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">ABC Corporation</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Monthly</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">2024-02-01</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$605.00</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">2024-01-01</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button type="button" class="text-indigo-600 hover:text-indigo-900 mr-2">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="text-gray-600 hover:text-gray-900">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" class="rounded" checked>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Quarterly Maintenance</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">XYZ Industries</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Quarterly</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">2024-04-01</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$1,250.00</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">2024-01-01</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button type="button" class="text-indigo-600 hover:text-indigo-900 mr-2">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="text-gray-600 hover:text-gray-900">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" class="rounded">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Office Space Rental</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Global Tech Ltd</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Monthly</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">2024-02-01</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$2,500.00</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">2024-01-01</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Generated</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button type="button" class="text-indigo-600 hover:text-indigo-900 mr-2">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="text-gray-600 hover:text-gray-900">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Batch Generation Options -->
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Batch Generation Options</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Invoice Date</label>
                        <input type="date" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" value="{{ date('Y-m-d') }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Due Date</label>
                        <input type="date" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" value="{{ date('Y-m-d', strtotime('+30 days')) }}">
                    </div>
                </div>

                <div class="space-y-3 mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" class="mr-2" checked>
                        <span class="text-sm text-gray-700">Generate invoices immediately</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" class="mr-2" checked>
                        <span class="text-sm text-gray-700">Send email notifications to customers</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" class="mr-2">
                        <span class="text-sm text-gray-700">Schedule for later date/time</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" class="mr-2">
                        <span class="text-sm text-gray-700">Create draft invoices for review first</span>
                    </label>
                </div>

                <!-- Preview Summary -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <h4 class="text-sm font-medium text-blue-800 mb-2">Generation Summary</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="text-blue-600">Selected Invoices:</span>
                            <span class="font-medium ml-2">2</span>
                        </div>
                        <div>
                            <span class="text-blue-600">Total Amount:</span>
                            <span class="font-medium ml-2">$1,855.00</span>
                        </div>
                        <div>
                            <span class="text-blue-600">Customers:</span>
                            <span class="font-medium ml-2">2</span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end space-x-4">
                    <button type="button" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </button>
                    <button type="button" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-file-alt mr-2"></i>Preview Selected
                    </button>
                    <button type="button" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-print mr-2"></i>Print Preview
                    </button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        <i class="fas fa-play mr-2"></i>Generate Invoices
                    </button>
                </div>
            </div>

            <!-- Generation Progress (Hidden by default) -->
            <div class="bg-white shadow rounded-lg p-6" id="generationProgress" style="display: none;">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Generating Invoices...</h3>
                
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span>Processing ABC Corporation - Monthly Software License</span>
                            <span class="text-green-600">✓ Complete</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full" style="width: 100%"></div>
                        </div>
                    </div>
                    
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span>Processing XYZ Industries - Quarterly Maintenance</span>
                            <span class="text-blue-600">⏳ In Progress</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: 60%"></div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-sm text-green-800">
                        <i class="fas fa-check-circle mr-2"></i>
                        Successfully generated 1 of 2 invoices. Invoice #INV-2024-0151 created for ABC Corporation.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Simulate invoice generation process
        document.addEventListener('click', function(e) {
            if (e.target.closest('button') && e.target.closest('button').textContent.includes('Generate Invoices')) {
                e.preventDefault();
                document.getElementById('generationProgress').style.display = 'block';
                document.getElementById('generationProgress').scrollIntoView({ behavior: 'smooth' });
            }
        });

        // Handle select all functionality
        document.addEventListener('change', function(e) {
            if (e.target.type === 'checkbox' && e.target.closest('thead')) {
                const checkboxes = document.querySelectorAll('tbody input[type="checkbox"]');
                checkboxes.forEach(cb => cb.checked = e.target.checked);
            }
        });
    </script>
@endpush
