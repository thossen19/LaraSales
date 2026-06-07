<?php $__env->startPush('scripts'); ?>
<script>
function allocate_all(id) {
    var unalloc = parseFloat(document.getElementById('unalloc_' + id).value) || 0;
    var field = document.querySelector('input[name="amount' + id + '"]');
    if (field) field.value = unalloc.toFixed(2);
    calcTotals();
}
function allocate_none(id) {
    var field = document.querySelector('input[name="amount' + id + '"]');
    if (field) field.value = '0.00';
    calcTotals();
}
function calcTotals() {
    var total = 0;
    var inputs = document.querySelectorAll('input[name^="amount"]');
    inputs.forEach(function(inp) {
        var v = parseFloat(inp.value) || 0;
        total += v;
    });
    var el = document.getElementById('total_allocated');
    if (el) el.innerText = '$' + total.toFixed(2);
    var left = <?php echo e(abs($cart['amount'])); ?> - total;
    var el2 = document.getElementById('left_to_allocate');
    if (el2) {
        if (left < -0.001) {
            el2.innerHTML = '<span class="text-red-600 font-bold">$' + left.toFixed(2) + '</span>';
        } else {
            el2.innerText = '$' + left.toFixed(2);
        }
    }
}
document.addEventListener('DOMContentLoaded', function() {
    calcTotals();
    document.querySelectorAll('input[name^="amount"]').forEach(function(inp) {
        inp.addEventListener('input', calcTotals);
        inp.addEventListener('change', calcTotals);
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('title', 'Allocate Customer Payment or Credit Note'); ?>
<?php $__env->startSection('content'); ?>
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Allocate Customer Payment or Credit Note</h2>
</div>

<?php if(session('success')): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 text-center"><?php echo e(session('success')); ?></div>
<?php endif; ?>
<?php if(session('error')): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?php echo e(session('error')); ?></div>
<?php endif; ?>

<form method="POST" action="<?php echo e(route('sales.allocations.customer-allocate')); ?>">
<?php echo csrf_field(); ?>

<div class="bg-white shadow rounded-lg overflow-hidden mb-6">
    <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700">
        <h3 class="text-lg font-semibold text-white">
            <i class="fas fa-link mr-2"></i>
            Allocation of <?php echo e($systypes[$cart['trans_type']] ?? 'Transaction'); ?> #<?php echo e($cart['trans_no']); ?>

        </h3>
    </div>
    <div class="p-6">
        <div class="text-lg font-medium text-gray-800 mb-2"><?php echo e($cart['customer_name']); ?></div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm mb-4">
            <div>
                <span class="text-gray-500">Date:</span>
                <span class="font-medium text-gray-800"><?php echo e($cart['date'] ? \Carbon\Carbon::parse($cart['date'])->format('d/m/Y') : '-'); ?></span>
            </div>
            <div>
                <span class="text-gray-500">Total:</span>
                <span class="font-medium text-gray-800">$<?php echo e(number_format($cart['bank_amount'] ?? $cart['amount'], 2)); ?> <?php echo e($cart['currency']); ?></span>
            </div>
            <?php if(abs($cart['bank_amount'] ?? $cart['amount']) != abs($cart['amount'])): ?>
            <div>
                <span class="text-gray-500">Amount to be settled:</span>
                <span class="font-medium text-gray-800">$<?php echo e(number_format(abs($cart['amount']), 2)); ?> <?php echo e($cart['currency']); ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div id="alloc_tbl">
<div class="bg-white shadow rounded-lg overflow-hidden mb-6">
    <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700">
        <h3 class="text-lg font-semibold text-white">
            <i class="fas fa-list mr-2"></i>
            Allocated amounts in <?php echo e($cart['currency']); ?>:
        </h3>
    </div>
    <?php if(count($cart['allocs']) > 0): ?>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Transaction Type</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">#</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Ref</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Due Date</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Amount</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Other Allocations</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Left to Allocate</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">This Allocation</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase" colspan="2">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php $k = 0; ?>
                <?php $__currentLoopData = $cart['allocs']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $alloc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if(abs($alloc['amount']) - $alloc['amount_allocated'] > 0): ?>
                        <?php
                            $rowClass = $k % 2 == 0 ? 'bg-white' : 'bg-gray-50';
                            $k++;
                            $unAllocated = round(abs($alloc['amount']) - $alloc['amount_allocated'], 6);
                        ?>
                        <tr class="<?php echo e($rowClass); ?> hover:bg-gray-50 transition">
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700"><?php echo e($systypes[$alloc['type']] ?? 'Invoice'); ?></td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-right">
                                <a href="<?php echo e(route('sales.orders.show', $alloc['type_no'])); ?>" class="text-blue-600 hover:text-blue-800 font-medium" target="_blank"><?php echo e($alloc['type_no']); ?></a>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900"><?php echo e($alloc['ref']); ?></td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700"><?php echo e($alloc['date'] ? \Carbon\Carbon::parse($alloc['date'])->format('d/m/Y') : '-'); ?></td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700"><?php echo e($alloc['due_date'] ? \Carbon\Carbon::parse($alloc['due_date'])->format('d/m/Y') : '-'); ?></td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-right font-medium text-gray-900">$<?php echo e(number_format(abs($alloc['amount']), 2)); ?></td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-700">$<?php echo e(number_format($alloc['amount_allocated'], 2)); ?></td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-700" id="unalloc_td_<?php echo e($id); ?>">$<?php echo e(number_format($unAllocated, 2)); ?></td>
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                <input type="text" name="amount<?php echo e($id); ?>" value="<?php echo e(number_format($alloc['current_allocated'], 2, '.', '')); ?>"
                                    class="border border-gray-300 rounded px-2 py-0.5 text-right w-24 text-sm">
                                <input type="hidden" name="un_allocated<?php echo e($id); ?>" id="unalloc_<?php echo e($id); ?>" value="<?php echo e($unAllocated); ?>">
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                <a href="javascript:void(0)" onclick="allocate_all(<?php echo e($id); ?>)" class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">All</a>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                <a href="javascript:void(0)" onclick="allocate_none(<?php echo e($id); ?>)" class="text-gray-500 hover:text-gray-700 text-xs font-medium">None</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-200">
        <div class="w-80 ml-auto space-y-2">
            <div class="flex justify-between text-sm">
                <span class="text-gray-500 font-medium">Total Allocated:</span>
                <span class="font-bold text-gray-900" id="total_allocated">$<?php echo e(number_format($totalAllocated, 2)); ?></span>
            </div>
            <?php $leftToAllocate = abs($cart['amount']) - $totalAllocated; ?>
            <div class="flex justify-between text-sm">
                <span class="text-gray-500 font-medium">Left to Allocate:</span>
                <span class="font-bold <?php echo e($leftToAllocate < -0.001 ? 'text-red-600' : 'text-gray-900'); ?>" id="left_to_allocate">$<?php echo e(number_format($leftToAllocate, 2)); ?></span>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="p-6 text-center text-gray-500">
        <i class="fas fa-inbox text-4xl mb-3 text-gray-300 block"></i>
        There are no unsettled transactions to allocate.
    </div>
    <?php endif; ?>
</div>
</div>

<input type="hidden" name="TotalNumberOfAllocs" value="<?php echo e(count($cart['allocs'])); ?>">

<div class="flex justify-center gap-4 mt-6">
    <?php if(count($cart['allocs']) > 0): ?>
        <button type="submit" name="UpdateDisplay" value="1" class="px-6 py-2.5 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 transition shadow-sm"><i class="fas fa-sync mr-2"></i>Refresh</button>
        <button type="submit" name="Process" value="1" class="px-8 py-2.5 bg-gradient-to-r from-green-600 to-green-700 text-white font-medium rounded-md hover:from-green-700 hover:to-green-800 transition shadow-sm"><i class="fas fa-check mr-2"></i>Process</button>
    <?php endif; ?>
    <button type="submit" name="Cancel" value="1" class="px-6 py-2.5 bg-white text-gray-700 font-medium rounded-md hover:bg-gray-100 transition border border-gray-300 shadow-sm"><i class="fas fa-arrow-left mr-2"></i>Back to Allocations</button>
</div>

</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lupu\Desktop\laravel\fa-saas\resources\views/sales/allocations/customer-allocate.blade.php ENDPATH**/ ?>