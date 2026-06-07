<?php $__env->startSection('title', 'Search All Sales Orders - Sales ERP'); ?>

<?php $__env->startSection('content'); ?>
    <div>
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900">Search All Sales Orders</h2>
            <p class="mt-2 text-gray-600">Search and view sales orders with advanced filtering options.</p>
        </div>

        <form method="GET" action="<?php echo e(route('sales.inquiries.orders')); ?>">
            <div class="bg-white shadow rounded-lg overflow-hidden mb-6">
                <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700">
                    <h3 class="text-lg font-semibold text-white"><i class="fas fa-filter mr-2"></i>Search Filters</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">#:</label>
                            <input type="text" name="OrderNumber" value="<?php echo e(request('OrderNumber')); ?>" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Ref:</label>
                            <input type="text" name="OrderReference" value="<?php echo e(request('OrderReference')); ?>" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Order date / Delivery date:</label>
                            <div class="flex items-center gap-2 mt-1">
                                <label class="inline-flex items-center text-xs text-gray-700">
                                    <input type="radio" name="by_delivery" value="0" <?php echo e(!request('by_delivery') ? 'checked' : ''); ?> class="mr-1"> Order date
                                </label>
                                <label class="inline-flex items-center text-xs text-gray-700">
                                    <input type="radio" name="by_delivery" value="1" <?php echo e(request('by_delivery') == '1' ? 'checked' : ''); ?> class="mr-1"> Delivery date
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">From:</label>
                            <input type="date" name="OrdersAfterDate" value="<?php echo e(request('OrdersAfterDate')); ?>" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">To:</label>
                            <input type="date" name="OrdersToDate" value="<?php echo e(request('OrdersToDate')); ?>" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Branch/Location:</label>
                            <select name="StockLocation" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="all">All Locations</option>
                                <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($loc->id); ?>" <?php echo e(request('StockLocation') == $loc->id ? 'selected' : ''); ?>><?php echo e($loc->branch_name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Item:</label>
                            <select name="SelectStockFromList" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="all">All Items</option>
                                <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $it): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($it->code); ?>" <?php echo e(request('SelectStockFromList') == $it->code ? 'selected' : ''); ?>><?php echo e($it->code); ?> - <?php echo e($it->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Customer:</label>
                            <select name="customer_id" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="all">All Customers</option>
                                <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($c->id); ?>" <?php echo e(request('customer_id') == $c->id ? 'selected' : ''); ?>><?php echo e($c->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="flex items-end gap-3">
                            <label class="inline-flex items-center text-xs text-gray-700">
                                <input type="checkbox" name="show_voided" value="1" <?php echo e(request('show_voided') ? 'checked' : ''); ?> class="mr-1"> Zero values
                            </label>
                            <label class="inline-flex items-center text-xs text-gray-700">
                                <input type="checkbox" name="no_auto" value="1" <?php echo e(request('no_auto') ? 'checked' : ''); ?> class="mr-1"> No auto
                            </label>
                            <button type="submit" class="px-5 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition shadow-sm">
                                <i class="fas fa-search mr-1"></i>Search
                            </button>
                            <a href="<?php echo e(route('sales.inquiries.orders')); ?>" class="px-4 py-2 bg-white text-gray-700 text-sm font-medium rounded-md hover:bg-gray-100 transition border border-gray-300">
                                <i class="fas fa-undo mr-1"></i>Reset
                            </a>
                        </div>
                    </div>
                    <input type="hidden" name="order_view_mode" value="default">
                    <input type="hidden" name="type" value="30">
                </div>
            </div>
            </form>

            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700">
                    <h3 class="text-lg font-semibold text-white"><i class="fas fa-list mr-2"></i>Sales Orders</h3>
                </div>
                <div class="p-6">
                    <div class="mb-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <span class="text-lg font-medium text-gray-900">Orders Found: <span class="text-indigo-600 font-bold"><?php echo e($orders->total()); ?></span></span>
                            </div>
                            <div class="text-right text-sm text-gray-600">
                                <?php if($orders->total() > 0): ?>
                                    Showing <?php echo e($orders->firstItem()); ?>-<?php echo e($orders->lastItem()); ?> of <?php echo e($orders->total()); ?> results
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Order #</th>
                                    <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Ref</th>
                                    <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Customer</th>
                                    <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Branch</th>
                                    <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Cust Ref</th>
                                    <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Order Date</th>
                                    <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Required By</th>
                                    <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Delivery To</th>
                                    <th class="px-3 py-2.5 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Order Total</th>
                                    <th class="px-3 py-2.5 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Curr</th>
                                    <th class="px-3 py-2.5 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $o): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <?php $isOverdue = $o->delivery_date && $o->delivery_date->isPast() && $o->status !== 'delivered' && $o->status !== 'cancelled'; ?>
                                    <tr class="<?php echo e($isOverdue ? 'bg-red-50' : 'hover:bg-gray-50'); ?> transition">
                                        <td class="px-3 py-2.5 whitespace-nowrap text-sm font-medium">
                                            <a href="<?php echo e(route('sales.orders.show', $o)); ?>" class="text-indigo-600 hover:text-indigo-900"><?php echo e($o->order_number ?? '#' . $o->id); ?></a>
                                        </td>
                                        <td class="px-3 py-2.5 whitespace-nowrap text-sm text-gray-700"><?php echo e($o->order_number ?? '-'); ?></td>
                                        <td class="px-3 py-2.5 whitespace-nowrap text-sm text-gray-900"><?php echo e($o->customer->name ?? 'N/A'); ?></td>
                                        <td class="px-3 py-2.5 whitespace-nowrap text-sm text-gray-700"><?php echo e($o->customerBranch->branch_name ?? '-'); ?></td>
                                        <td class="px-3 py-2.5 whitespace-nowrap text-sm text-gray-700"><?php echo e($o->internal_notes ?? '-'); ?></td>
                                        <td class="px-3 py-2.5 whitespace-nowrap text-sm text-gray-700"><?php echo e($o->order_date ? $o->order_date->format('d/m/Y') : '-'); ?></td>
                                        <td class="px-3 py-2.5 whitespace-nowrap text-sm text-gray-700"><?php echo e($o->delivery_date ? $o->delivery_date->format('d/m/Y') : '-'); ?></td>
                                        <td class="px-3 py-2.5 whitespace-nowrap text-sm text-gray-700 max-w-[120px] truncate" title="<?php echo e($o->customerBranch->branch_name ?? ''); ?>"><?php echo e($o->customerBranch->branch_name ?? $o->delivery_address ?? '-'); ?></td>
                                        <td class="px-3 py-2.5 whitespace-nowrap text-sm text-right font-medium text-gray-900"><?php echo e(number_format($o->total_amount, 2)); ?></td>
                                        <td class="px-3 py-2.5 whitespace-nowrap text-sm text-center text-gray-700">USD</td>
                                        <td class="px-3 py-2.5 whitespace-nowrap text-center">
                                            <div class="flex items-center justify-center gap-0.5">
                                                <a href="<?php echo e(route('sales.orders.edit', $o)); ?>" class="p-1.5 text-indigo-600 hover:text-indigo-900 hover:bg-indigo-50 rounded transition" title="Edit"><i class="fas fa-edit text-xs"></i></a>
                                                <a href="<?php echo e(route('sales.delivery.from-order', ['order_id' => $o->id])); ?>" class="p-1.5 text-blue-600 hover:text-blue-900 hover:bg-blue-50 rounded transition" title="Dispatch"><i class="fas fa-truck text-xs"></i></a>
                                                <a href="<?php echo e(route('sales.orders.show', $o)); ?>" class="p-1.5 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded transition" title="Print" onclick="window.open(this.href+'?print=1', '_blank'); return false;"><i class="fas fa-print text-xs"></i></a>
                                                <form action="<?php echo e(route('sales.orders.destroy', $o)); ?>" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this order?')">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="p-1.5 text-red-600 hover:text-red-900 hover:bg-red-50 rounded transition" title="Delete"><i class="fas fa-trash text-xs"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="11" class="px-3 py-12 text-center text-gray-500">
                                            <i class="fas fa-file-invoice text-5xl mb-3 text-gray-300"></i>
                                            <p class="text-base font-medium text-gray-400">No orders found</p>
                                            <p class="text-sm mt-1">Try adjusting your search filters.</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if($orders->hasPages()): ?>
                        <div class="mt-6">
                            <?php echo e($orders->appends(request()->query())->links()); ?>

                        </div>
                    <?php endif; ?>

                    <div class="mt-4 text-center">
                        <a href="<?php echo e(route('sales.inquiries.orders', request()->except('order_view_mode', 'type'))); ?>" class="text-sm text-gray-500 hover:text-gray-700">
                            <i class="fas fa-sync-alt mr-1"></i>Update
                        </a>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-6">
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                            <i class="fas fa-file-invoice text-white"></i>
                        </div>
                        <div class="ml-5">
                            <p class="text-sm font-medium text-gray-500 truncate">Total Orders</p>
                            <p class="text-lg font-medium text-gray-900"><?php echo e(number_format($totalOrders)); ?></p>
                        </div>
                    </div>
                </div>
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                            <i class="fas fa-check-circle text-white"></i>
                        </div>
                        <div class="ml-5">
                            <p class="text-sm font-medium text-gray-500 truncate">Completed</p>
                            <p class="text-lg font-medium text-gray-900"><?php echo e(number_format($completedCount)); ?></p>
                        </div>
                    </div>
                </div>
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                            <i class="fas fa-clock text-white"></i>
                        </div>
                        <div class="ml-5">
                            <p class="text-sm font-medium text-gray-500 truncate">In Progress</p>
                            <p class="text-lg font-medium text-gray-900"><?php echo e(number_format($inProgressCount)); ?></p>
                        </div>
                    </div>
                </div>
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                            <i class="fas fa-dollar-sign text-white"></i>
                        </div>
                        <div class="ml-5">
                            <p class="text-sm font-medium text-gray-500 truncate">Total Value</p>
                            <p class="text-lg font-medium text-gray-900">$<?php echo e(number_format($totalValue, 2)); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('styles'); ?>
    <style>
        .pagination { display: flex; justify-content: center; gap: 4px; flex-wrap: wrap; }
        .pagination .page-item { list-style: none; }
        .pagination .page-link { display: block; padding: 6px 12px; border: 1px solid #d1d5db; border-radius: 6px; color: #374151; font-size: 14px; text-decoration: none; transition: all 0.15s; }
        .pagination .page-link:hover { background-color: #f3f4f6; }
        .pagination .active .page-link { background-color: #4f46e5; border-color: #4f46e5; color: white; }
        .pagination .disabled .page-link { color: #9ca3af; pointer-events: none; background-color: #f9fafb; }
        .pagination svg { width: 16px; height: 16px; }
        tr.bg-red-50 td { color: #dc2626; }
        tr.bg-red-50 a { color: #dc2626; }
        tr.bg-red-50 a:hover { color: #b91c1c; }
    </style>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lupu\Desktop\laravel\fa-saas\resources\views/sales/inquiries/orders.blade.php ENDPATH**/ ?>