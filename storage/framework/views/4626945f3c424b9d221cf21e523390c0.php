<?php $__env->startSection('title', 'General Ledger Inquiry - Sales ERP'); ?>
<?php $__env->startSection('content'); ?>
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">General Ledger Inquiry</h2>
</div>

<form method="POST" action="<?php echo e(route('banking.inquiries.gl')); ?>">
<?php echo csrf_field(); ?>
<table class="mb-4">
    <tr>
        <td class="py-1 pr-2 text-sm font-medium text-gray-700 whitespace-nowrap">Account:</td>
        <td class="py-1 pr-4">
            <select name="account" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                <option value="">-- All Accounts --</option>
                <?php $__currentLoopData = $gl_accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($a->code); ?>" <?php echo e($account == $a->code ? 'selected' : ''); ?>><?php echo e($a->code); ?> <?php echo e($a->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </td>
        <td class="py-1 pr-2 text-sm font-medium text-gray-700 whitespace-nowrap">from:</td>
        <td class="py-1 pr-4">
            <input type="date" name="TransFromDate" value="<?php echo e($fromDate); ?>" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
        </td>
        <td class="py-1 pr-2 text-sm font-medium text-gray-700 whitespace-nowrap">to:</td>
        <td class="py-1">
            <input type="date" name="TransToDate" value="<?php echo e($toDate); ?>" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
        </td>
    </tr>
</table>
<table class="mb-4">
    <tr>
        <?php if($use_dimension >= 1): ?>
        <td class="py-1 pr-2 text-sm font-medium text-gray-700 whitespace-nowrap">Dimension 1:</td>
        <td class="py-1 pr-4">
            <select name="Dimension" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                <option value=""> </option>
                <?php $__currentLoopData = $dimensions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($d->id); ?>" <?php echo e($dimension == $d->id ? 'selected' : ''); ?>><?php echo e($d->code ? $d->code . ' - ' : ''); ?><?php echo e($d->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </td>
        <?php endif; ?>
        <?php if($use_dimension > 1): ?>
        <td class="py-1 pr-2 text-sm font-medium text-gray-700 whitespace-nowrap">Dimension 2:</td>
        <td class="py-1 pr-4">
            <select name="Dimension2" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                <option value=""> </option>
                <?php $__currentLoopData = $dimensions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($d->id); ?>" <?php echo e($dimension2 == $d->id ? 'selected' : ''); ?>><?php echo e($d->code ? $d->code . ' - ' : ''); ?><?php echo e($d->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </td>
        <?php endif; ?>
        <td class="py-1 pr-2 text-sm font-medium text-gray-700 whitespace-nowrap">Memo:</td>
        <td class="py-1 pr-4">
            <input type="text" name="Memo" value="<?php echo e($memo); ?>" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
        </td>
        <td class="py-1 pr-2 text-sm font-medium text-gray-700 whitespace-nowrap">Amount min:</td>
        <td class="py-1 pr-4">
            <input type="text" name="amount_min" value="<?php echo e($amountMin > 0 ? $amountMin : ''); ?>" class="border border-gray-300 rounded-md px-3 py-2 text-sm w-24 text-right">
        </td>
        <td class="py-1 pr-2 text-sm font-medium text-gray-700 whitespace-nowrap">Amount max:</td>
        <td class="py-1 pr-4">
            <input type="text" name="amount_max" value="<?php echo e($amountMax > 0 ? $amountMax : ''); ?>" class="border border-gray-300 rounded-md px-3 py-2 text-sm w-24 text-right">
        </td>
        <td class="py-1">
            <button type="submit" name="Show" value="1" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">Show</button>
        </td>
    </tr>
</table>
</form>

<?php if(request('Show') || request('account')): ?>
<hr class="mb-4">

<?php if($account): ?>
    <div class="mb-2 text-sm font-semibold text-gray-800"><?php echo e($account); ?> &nbsp;&nbsp;&nbsp; <?php echo e(optional(\DB::table('accounts')->where('code', $account)->first())->name); ?></div>
<?php endif; ?>

<?php
    $dim = (int) $use_dimension;
    $colspan = ($dim == 2 ? '7' : ($dim == 1 ? '6' : '5'));
    $hasAccount = $account !== '';
?>

<div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">#</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Account</th>
                    <?php if($dim >= 1): ?><th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Dimension 1</th><?php endif; ?>
                    <?php if($dim > 1): ?><th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Dimension 2</th><?php endif; ?>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Person/Item</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Debit</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Credit</th>
                    <?php if($showBalances): ?><th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Balance</th><?php endif; ?>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Memo</th>
                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php if($showBalances): ?>
                <tr class="bg-yellow-50">
                    <td class="px-3 py-2 text-sm font-bold text-gray-700" colspan="<?php echo e($colspan); ?>">Opening Balance - <?php echo e($fromDate); ?></td>
                    <?php
                        $ob = $openingBalance;
                    ?>
                    <?php if($ob >= 0): ?>
                        <td class="px-3 py-2 text-sm text-right text-gray-700"><?php echo e(number_format($ob, 2)); ?></td>
                        <td class="px-3 py-2 text-sm text-right text-gray-700"></td>
                    <?php else: ?>
                        <td class="px-3 py-2 text-sm text-right text-gray-700"></td>
                        <td class="px-3 py-2 text-sm text-right text-gray-700"><?php echo e(number_format(abs($ob), 2)); ?></td>
                    <?php endif; ?>
                    <td class="px-3 py-2 text-sm text-gray-500"></td>
                    <td class="px-3 py-2 text-sm text-gray-500"></td>
                    <td class="px-3 py-2"></td>
                </tr>
                <?php endif; ?>

                <?php $__empty_1 = true; $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php if($loop->iteration % 12 == 1 && !$loop->first): ?>
            </tbody>
        </table>
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">#</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Account</th>
                    <?php if($dim >= 1): ?><th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Dimension 1</th><?php endif; ?>
                    <?php if($dim > 1): ?><th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Dimension 2</th><?php endif; ?>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Person/Item</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Debit</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Credit</th>
                    <?php if($showBalances): ?><th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Balance</th><?php endif; ?>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Memo</th>
                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                    <?php endif; ?>

                    <?php
                        $amount = $t->debit_amount - $t->credit_amount;
                        $typeLabels = ['journal' => 'Journal Entry', 'deposit' => 'Bank Deposit', 'payment' => 'Bank Payment', 'transfer' => 'Bank Transfer', 'accrual' => 'Accrual'];
                        $typeLabel = $typeLabels[$t->reference_type] ?? ucfirst($t->reference_type);
                        $memoText = $t->line_description ?: $t->je_description;
                    ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-2 text-sm text-gray-700"><?php echo e($typeLabel); ?></td>
                        <td class="px-3 py-2 text-sm text-center"><a href="#" class="text-indigo-600 hover:text-indigo-900"><?php echo e($t->je_id); ?></a></td>
                        <td class="px-3 py-2 text-sm"><a href="#" class="text-indigo-600 hover:text-indigo-900"><?php echo e($t->entry_number); ?></a></td>
                        <td class="px-3 py-2 text-sm text-gray-900"><?php echo e($t->entry_date); ?></td>
                        <?php if(!$hasAccount): ?>
                            <td class="px-3 py-2 text-sm text-gray-600"><?php echo e($t->account_code); ?> <?php echo e($t->account_name); ?></td>
                        <?php endif; ?>
                        <?php if($dim >= 1): ?><td class="px-3 py-2 text-sm text-gray-500">-</td><?php endif; ?>
                        <?php if($dim > 1): ?><td class="px-3 py-2 text-sm text-gray-500">-</td><?php endif; ?>
                        <td class="px-3 py-2 text-sm text-gray-500"><?php echo e($t->user_name ?? ''); ?></td>
                        <?php if($amount >= 0): ?>
                            <td class="px-3 py-2 text-sm text-right text-gray-700"><?php echo e(number_format($amount, 2)); ?></td>
                            <td class="px-3 py-2 text-sm text-right text-gray-700"></td>
                        <?php else: ?>
                            <td class="px-3 py-2 text-sm text-right text-gray-700"></td>
                            <td class="px-3 py-2 text-sm text-right text-gray-700"><?php echo e(number_format(abs($amount), 2)); ?></td>
                        <?php endif; ?>
                        <?php if($showBalances): ?>
                            <td class="px-3 py-2 text-sm text-right text-gray-700"><?php echo e(number_format($t->running_balance, 2)); ?></td>
                        <?php endif; ?>
                        <td class="px-3 py-2 text-sm text-gray-600 max-w-xs truncate"><?php echo e($memoText); ?></td>
                        <td class="px-3 py-2 text-center text-sm">
                            <?php if($t->reference_type == 'journal'): ?>
                                <a href="#" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="<?php echo e(6 + ($dim >= 1 ? 1 : 0) + ($dim > 1 ? 1 : 0) + ($hasAccount ? 0 : 1) + ($showBalances ? 1 : 0)); ?>" class="px-3 py-8 text-center text-gray-500">No general ledger transactions have been created for the specified criteria.</td>
                    </tr>
                <?php endif; ?>

                <?php if($showBalances && $transactions->count() > 0): ?>
                <tr class="bg-yellow-50">
                    <td class="px-3 py-2 text-sm font-bold text-gray-700" colspan="<?php echo e($colspan); ?>">Ending Balance - <?php echo e($toDate); ?></td>
                    <?php if($runningBalance >= 0): ?>
                        <td class="px-3 py-2 text-sm text-right text-gray-700"><?php echo e(number_format($runningBalance, 2)); ?></td>
                        <td class="px-3 py-2 text-sm text-right text-gray-700"></td>
                    <?php else: ?>
                        <td class="px-3 py-2 text-sm text-right text-gray-700"></td>
                        <td class="px-3 py-2 text-sm text-right text-gray-700"><?php echo e(number_format(abs($runningBalance), 2)); ?></td>
                    <?php endif; ?>
                    <td class="px-3 py-2 text-sm text-gray-500"></td>
                    <td class="px-3 py-2 text-sm text-gray-500"></td>
                    <td class="px-3 py-2"></td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lupu\Desktop\laravel\fa-saas\resources\views/banking/inquiries/gl.blade.php ENDPATH**/ ?>