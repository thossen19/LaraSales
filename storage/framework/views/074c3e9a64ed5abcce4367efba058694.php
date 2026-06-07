<?php $__env->startSection('title', 'Customer Payment Allocation - Sales ERP'); ?>

<?php $__env->startSection('content'); ?>
    <div>
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900">Allocate Customer Payments or Credit Notes</h2>
            <p class="mt-2 text-gray-600">Allocate unapplied payments and credit notes to customer invoices.</p>
        </div>

            <!-- Customer Selection -->
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Select Customer</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Customer *</label>
                        <select class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                            <option value="">Select Customer</option>
                            <option value="1">ABC Corporation</option>
                            <option value="2">XYZ Industries</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Show</label>
                        <select class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="all">All Unallocated</option>
                            <option value="payments">Payments Only</option>
                            <option value="credits">Credit Notes Only</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="button" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                            <i class="fas fa-search mr-2"></i>Load Data
                        </button>
                    </div>
                </div>
            </div>

            <!-- Unallocated Payments and Credits -->
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Unallocated Payments & Credits</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <input type="checkbox" class="rounded">
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Number</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unallocated</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" class="rounded" checked>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Payment</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">PAY-2024-003</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">2024-01-26</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Bank Transfer</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$2,000.00</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$2,000.00</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button type="button" class="text-indigo-600 hover:text-indigo-900">
                                        <i class="fas fa-link"></i> Allocate
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" class="rounded" checked>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">Credit</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">CN-2024-002</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">2024-01-20</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">-</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$150.00</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$150.00</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button type="button" class="text-indigo-600 hover:text-indigo-900">
                                        <i class="fas fa-link"></i> Allocate
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Outstanding Invoices -->
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Outstanding Invoices</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice #</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paid Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance Due</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Days Overdue</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">INV-2024-003</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">2024-01-10</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">2024-02-09</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$3,500.00</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$0.00</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$3,500.00</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Unpaid</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">0</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">INV-2024-004</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">2024-01-05</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">2024-02-04</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$1,200.00</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$500.00</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$700.00</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Partial</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">5</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Allocation Form -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Allocate Selected Items</h3>
                
                <form>
                    <!-- Allocation Summary -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <h4 class="text-sm font-medium text-blue-800 mb-2">Allocation Summary</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div>
                                <span class="text-blue-600">Total Available:</span>
                                <span class="font-medium ml-2">$2,150.00</span>
                            </div>
                            <div>
                                <span class="text-blue-600">Total Invoices:</span>
                                <span class="font-medium ml-2">$4,200.00</span>
                            </div>
                            <div>
                                <span class="text-blue-600">Remaining Balance:</span>
                                <span class="font-medium ml-2">$2,050.00</span>
                            </div>
                        </div>
                    </div>

                    <!-- Allocation Details -->
                    <div class="space-y-4 mb-6">
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex justify-between items-center mb-3">
                                <h5 class="text-sm font-medium text-gray-900">Payment: PAY-2024-003 ($2,000.00)</h5>
                                <span class="text-sm text-green-600">Available: $2,000.00</span>
                            </div>
                            <div class="space-y-2">
                                <div class="flex justify-between items-center">
                                    <label class="text-sm text-gray-700">INV-2024-003 - $3,500.00</label>
                                    <input type="number" class="border border-gray-300 rounded-md px-2 py-1 text-sm w-24" value="2000.00" step="0.01" max="3500.00">
                                </div>
                            </div>
                        </div>
                        
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex justify-between items-center mb-3">
                                <h5 class="text-sm font-medium text-gray-900">Credit: CN-2024-002 ($150.00)</h5>
                                <span class="text-sm text-green-600">Available: $150.00</span>
                            </div>
                            <div class="space-y-2">
                                <div class="flex justify-between items-center">
                                    <label class="text-sm text-gray-700">INV-2024-004 - $700.00</label>
                                    <input type="number" class="border border-gray-300 rounded-md px-2 py-1 text-sm w-24" value="150.00" step="0.01" max="700.00">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Allocation Options -->
                    <div class="space-y-3 mb-6">
                        <label class="flex items-center">
                            <input type="checkbox" class="mr-2" checked>
                            <span class="text-sm text-gray-700">Auto-allocate oldest invoices first</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" class="mr-2">
                            <span class="text-sm text-gray-700">Write off small remaining balances</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" class="mr-2">
                            <span class="text-sm text-gray-700">Send payment confirmation emails</span>
                        </label>
                    </div>

                    <!-- Notes -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Allocation Notes</label>
                        <textarea class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" rows="3" placeholder="Enter allocation notes..."></textarea>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end space-x-4">
                        <button type="button" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </button>
                        <button type="button" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-calculator mr-2"></i>Recalculate
                        </button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            <i class="fas fa-link mr-2"></i>Process Allocation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        // Handle allocation calculations
        document.addEventListener('input', function(e) {
            if (e.target.type === 'number' && e.target.closest('form')) {
                // Simple allocation calculation logic would go here
                console.log('Allocation amount changed:', e.target.value);
            }
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lupu\Desktop\laravel\fa-saas\resources\views/sales/allocation/index.blade.php ENDPATH**/ ?>