<?php $__env->startSection('title', 'Search Purchase Orders - Sales ERP'); ?>

<?php $__env->startSection('content'); ?>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Search Purchase Orders</h2>
    </div>

    <form method="GET" action="<?php echo e(route('purchases.inquiries.orders')); ?>" class="mb-6">
        <div class="bg-white shadow rounded-lg p-4 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700">#</label>
                    <input type="text" name="po_no" value="<?php echo e($po_no); ?>"
                           class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="Order number">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">from:</label>
                    <input type="date" name="from_date" value="<?php echo e($from_date); ?>"
                           class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           <?php if($po_no): ?> disabled <?php endif; ?>>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">to:</label>
                    <input type="date" name="to_date" value="<?php echo e($to_date); ?>"
                           class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           <?php if($po_no): ?> disabled <?php endif; ?>>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">into location:</label>
                    <select name="location"
                            class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            <?php if($po_no): ?> disabled <?php endif; ?>>
                        <option value="">All</option>
                        <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($loc->code); ?>" <?php echo e($location == $loc->code ? 'selected' : ''); ?>><?php echo e($loc->location_name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700">for item:</label>
                    <select name="item_code"
                            class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            <?php if($po_no): ?> disabled <?php endif; ?>>
                        <option value="">All</option>
                        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $itm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($itm->code); ?>" <?php echo e($item_code == $itm->code ? 'selected' : ''); ?>><?php echo e($itm->code); ?> - <?php echo e($itm->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Select a supplier:</label>
                    <select name="supplier_id"
                            class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All</option>
                        <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($s->id); ?>" <?php echo e($supplier_id == $s->id ? 'selected' : ''); ?>><?php echo e($s->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">&nbsp;</label>
                    <div class="flex items-center gap-2 mt-1">
                        <input type="checkbox" name="also_closed" value="1" <?php echo e($also_closed == '1' ? 'checked' : ''); ?>

                               class="rounded border-gray-300">
                        <span class="text-sm text-gray-700">Also closed</span>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button type="submit" name="SearchOrders" value="1"
                            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 flex-1">Search</button>
                    <a href="<?php echo e(route('purchases.inquiries.orders')); ?>"
                       class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">Clear</a>
                </div>
            </div>
        </div>
    </form>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <?php if($orders->count() > 0): ?>
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="text-left px-4 py-3">#</th>
                        <th class="text-left px-4 py-3">Reference</th>
                        <th class="text-left px-4 py-3">Supplier</th>
                        <?php if(!$location): ?>
                        <th class="text-left px-4 py-3">Location</th>
                        <?php endif; ?>
                        <th class="text-left px-4 py-3">Supplier's Reference</th>
                        <th class="text-left px-4 py-3">Order Date</th>
                        <th class="text-center px-4 py-3">Currency</th>
                        <th class="text-right px-4 py-3">Order Total</th>
                        <th class="text-center px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <a href="<?php echo e(route('purchases.orders.print', $order->id)); ?>"
                               class="text-blue-600 hover:text-blue-800 font-medium"><?php echo e($order->order_number); ?></a>
                        </td>
                        <td class="px-4 py-3"><?php echo e($order->reference ?? ''); ?></td>
                        <td class="px-4 py-3"><?php echo e($order->supplier->name ?? 'N/A'); ?></td>
                        <?php if(!$location): ?>
                        <td class="px-4 py-3"><?php echo e($order->location ?? ''); ?></td>
                        <?php endif; ?>
                        <td class="px-4 py-3"><?php echo e($order->supp_ref ?? ''); ?></td>
                        <td class="px-4 py-3"><?php echo e($order->order_date ? $order->order_date->format('Y-m-d') : ''); ?></td>
                        <td class="text-center px-4 py-3"><?php echo e($order->curr_code ?? 'USD'); ?></td>
                        <td class="text-right px-4 py-3"><?php echo e(number_format($order->total_amount ?? 0, 2)); ?></td>
                        <td class="text-center px-4 py-3 whitespace-nowrap">
                            <?php if(in_array($order->status, ['pending', 'partial'])): ?>
                                <a href="<?php echo e(route('purchases.orders.receive')); ?>?id=<?php echo e($order->id); ?>"
                                   class="text-green-600 hover:text-green-800 mx-1" title="Receive"><i class="fas fa-truck-loading"></i></a>
                            <?php endif; ?>
                            <a href="<?php echo e(route('purchases.orders.create')); ?>?ModifyOrderNumber=<?php echo e($order->id); ?>"
                               class="text-indigo-600 hover:text-indigo-800 mx-1" title="Edit"><i class="fas fa-edit"></i></a>
                            <a href="<?php echo e(route('purchases.orders.print', $order->id)); ?>"
                               class="text-blue-600 hover:text-blue-800 mx-1" title="Print"><i class="fas fa-print"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            <div class="px-4 py-3 border-t">
                <?php echo e($orders->links()); ?>

            </div>
        <?php else: ?>
            <div class="text-center py-12 text-gray-500">
                <i class="fas fa-search text-6xl mb-4"></i>
                <p class="text-lg">No purchase orders found.</p>
            </div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lupu\Desktop\laravel\fa-saas\resources\views/purchases/inquiries/orders.blade.php ENDPATH**/ ?>