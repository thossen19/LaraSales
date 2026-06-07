@extends('layouts.app')

@section('title', 'Recurrent Invoices Setup - Sales ERP')

@section('content')
    <div>
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900">Recurrent Invoices Setup</h2>
            <p class="mt-2 text-gray-600">Configure and manage recurring invoice templates and schedules.</p>
        </div>

            <!-- Add New Recurrent Invoice Template -->
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Create Recurrent Invoice Template</h3>
                <form>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Template Name *</label>
                            <input type="text" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Enter template name" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Customer *</label>
                            <select class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                                <option value="">Select Customer</option>
                                <option value="1">ABC Corporation</option>
                                <option value="2">XYZ Industries</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Billing Cycle *</label>
                            <select class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                                <option value="daily">Daily</option>
                                <option value="weekly">Weekly</option>
                                <option value="monthly">Monthly</option>
                                <option value="quarterly">Quarterly</option>
                                <option value="yearly">Yearly</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Next Invoice Date *</label>
                            <input type="date" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                            <input type="date" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
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
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Auto-Generate</label>
                            <select class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Send Email</label>
                            <select class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                            </select>
                        </div>
                    </div>

                    <!-- Line Items -->
                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="text-md font-medium text-gray-900">Invoice Items</h4>
                            <button type="button" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 text-sm">
                                <i class="fas fa-plus mr-2"></i>Add Item
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
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tax</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <select class="border border-gray-300 rounded-md px-2 py-1 text-sm">
                                                <option>Select Item</option>
                                                <option>Software License</option>
                                                <option>Cloud Storage</option>
                                                <option>Support Plan</option>
                                            </select>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="text" class="border border-gray-300 rounded-md px-2 py-1 text-sm w-full" placeholder="Description">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="number" class="border border-gray-300 rounded-md px-2 py-1 text-sm w-20" value="1" min="1">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="number" class="border border-gray-300 rounded-md px-2 py-1 text-sm w-24" value="0.00" step="0.01">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <select class="border border-gray-300 rounded-md px-2 py-1 text-sm">
                                                <option>10%</option>
                                                <option>0%</option>
                                                <option>Exempt</option>
                                            </select>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$0.00</td>
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

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <textarea class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" rows="4" placeholder="Enter invoice notes..."></textarea>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <button type="button" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            <i class="fas fa-save mr-2"></i>Create Template
                        </button>
                    </div>
                </form>
            </div>

            <!-- Existing Recurrent Invoices -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Recurrent Invoice Templates</h3>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Template Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Billing Cycle</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Next Invoice</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Auto-Generate</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Monthly Software License</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">ABC Corporation</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Monthly</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">2024-02-01</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$605.00</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Yes</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button type="button" class="text-indigo-600 hover:text-indigo-900 mr-2">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="text-gray-600 hover:text-gray-900 mr-2">
                                        <i class="fas fa-play"></i>
                                    </button>
                                    <button type="button" class="text-gray-600 hover:text-gray-900">
                                        <i class="fas fa-pause"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Quarterly Maintenance</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">XYZ Industries</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Quarterly</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">2024-04-01</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$1,250.00</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Yes</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button type="button" class="text-indigo-600 hover:text-indigo-900 mr-2">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="text-gray-600 hover:text-gray-900 mr-2">
                                        <i class="fas fa-play"></i>
                                    </button>
                                    <button type="button" class="text-gray-600 hover:text-gray-900">
                                        <i class="fas fa-pause"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Office Space Rental</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Global Tech Ltd</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Monthly</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">2024-02-01</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$2,500.00</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Paused</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Yes</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button type="button" class="text-indigo-600 hover:text-indigo-900 mr-2">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="text-gray-600 hover:text-gray-900 mr-2">
                                        <i class="fas fa-play"></i>
                                    </button>
                                    <button type="button" class="text-gray-600 hover:text-gray-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recurrent Invoice Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-6">
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                            <i class="fas fa-sync text-white"></i>
                        </div>
                        <div class="ml-5">
                            <p class="text-sm font-medium text-gray-500 truncate">Total Templates</p>
                            <p class="text-lg font-medium text-gray-900">12</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                            <i class="fas fa-play-circle text-white"></i>
                        </div>
                        <div class="ml-5">
                            <p class="text-sm font-medium text-gray-500 truncate">Active</p>
                            <p class="text-lg font-medium text-gray-900">8</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                            <i class="fas fa-dollar-sign text-white"></i>
                        </div>
                        <div class="ml-5">
                            <p class="text-sm font-medium text-gray-500 truncate">Monthly Revenue</p>
                            <p class="text-lg font-medium text-gray-900">$15,680</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-orange-500 rounded-md p-3">
                            <i class="fas fa-calendar text-white"></i>
                        </div>
                        <div class="ml-5">
                            <p class="text-sm font-medium text-gray-500 truncate">Next Due</p>
                            <p class="text-lg font-medium text-gray-900">5</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Invoices -->
            <div class="bg-white shadow rounded-lg p-6 mt-6">
                <h4 class="text-md font-medium text-gray-900 mb-4">Upcoming Recurrent Invoices (Next 7 Days)</h4>
                <div class="space-y-3">
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <div>
                            <div class="text-sm font-medium text-gray-900">Monthly Software License - ABC Corporation</div>
                            <div class="text-sm text-gray-600">Due: Feb 1, 2024 | Amount: $605.00</div>
                        </div>
                        <button type="button" class="text-indigo-600 hover:text-indigo-900 text-sm">
                            Generate Now
                        </button>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <div>
                            <div class="text-sm font-medium text-gray-900">Office Space Rental - Global Tech Ltd</div>
                            <div class="text-sm text-gray-600">Due: Feb 1, 2024 | Amount: $2,500.00</div>
                        </div>
                        <button type="button" class="text-indigo-600 hover:text-indigo-900 text-sm">
                            Generate Now
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
