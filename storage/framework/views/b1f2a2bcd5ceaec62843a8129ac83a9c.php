<?php $__env->startSection('title', 'Customers - Sales ERP'); ?>

<?php $__env->startSection('content'); ?>
    <div>
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900">Customers</h2>
            <p class="mt-2 text-gray-600">Manage your customer database.</p>
        </div>

            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700">
                    <h3 class="text-lg font-semibold text-white"><i class="fas fa-users mr-2"></i>Customers List</h3>
                </div>
                <div class="p-6">
                    <form method="GET" action="<?php echo e(route('sales.customers.index')); ?>" class="mb-4">
                        <div class="flex items-center gap-4 flex-wrap">
                            <div class="flex-1 min-w-[200px]">
                                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Search by name, code, contact, phone..." class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <label class="inline-flex items-center text-sm text-gray-700">
                                <input type="checkbox" name="show_inactive" value="1" <?php echo e(request('show_inactive') ? 'checked' : ''); ?> class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 mr-1"> Show inactive
                            </label>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition shadow-sm">
                                <i class="fas fa-search mr-1"></i>Search
                            </button>
                            <a href="<?php echo e(route('sales.customers.create')); ?>" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 transition shadow-sm">
                                <i class="fas fa-plus mr-1"></i>Add Customer
                            </a>
                        </div>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase">Code</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase">Name</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase">Short Name</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase">Phone</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase">City</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase">Credit Limit</th>
                                    <th class="px-4 py-2.5 text-center text-xs font-semibold text-gray-600 uppercase">Status</th>
                                    <th class="px-4 py-2.5 text-center text-xs font-semibold text-gray-600 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php $__empty_1 = true; $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-4 py-2.5 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo e($customer->customer_code); ?></td>
                                        <td class="px-4 py-2.5 whitespace-nowrap text-sm text-gray-900">
                                            <a href="<?php echo e(route('sales.customers.edit', $customer)); ?>" class="text-indigo-600 hover:text-indigo-900"><?php echo e($customer->name); ?></a>
                                        </td>
                                        <td class="px-4 py-2.5 whitespace-nowrap text-sm text-gray-700"><?php echo e($customer->cust_ref ?? '-'); ?></td>
                                        <td class="px-4 py-2.5 whitespace-nowrap text-sm text-gray-700"><?php echo e($customer->phone); ?></td>
                                        <td class="px-4 py-2.5 whitespace-nowrap text-sm text-gray-700"><?php echo e($customer->city); ?></td>
                                        <td class="px-4 py-2.5 whitespace-nowrap text-sm text-gray-900 font-medium"><?php echo e($customer->formatted_credit_limit); ?></td>
                                        <td class="px-4 py-2.5 whitespace-nowrap text-center">
                                            <?php if($customer->status == 'active'): ?>
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                            <?php elseif($customer->status == 'inactive'): ?>
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Inactive</span>
                                            <?php else: ?>
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Hold</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-2.5 whitespace-nowrap text-center">
                                            <div class="flex items-center justify-center gap-1">
                                                <a href="<?php echo e(route('sales.customers.edit', $customer)); ?>" class="p-1.5 text-indigo-600 hover:text-indigo-900 hover:bg-indigo-50 rounded transition" title="Edit"><i class="fas fa-edit text-xs"></i></a>
                                                <form action="<?php echo e(route('sales.customers.destroy', $customer)); ?>" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this customer?')">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="p-1.5 text-red-600 hover:text-red-900 hover:bg-red-50 rounded transition" title="Delete"><i class="fas fa-trash text-xs"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="8" class="px-4 py-12 text-center text-gray-500">
                                            <i class="fas fa-users text-5xl mb-3 text-gray-300"></i>
                                            <p class="text-base font-medium text-gray-400">No customers found</p>
                                            <p class="text-sm mt-1">
                                                <?php if(request('search')): ?>
                                                    Try a different search term.
                                                <?php else: ?>
                                                    <a href="<?php echo e(route('sales.customers.create')); ?>" class="text-indigo-600 hover:text-indigo-900">Add your first customer</a>
                                                <?php endif; ?>
                                            </p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if($customers->hasPages()): ?>
                        <div class="mt-6">
                            <?php echo e($customers->appends(request()->query())->links()); ?>

                        </div>
                    <?php endif; ?>
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
    </style>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lupu\Desktop\laravel\fa-saas\resources\views/sales/customers/index.blade.php ENDPATH**/ ?>