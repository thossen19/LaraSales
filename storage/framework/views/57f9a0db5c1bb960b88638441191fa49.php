<?php $__env->startSection('title', 'Journal Inquiry - Sales ERP'); ?>
<?php $__env->startSection('content'); ?>
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Journal Inquiry</h2>
</div>

<form method="POST" action="<?php echo e(route('banking.inquiries.journal')); ?>">
<?php echo csrf_field(); ?>

<table class="mb-4">
    <tr>
        <td class="py-1 pr-2 text-sm font-medium text-gray-700 whitespace-nowrap">Reference:</td>
        <td class="py-1 pr-4">
            <input type="text" name="Ref" value="<?php echo e($ref); ?>" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
        </td>
        <td class="py-1 pr-2 text-sm font-medium text-gray-700 whitespace-nowrap">Type:</td>
        <td class="py-1 pr-4">
            <select name="filterType" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                <?php $__currentLoopData = $journal_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($val); ?>" <?php echo e($filterType == $val ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </td>
        <td class="py-1 pr-2 text-sm font-medium text-gray-700 whitespace-nowrap">From:</td>
        <td class="py-1 pr-4">
            <input type="date" name="FromDate" value="<?php echo e($fromDate); ?>" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
        </td>
        <td class="py-1 pr-2 text-sm font-medium text-gray-700 whitespace-nowrap">To:</td>
        <td class="py-1">
            <input type="date" name="ToDate" value="<?php echo e($toDate); ?>" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
        </td>
    </tr>
    <tr>
        <td class="py-1 pr-2 text-sm font-medium text-gray-700 whitespace-nowrap">Memo:</td>
        <td class="py-1 pr-4">
            <input type="text" name="Memo" value="<?php echo e($memo); ?>" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
        </td>
        <td class="py-1 pr-2 text-sm font-medium text-gray-700 whitespace-nowrap">User:</td>
        <td class="py-1 pr-4">
            <select name="userid" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                <option value="">-- All --</option>
                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($u->id); ?>" <?php echo e($userId == $u->id ? 'selected' : ''); ?>><?php echo e($u->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </td>
        <?php if($use_dimension): ?>
        <td class="py-1 pr-2 text-sm font-medium text-gray-700 whitespace-nowrap">Dimension:</td>
        <td class="py-1 pr-4">
            <select name="dimension" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                <option value="">-- All --</option>
                <?php $__currentLoopData = $dimensions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($d->id); ?>" <?php echo e($dimension == $d->id ? 'selected' : ''); ?>><?php echo e($d->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </td>
        <?php endif; ?>
        <td class="py-1 pr-2 text-sm font-medium text-gray-700 whitespace-nowrap">
            <label class="flex items-center cursor-pointer">
                <input type="checkbox" name="AlsoClosed" value="1" <?php echo e($alsoClosed ? 'checked' : ''); ?> class="rounded border-gray-300 text-indigo-600">
                <span class="ml-1">Show closed:</span>
            </label>
        </td>
        <td class="py-1">
            <button type="submit" name="Search" value="1" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">Search</button>
        </td>
    </tr>
</table>
</form>

<?php if(request('Search') || $entries->count() > 0): ?>
<div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">#</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Trans #</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Counterparty</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Memo</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">View</th>
                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Edit</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php $__empty_1 = true; $__currentLoopData = $entries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $typeLabels = ['journal' => 'Journal Entry', 'deposit' => 'Bank Deposit', 'payment' => 'Bank Payment', 'transfer' => 'Bank Transfer', 'accrual' => 'Accrual'];
                        $typeLabel = $typeLabels[$e->reference_type] ?? ucfirst($e->reference_type);
                        $amount = $e->total_debit;
                        $viewUrl = route('banking.inquiries.journal'); // placeholder view link
                        $editUrl = '#'; // placeholder edit link
                        $isEditable = $e->is_posted ? false : true;
                    ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-2 text-sm text-center text-gray-500"><?php echo e($loop->iteration); ?></td>
                        <td class="px-3 py-2 text-sm text-gray-900"><?php echo e($e->entry_date); ?></td>
                        <td class="px-3 py-2 text-sm text-gray-700"><?php echo e($typeLabel); ?></td>
                        <td class="px-3 py-2 text-sm"><a href="#" class="text-indigo-600 hover:text-indigo-900"><?php echo e($e->id); ?></a></td>
                        <td class="px-3 py-2 text-sm text-gray-600"><?php echo e($e->reference_type); ?></td>
                        <td class="px-3 py-2 text-sm text-gray-700"><?php echo e($e->entry_number); ?></td>
                        <td class="px-3 py-2 text-sm text-right text-gray-700"><?php echo e(number_format($amount, 2)); ?></td>
                        <td class="px-3 py-2 text-sm text-gray-600 max-w-xs truncate"><?php echo e($e->description); ?></td>
                        <td class="px-3 py-2 text-sm text-gray-600"><?php echo e($e->user_name ?? 'N/A'); ?></td>
                        <td class="px-3 py-2 text-center">
                            <a href="#" class="text-indigo-600 hover:text-indigo-900 text-sm" title="View GL">GL</a>
                        </td>
                        <td class="px-3 py-2 text-center">
                            <?php if($isEditable): ?>
                                <a href="<?php echo e($editUrl); ?>" class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</a>
                            <?php else: ?>
                                <span class="text-gray-400 text-sm">--</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="11" class="px-3 py-8 text-center text-gray-500">No journal entries found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if($entries->hasPages()): ?>
        <div class="px-4 py-3 border-t border-gray-200">
            <?php echo e($entries->links()); ?>

        </div>
    <?php endif; ?>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lupu\Desktop\laravel\fa-saas\resources\views/banking/inquiries/journal.blade.php ENDPATH**/ ?>