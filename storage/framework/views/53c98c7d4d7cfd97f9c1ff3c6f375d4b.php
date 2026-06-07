<?php $__env->startSection('title', 'Order #' . $order->order_number . ' - Sales ERP'); ?>

<?php $__env->startSection('content'); ?>
    <div>
        <div class="mb-6 flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Order #<?php echo e($order->order_number); ?></h2>
                    <p class="mt-1 text-gray-600">
                        Created on <?php echo e($order->created_at->format('d/m/Y')); ?> 
                        | Status: <?php echo $order->status_badge; ?>

                    </p>
                </div>
                <div class="flex gap-2">
                    <a href="<?php echo e(route('sales.orders.edit', $order)); ?>" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition"><i class="fas fa-edit mr-1"></i>Edit</a>
                    <a href="<?php echo e(route('sales.inquiries.orders')); ?>" class="px-4 py-2 bg-white text-gray-700 text-sm font-medium rounded-md hover:bg-gray-100 transition border border-gray-300"><i class="fas fa-arrow-left mr-1"></i>Back</a>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg overflow-hidden mb-6">
                <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700">
                    <h3 class="text-lg font-semibold text-white"><i class="fas fa-info-circle mr-2"></i>Order Details</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <p class="text-sm text-gray-500">Customer</p>
                            <p class="text-sm font-medium text-gray-900"><?php echo e($order->customer->name ?? 'N/A'); ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Branch</p>
                            <p class="text-sm font-medium text-gray-900"><?php echo e($order->customerBranch->branch_name ?? 'N/A'); ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Sales Person</p>
                            <p class="text-sm font-medium text-gray-900"><?php echo e($order->salesPerson->name ?? 'N/A'); ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Sales Type</p>
                            <p class="text-sm font-medium text-gray-900"><?php echo e($order->salesType->type_name ?? 'N/A'); ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Order Date</p>
                            <p class="text-sm font-medium text-gray-900"><?php echo e($order->order_date ? $order->order_date->format('d/m/Y') : '-'); ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Delivery Date</p>
                            <p class="text-sm font-medium text-gray-900"><?php echo e($order->delivery_date ? $order->delivery_date->format('d/m/Y') : '-'); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg overflow-hidden mb-6">
                <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700">
                    <h3 class="text-lg font-semibold text-white"><i class="fas fa-shopping-cart mr-2"></i>Line Items</h3>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Item Code</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Description</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Quantity</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Unit Price</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Discount</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Total</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php $__empty_1 = true; $__currentLoopData = $order->lineItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm text-gray-900"><?php echo e($line->item_code); ?></td>
                                        <td class="px-4 py-3 text-sm text-gray-700"><?php echo e($line->description); ?></td>
                                        <td class="px-4 py-3 text-sm text-right text-gray-700"><?php echo e(number_format($line->quantity, 2)); ?></td>
                                        <td class="px-4 py-3 text-sm text-right text-gray-700"><?php echo e(number_format($line->unit_price, 2)); ?></td>
                                        <td class="px-4 py-3 text-sm text-right text-gray-700"><?php echo e(number_format($line->discount_percentage, 2)); ?>%</td>
                                        <td class="px-4 py-3 text-sm text-right font-medium text-gray-900"><?php echo e(number_format($line->line_total, 2)); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">No line items found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700">
                    <h3 class="text-lg font-semibold text-white"><i class="fas fa-calculator mr-2"></i>Summary</h3>
                </div>
                <div class="p-6">
                    <div class="flex justify-end">
                        <div class="w-72 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Subtotal:</span>
                                <span class="font-medium text-gray-900">$<?php echo e(number_format($order->subtotal, 2)); ?></span>
                            </div>
                            <?php if($order->discount_amount > 0): ?>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Discount:</span>
                                <span class="font-medium text-red-600">-$<?php echo e(number_format($order->discount_amount, 2)); ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if($order->tax_amount > 0): ?>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Tax:</span>
                                <span class="font-medium text-gray-900">$<?php echo e(number_format($order->tax_amount, 2)); ?></span>
                            </div>
                            <?php endif; ?>
                            <div class="flex justify-between text-base font-bold pt-2 border-t border-gray-200">
                                <span class="text-gray-900">Total:</span>
                                <span class="text-indigo-700">$<?php echo e(number_format($order->total_amount, 2)); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lupu\Desktop\laravel\fa-saas\resources\views/sales/orders/show.blade.php ENDPATH**/ ?>