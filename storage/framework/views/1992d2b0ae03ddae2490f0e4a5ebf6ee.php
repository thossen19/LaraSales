<?php $__env->startSection('title', 'Invoice Prepaid Orders - Sales ERP'); ?>

<?php $__env->startSection('content'); ?>
    <div>
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900">Invoice Prepaid Orders</h2>
            <p class="mt-2 text-gray-600">Create invoices for orders that have been prepaid by customers.</p>
        </div>

            <!-- Prepaid Order Selection -->
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Select Prepaid Order</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Order Number</label>
                        <input type="text" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Enter order number">
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
                        <label class="block text-sm font-medium text-gray-700 mb-2">Payment Status</label>
                        <select class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="prepaid">Prepaid</option>
                            <option value="partial">Partially Paid</option>
                        </select>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="button" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                        <i class="fas fa-search mr-2"></i>Search Orders
                    </button>
                </div>
            </div>

            <!-- Available Prepaid Orders -->
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Available Prepaid Orders</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order #</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prepaid Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">SO-2024-003</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">ABC Corporation</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">2024-01-20</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$2,500.00</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$2,500.00</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$0.00</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Fully Prepaid</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button type="button" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                        <i class="fas fa-file-invoice mr-1"></i>Invoice
                                    </button>
                                    <button type="button" class="text-gray-600 hover:text-gray-900">
                                        <i class="fas fa-eye mr-1"></i>View
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">SO-2024-004</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">XYZ Industries</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">2024-01-21</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$4,200.00</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$2,100.00</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$2,100.00</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Partially Paid</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button type="button" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                        <i class="fas fa-file-invoice mr-1"></i>Invoice
                                    </button>
                                    <button type="button" class="text-gray-600 hover:text-gray-900">
                                        <i class="fas fa-eye mr-1"></i>View
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Invoice Form (Hidden by default) -->
            <div class="bg-white shadow rounded-lg p-6" id="prepaidInvoiceForm" style="display: none;">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-medium text-gray-900">Create Invoice for Prepaid Order</h3>
                    <div class="text-sm text-gray-600">
                        Order: <span class="font-medium" id="selectedOrder">SO-2024-003</span> | 
                        Customer: <span class="font-medium" id="selectedCustomer">ABC Corporation</span>
                    </div>
                </div>

                <form>
                    <!-- Invoice Details -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Invoice Date *</label>
                            <input type="date" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" value="<?php echo e(date('Y-m-d')); ?>" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Due Date *</label>
                            <input type="date" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" value="<?php echo e(date('Y-m-d')); ?>" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Payment Status</label>
                            <select class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="paid">Paid in Full</option>
                                <option value="partial">Partially Paid</option>
                            </select>
                        </div>
                    </div>

                    <!-- Payment Information -->
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                        <h4 class="text-sm font-medium text-green-800 mb-2">Payment Information</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div>
                                <span class="text-green-600">Order Total:</span>
                                <span class="font-medium ml-2">$2,500.00</span>
                            </div>
                            <div>
                                <span class="text-green-600">Prepaid Amount:</span>
                                <span class="font-medium ml-2">$2,500.00</span>
                            </div>
                            <div>
                                <span class="text-green-600">Balance Due:</span>
                                <span class="font-medium ml-2">$0.00</span>
                            </div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="mb-6">
                        <h4 class="text-md font-medium text-gray-900 mb-4">Order Items</h4>
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
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Laptop Computer</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">High-performance laptop with 16GB RAM</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">3</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$800.00</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">0%</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">10%</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$2,640.00</td>
                                    </tr>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Mouse</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Wireless optical mouse</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">3</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$25.00</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">0%</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">10%</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$82.50</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Totals -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Invoice Notes</label>
                            <textarea class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" rows="4" placeholder="Enter invoice notes..."></textarea>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Subtotal:</span>
                                <span class="text-sm font-medium">$2,475.00</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Discount:</span>
                                <span class="text-sm font-medium">$0.00</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Tax (10%):</span>
                                <span class="text-sm font-medium">$247.50</span>
                            </div>
                            <div class="flex justify-between text-lg font-bold border-t pt-2">
                                <span>Total:</span>
                                <span>$2,722.50</span>
                            </div>
                            <div class="flex justify-between text-sm font-medium text-green-600 border-t pt-2 mt-2">
                                <span>Prepaid:</span>
                                <span>-$2,722.50</span>
                            </div>
                            <div class="flex justify-between text-lg font-bold text-green-600">
                                <span>Balance Due:</span>
                                <span>$0.00</span>
                            </div>
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
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                            <i class="fas fa-file-invoice mr-2"></i>Create Paid Invoice
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        // Simulate clicking invoice button to show invoice form
        document.addEventListener('click', function(e) {
            if (e.target.closest('button') && e.target.closest('button').textContent.includes('Invoice')) {
                e.preventDefault();
                document.getElementById('prepaidInvoiceForm').style.display = 'block';
                document.getElementById('prepaidInvoiceForm').scrollIntoView({ behavior: 'smooth' });
            }
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lupu\Desktop\laravel\fa-saas\resources\views/sales/invoice/prepaid.blade.php ENDPATH**/ ?>