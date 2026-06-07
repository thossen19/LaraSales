@extends('layouts.app')

@section('title', 'Template Delivery - Sales ERP')

@section('content')
    <div>
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900">Template Delivery</h2>
            <p class="mt-2 text-gray-600">Create deliveries using predefined templates for recurring deliveries.</p>
        </div>

            <!-- Template Selection -->
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Select Delivery Template</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Template Name</label>
                        <input type="text" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Enter template name">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Customer</label>
                        <select class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">All Customers</option>
                            <option value="1">ABC Corporation</option>
                            <option value="2">XYZ Industries</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Frequency</label>
                        <select class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">All Frequencies</option>
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                        </select>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="button" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                        <i class="fas fa-search mr-2"></i>Search Templates
                    </button>
                </div>
            </div>

            <!-- Available Templates -->
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Available Delivery Templates</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Template Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Frequency</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Used</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Monthly Office Supplies</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">ABC Corporation</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Monthly</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">5 items</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">2024-01-01</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button type="button" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                        <i class="fas fa-truck mr-1"></i>Use Template
                                    </button>
                                    <button type="button" class="text-gray-600 hover:text-gray-900">
                                        <i class="fas fa-edit mr-1"></i>Edit
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Weekly IT Equipment</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">XYZ Industries</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Weekly</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">3 items</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">2024-01-15</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button type="button" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                        <i class="fas fa-truck mr-1"></i>Use Template
                                    </button>
                                    <button type="button" class="text-gray-600 hover:text-gray-900">
                                        <i class="fas fa-edit mr-1"></i>Edit
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Delivery Form (Hidden by default) -->
            <div class="bg-white shadow rounded-lg p-6" id="templateDeliveryForm" style="display: none;">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-medium text-gray-900">Create Delivery from Template</h3>
                    <div class="text-sm text-gray-600">
                        Template: <span class="font-medium" id="selectedTemplate">Monthly Office Supplies</span>
                    </div>
                </div>

                <form>
                    <!-- Delivery Details -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Date *</label>
                            <input type="date" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Warehouse *</label>
                            <select class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                                <option value="">Select Warehouse</option>
                                <option value="1">Main Warehouse</option>
                                <option value="2">Secondary Warehouse</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Person</label>
                            <select class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">Select Person</option>
                                <option value="1">John Smith</option>
                                <option value="2">Jane Doe</option>
                            </select>
                        </div>
                    </div>

                    <!-- Template Items -->
                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="text-md font-medium text-gray-900">Template Items</h4>
                            <button type="button" class="text-indigo-600 hover:text-indigo-900 text-sm">
                                <i class="fas fa-plus mr-1"></i>Add Custom Item
                            </button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Template Qty</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Delivery Qty</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Office Paper</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">A4 premium office paper</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">10 boxes</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="number" class="border border-gray-300 rounded-md px-2 py-1 text-sm w-20" value="10" min="0">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Boxes</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <button type="button" class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Pens</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Blue ballpoint pens</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">50 units</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="number" class="border border-gray-300 rounded-md px-2 py-1 text-sm w-20" value="50" min="0">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Units</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <button type="button" class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Delivery Address -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Address</label>
                        <textarea class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" rows="3" placeholder="Enter delivery address...">123 Business Ave, Suite 100
New York, NY 10001</textarea>
                    </div>

                    <!-- Delivery Notes -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Notes</label>
                        <textarea class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" rows="4" placeholder="Enter delivery notes..."></textarea>
                    </div>

                    <!-- Template Options -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <h4 class="text-sm font-medium text-blue-800 mb-2">Template Options</h4>
                        <div class="flex items-center space-x-4">
                            <label class="flex items-center">
                                <input type="checkbox" class="mr-2" checked>
                                <span class="text-sm">Save as new template for future use</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="mr-2">
                                <span class="text-sm">Update original template quantities</span>
                            </label>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end space-x-4">
                        <button type="button" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </button>
                        <button type="button" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-print mr-2"></i>Print
                        </button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            <i class="fas fa-truck mr-2"></i>Process Delivery
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Simulate clicking use template button to show delivery form
        document.addEventListener('click', function(e) {
            if (e.target.closest('button') && e.target.closest('button').textContent.includes('Use Template')) {
                e.preventDefault();
                document.getElementById('templateDeliveryForm').style.display = 'block';
                document.getElementById('templateDeliveryForm').scrollIntoView({ behavior: 'smooth' });
            }
        });
    </script>
@endpush
