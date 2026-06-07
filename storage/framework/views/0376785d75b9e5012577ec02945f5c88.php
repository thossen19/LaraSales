<?php $__env->startSection('title', 'Supplier Allocations - Sales ERP'); ?>

<?php $__env->startSection('content'); ?>
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900">Supplier Allocations</h2>
        <p class="mt-2 text-gray-600">View and manage supplier payment allocations.</p>
    </div>

    <?php if(session('success')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <form method="GET" action="<?php echo e(route('purchases.allocation.index')); ?>" class="mb-6">
        <div class="bg-white shadow rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Supplier</label>
                    <select name="supplier_id"
                            class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Suppliers</option>
                        <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($s->id); ?>" <?php echo e($supplier_id == $s->id ? 'selected' : ''); ?>><?php echo e($s->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">From</label>
                    <input type="date" name="from_date" value="<?php echo e($from_date); ?>"
                           class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">To</label>
                    <input type="date" name="to_date" value="<?php echo e($to_date); ?>"
                           class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <button type="submit"
                            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 w-full">Search</button>
                </div>
                <div>
                    <a href="<?php echo e(route('purchases.allocation.index')); ?>"
                       class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 block text-center">Clear</a>
                </div>
            </div>
        </div>
    </form>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <?php if($allocations->count() > 0): ?>
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="text-left px-4 py-3">Date</th>
                        <th class="text-left px-4 py-3">Payment #</th>
                        <th class="text-left px-4 py-3">Payment Type</th>
                        <th class="text-left px-4 py-3">Supplier</th>
                        <th class="text-left px-4 py-3">Invoice #</th>
                        <th class="text-right px-4 py-3">Amount</th>
                        <th class="text-center px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $allocations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $alloc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-4 py-3"><?php echo e($alloc->created_at->format('Y-m-d')); ?></td>
                        <td class="px-4 py-3"><?php echo e($alloc->payment->payment_number ?? 'N/A'); ?></td>
                        <td class="px-4 py-3">Payment</td>
                        <td class="px-4 py-3"><?php echo e($alloc->payment->supplier->name ?? $alloc->invoice->supplier->name ?? 'N/A'); ?></td>
                        <td class="px-4 py-3"><?php echo e($alloc->invoice->invoice_number ?? 'N/A'); ?></td>
                        <td class="text-right px-4 py-3"><?php echo e(number_format($alloc->amount, 2)); ?></td>
                        <td class="text-center px-4 py-3 space-x-2">
                            <a href="<?php echo e(route('purchases.allocation.view', $alloc->id)); ?>"
                               class="text-blue-600 hover:text-blue-800">View</a>
                            <form method="POST" action="<?php echo e(route('purchases.allocation.index')); ?>"
                                  onsubmit="return confirm('Delete this allocation?');"
                                  class="inline">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="supplier_id" value="<?php echo e($supplier_id); ?>">
                                <input type="hidden" name="from_date" value="<?php echo e($from_date); ?>">
                                <input type="hidden" name="to_date" value="<?php echo e($to_date); ?>">
                                <button type="submit" name="delete_id" value="<?php echo e($alloc->id); ?>"
                                        class="text-red-600 hover:text-red-800">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            <div class="px-4 py-3 border-t">
                <?php echo e($allocations->links()); ?>

            </div>
        <?php else: ?>
            <div class="text-center py-12 text-gray-500">
                <i class="fas fa-link text-6xl mb-4"></i>
                <p class="text-lg">No allocations found.</p>
            </div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lupu\Desktop\laravel\fa-saas\resources\views/purchases/allocation/index.blade.php ENDPATH**/ ?>