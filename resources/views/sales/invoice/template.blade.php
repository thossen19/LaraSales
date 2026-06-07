@extends('layouts.app')

@section('title', 'Template Invoice - Sales ERP')

@section('content')
    <div>
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900">Template Invoice</h2>
            <p class="mt-2 text-gray-600">Create invoices using predefined templates for recurring billing.</p>
        </div>

            <!-- Template Selection -->
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Select Invoice Template</h3>
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
                        <label class="block text-sm font-medium text-gray-700 mb-2">Billing Cycle</label>
                        <select class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">All Cycles</option>
                            <option value="monthly">Monthly</option>
                            <option value="quarterly">Quarterly</option>
                            <option value="yearly">Yearly</option>
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
                <h3 class="text-lg font-medium text-gray-900 mb-4">Available Invoice Templates</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Template Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Billing Cycle</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Used</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Monthly Software License</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">ABC Corporation</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Monthly</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">3 items</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">2024-01-01</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button type="button" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                        <i class="fas fa-file-invoice mr-1"></i>Use Template
                                    </button>
                                    <button type="button" class="text-gray-600 hover:text-gray-900">
                                        <i class="fas fa-edit mr-1"></i>Edit
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Quarterly Maintenance</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">XYZ Industries</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Quarterly</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">5 items</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">2024-01-01</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button type="button" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                        <i class="fas fa-file-invoice mr-1"></i>Use Template
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

            <!-- Invoice Form (Hidden by default) -->
            <div class="bg-white shadow rounded-lg p-6" id="templateInvoiceForm" style="display: none;">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-medium text-gray-900">Create Invoice from Template</h3>
                    <div class="text-sm text-gray-600">
                        Template: <span class="font-medium" id="selectedTemplate">Monthly Software License</span>
                    </div>
                </div>

                <form>
                    <!-- Invoice Details -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Invoice Date *</label>
                            <input type="date" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Due Date *</label>
                            <input type="date" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Payment Terms</label>
                            <select class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="0">Due on Receipt</option>
                                <option value="7">Net 7 Days</option>
                                <option value="15">Net 15 Days</option>
                                <option value="30" selected>Net 30 Days</option>
                                <option value="60">Net 60 Days</option>
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
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Discount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tax</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">ERP Software License</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Monthly subscription for ERP system</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="number" class="border border-gray-300 rounded-md px-2 py-1 text-sm w-20" value="1" min="0">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="number" class="border border-gray-300 rounded-md px-2 py-1 text-sm w-24" value="500.00" step="0.01">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="number" class="border border-gray-300 rounded-md px-2 py-1 text-sm w-20" value="0" step="0.01">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <select class="border border-gray-300 rounded-md px-2 py-1 text-sm">
                                                <option>10%</option>
                                                <option>0%</option>
                                                <option>Exempt</option>
                                            </select>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$550.00</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <button type="button" class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Cloud Storage</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Additional cloud storage (100GB)</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="number" class="border border-gray-300 rounded-md px-2 py-1 text-sm w-20" value="1" min="0">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="number" class="border border-gray-300 rounded-md px-2 py-1 text-sm w-24" value="50.00" step="0.01">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="number" class="border border-gray-300 rounded-md px-2 py-1 text-sm w-20" value="0" step="0.01">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <select class="border border-gray-300 rounded-md px-2 py-1 text-sm">
                                                <option>10%</option>
                                                <option>0%</option>
                                                <option>Exempt</option>
                                            </select>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$55.00</td>
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

                    <!-- Totals -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Invoice Notes</label>
                            <textarea class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" rows="4" placeholder="Enter invoice notes...">Monthly recurring invoice for software licenses and services.</textarea>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Subtotal:</span>
                                <span class="text-sm font-medium">$550.00</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Discount:</span>
                                <span class="text-sm font-medium">$0.00</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Tax (10%):</span>
                                <span class="text-sm font-medium">$55.00</span>
                            </div>
                            <div class="flex justify-between text-lg font-bold border-t pt-2">
                                <span>Total:</span>
                                <span>$605.00</span>
                            </div>
                        </div>
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
                                <span class="text-sm">Update original template prices</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="mr-2">
                                <span class="text-sm">Schedule automatic recurring invoices</span>
                            </label>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end space-x-4">
                        <button type="button" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </button>
                        <button type="button" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-save mr-2"></i>Save Draft
                        </button>
                        <button type="button" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-print mr-2"></i>Print
                        </button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            <i class="fas fa-file-invoice mr-2"></i>Create Invoice
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Simulate clicking use template button to show invoice form
        document.addEventListener('click', function(e) {
            if (e.target.closest('button') && e.target.closest('button').textContent.includes('Use Template')) {
                e.preventDefault();
                document.getElementById('templateInvoiceForm').style.display = 'block';
                document.getElementById('templateInvoiceForm').scrollIntoView({ behavior: 'smooth' });
            }
        });
    </script>
@endpush
