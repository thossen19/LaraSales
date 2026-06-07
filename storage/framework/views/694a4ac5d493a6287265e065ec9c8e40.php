<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<?php $__env->stopPush(); ?>
<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
flatpickr('#date_', { dateFormat: 'd/m/Y' });
flatpickr('#from_date', { dateFormat: 'd/m/Y' });
flatpickr('#to_date', { dateFormat: 'd/m/Y' });
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('title', 'Employee Payslip Entry'); ?>
<?php $__env->startSection('content'); ?>
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Employee Payslip Entry</h2>
</div>

<?php if($msg): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4"><?php echo $msg; ?></div>
    <?php if($added_trans_no): ?>
        <div class="text-center mb-4">
            <a href="<?php echo e(route('hr.payslips', ['NewPayslip' => 'Yes'])); ?>" class="text-blue-600 hover:text-blue-800 underline mx-2">Enter &amp;New Payslip</a>
            <a href="<?php echo e(route('hr.payment-advice')); ?>?PayslipNo=<?php echo e($added_payslip_no); ?>" class="text-blue-600 hover:text-blue-800 underline mx-2">Make Payment &amp;Advice for this Payslip</a>
        </div>
    <?php endif; ?>
<?php endif; ?>
<?php if($error): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?php echo $error; ?></div>
<?php endif; ?>

<form method="post" action="<?php echo e(route('hr.payslips')); ?>">
    <?php echo csrf_field(); ?>

    <table class="table-auto border-collapse bg-white shadow rounded-lg w-full mb-4">
        <tr>
            <td class="border px-3 py-2">
                <table class="w-full">
                    <tr>
                        <td class="p-1 text-right font-semibold">Date:</td>
                        <td class="p-1"><input type="text" id="date_" name="date_" value="<?php echo e(old('date_', !empty($cart['tran_date']) ? date('d/m/Y', strtotime($cart['tran_date'])) : date('d/m/Y'))); ?>" size="12" class="border px-2 py-1"></td>
                        <td class="p-1 text-right font-semibold">Reference:</td>
                        <td class="p-1"><input type="text" name="ref" value="<?php echo e(old('ref', $cart['reference'] ?? '')); ?>" size="12" class="border px-2 py-1"></td>
                        <td class="p-1 text-right font-semibold">From:</td>
                        <td class="p-1"><input type="text" id="from_date" name="from_date" value="<?php echo e(old('from_date', isset($cart['from_date']) && $cart['from_date'] ? date('d/m/Y', strtotime($cart['from_date'])) : '')); ?>" size="12" class="border px-2 py-1"></td>
                        <td class="p-1 text-right font-semibold">To:</td>
                        <td class="p-1"><input type="text" id="to_date" name="to_date" value="<?php echo e(old('to_date', isset($cart['to_date']) && $cart['to_date'] ? date('d/m/Y', strtotime($cart['to_date'])) : '')); ?>" size="12" class="border px-2 py-1"></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td class="border px-3 py-2">
                <table class="w-full">
                    <tr>
                        <td class="p-1 text-right font-semibold w-1/6">Pay To:</td>
                        <td class="p-1">
                            <select name="person_id" class="border px-2 py-1 min-w-[200px]">
                                <option value="">Select employee</option>
                                <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($emp->id); ?>" <?php if(old('person_id', $cart['person_id'] ?? '') == $emp->id): echo 'selected'; endif; ?>><?php echo e($emp->first_name); ?> <?php echo e($emp->last_name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </td>
                        <td class="p-1 text-right font-semibold">Pay Basis:</td>
                        <td class="p-1"><?php echo e($pay_basis_label ?: ''); ?></td>
                        <td class="p-1 text-right font-semibold">Payslip No:</td>
                        <td class="p-1"><?php echo e($next_payslip_no); ?></td>
                        <input type="hidden" name="PaySlipNo" value="<?php echo e($next_payslip_no); ?>">
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td class="border px-3 py-2">
                <table class="w-full">
                    <tr>
                        <td class="p-1 text-right font-semibold">Work days:</td>
                        <td class="p-1"><?php echo e($info['work_days'] ?? ''); ?> days</td>
                        <td class="p-1 text-right font-semibold">Leave hours:</td>
                        <td class="p-1"><?php echo e(isset($info['leave_hours']) ? number_format($info['leave_hours'], 2) : ''); ?> hours</td>
                        <?php if(isset($info['ot_totals'])): ?>
                            <?php $__currentLoopData = $overtimes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <td class="p-1 text-right font-semibold"><?php echo e($ot->overtime_name); ?>:</td>
                                <td class="p-1"><?php echo e(number_format($info['ot_totals'][$ot->overtime_id] ?? 0, 2)); ?> hours</td>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                            <?php $__currentLoopData = $overtimes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <td class="p-1 text-right font-semibold"><?php echo e($ot->overtime_name); ?>:</td>
                                <td class="p-1"></td>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </tr>
                </table>
                <input type="hidden" name="leaves" value="<?php echo e($info['leave_hours'] ?? 0); ?>">
                <input type="hidden" name="deductableleaves" value="<?php echo e($cart['deductable_leaves'] ?? 0); ?>">
                <input type="hidden" name="workdays" value="<?php echo e($info['work_days'] ?? 0); ?>">
            </td>
        </tr>
        <tr>
            <td class="border px-3 py-2">
                <table class="w-full">
                    <tr>
                        <?php $cols = 3; ?>
                        <?php $__empty_1 = true; $__currentLoopData = $leave_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <td class="p-1 text-right font-semibold"><?php echo e($lt->leave_name); ?>:</td>
                            <td class="p-1"><?php echo e(isset($info['leave_counts'][$lt->leave_id]) ? $info['leave_counts'][$lt->leave_id] . ' day(s)' : ''); ?></td>
                            <?php if ($loop->iteration % $cols == 0) echo '</tr><tr>'; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <td class="p-1"></td>
                        <?php endif; ?>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <?php if(count($cart['gl_items']) == 0): ?>
        <div class="text-center mb-4">
            <input type="submit" name="GeneratePayslip" value="Generate Payslip" title="Generate Payslip For Process" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
        </div>
    <?php endif; ?>

    <?php if(count($cart['gl_items']) > 0): ?>
        <div id="payslip_trans">
            <div class="bg-white shadow rounded-lg p-4 mb-4">
                <h3 class="font-bold text-lg mb-2">Rows</h3>
                <table class="table-auto border-collapse w-full text-sm">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="border px-2 py-1">Account Code</th>
                            <th class="border px-2 py-1">Account Description</th>
                            <th class="border px-2 py-1">Debit</th>
                            <th class="border px-2 py-1">Credit</th>
                            <th class="border px-2 py-1">Memo</th>
                            <th class="border px-2 py-1"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $k = 0; ?>
                        <?php $__currentLoopData = $cart['gl_items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($edit_index !== null && $edit_index == $line): ?>
                                <tr class="bg-yellow-50">
                                    <td class="border px-2 py-1">
                                        <select name="code_id" class="border px-2 py-1 text-xs">
                                            <option value="">Select GL Account</option>
                                            <?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $acc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($acc->code); ?>" <?php if($item['code_id'] == $acc->code): echo 'selected'; endif; ?>><?php echo e($acc->code); ?> - <?php echo e($acc->name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </td>
                                    <td class="border px-2 py-1">
                                        <?php $acc = $accounts->firstWhere('code', $item['code_id']); ?>
                                        <?php echo e($acc->name ?? ''); ?>

                                        <input type="hidden" name="Index" value="<?php echo e($line); ?>">
                                    </td>
                                    <td class="border px-2 py-1">
                                        <input type="text" name="AmountDebit" value="<?php echo e($item['amount'] > 0 ? number_format($item['amount'], 2, '.', '') : ''); ?>" size="12" class="border px-1 py-0.5 text-right">
                                    </td>
                                    <td class="border px-2 py-1">
                                        <input type="text" name="AmountCredit" value="<?php echo e($item['amount'] < 0 ? number_format(-$item['amount'], 2, '.', '') : ''); ?>" size="12" class="border px-1 py-0.5 text-right">
                                    </td>
                                    <td class="border px-2 py-1">
                                        <input type="text" name="LineMemo" value="<?php echo e($item['memo']); ?>" size="25" class="border px-1 py-0.5">
                                    </td>
                                    <td class="border px-2 py-1">
                                        <input type="submit" name="UpdateItem" value="Update" class="bg-green-500 hover:bg-green-600 text-white px-2 py-0.5 rounded text-xs">
                                        <input type="submit" name="CancelItemChanges" value="Cancel" class="bg-gray-500 hover:bg-gray-600 text-white px-2 py-0.5 rounded text-xs">
                                    </td>
                                </tr>
                            <?php else: ?>
                                <tr class="<?php echo e($k % 2 == 0 ? 'bg-white' : 'bg-gray-50'); ?>">
                                    <?php
                                        $acc = $accounts->firstWhere('code', $item['code_id']);
                                        $desc = $acc->name ?? $item['code_id'];
                                    ?>
                                    <td class="border px-2 py-1"><?php echo e($item['code_id']); ?></td>
                                    <td class="border px-2 py-1"><?php echo e($desc); ?></td>
                                    <?php if($item['amount'] > 0): ?>
                                        <td class="border px-2 py-1 text-right"><?php echo e(number_format($item['amount'], 2)); ?></td>
                                        <td class="border px-2 py-1"></td>
                                    <?php else: ?>
                                        <td class="border px-2 py-1"></td>
                                        <td class="border px-2 py-1 text-right"><?php echo e(number_format(-$item['amount'], 2)); ?></td>
                                    <?php endif; ?>
                                    <td class="border px-2 py-1"><?php echo e($item['memo']); ?></td>
                                    <td class="border px-2 py-1 text-center">
                                        <button type="submit" name="Edit" value="<?php echo e($line); ?>" class="text-blue-600 hover:text-blue-800 text-xs mr-1">Edit</button>
                                        <button type="submit" name="Delete" value="<?php echo e($line); ?>" class="text-red-600 hover:text-red-800 text-xs" onclick="return confirm('Remove line from journal?')">Delete</button>
                                    </td>
                                </tr>
                                <?php $k++; ?>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                    <tfoot>
                        <tr class="font-bold bg-gray-100">
                            <td colspan="2" class="border px-2 py-1 text-right">Total Salary</td>
                            <td class="border px-2 py-1 text-right"><?php echo e(number_format($total_debit, 2)); ?></td>
                            <td class="border px-2 py-1 text-right"><?php echo e(number_format($total_credit, 2)); ?></td>
                            <td class="border px-2 py-1" colspan="2"></td>
                        </tr>
                        <?php if($edit_index === null || !isset($cart['gl_items'][$edit_index])): ?>
                        <tr>
                            <td class="border px-2 py-1">
                                <select name="code_id" class="border px-2 py-1 text-xs">
                                    <option value="">Select GL Account</option>
                                    <?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $acc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($acc->code); ?>" <?php if(old('code_id') == $acc->code): echo 'selected'; endif; ?>><?php echo e($acc->code); ?> - <?php echo e($acc->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </td>
                            <td class="border px-2 py-1">
                                <input type="text" name="description" value="<?php echo e(old('description')); ?>" size="20" readonly class="border-0 bg-transparent">
                            </td>
                            <td class="border px-2 py-1">
                                <input type="text" name="AmountDebit" value="<?php echo e(old('AmountDebit')); ?>" size="12" class="border px-1 py-0.5 text-right">
                            </td>
                            <td class="border px-2 py-1">
                                <input type="text" name="AmountCredit" value="<?php echo e(old('AmountCredit')); ?>" size="12" class="border px-1 py-0.5 text-right">
                            </td>
                            <td class="border px-2 py-1">
                                <input type="text" name="LineMemo" value="<?php echo e(old('LineMemo')); ?>" size="25" class="border px-1 py-0.5">
                            </td>
                            <td class="border px-2 py-1">
                                <input type="submit" name="AddItem" value="Add Item" class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-0.5 rounded text-xs">
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tfoot>
                </table>
            </div>

            <div class="text-center mb-4">
                <textarea name="memo_" rows="3" cols="50" class="border px-2 py-1" placeholder="Memo"><?php echo e(old('memo_', $cart['memo_'] ?? '')); ?></textarea>
            </div>

            <div class="text-center space-x-2">
                <input type="submit" name="Process" value="Process PaySlip" title="Process journal entry only if debits equal to credits" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                <input type="submit" name="CancelOrder" value="Cancel" title="Cancels document entry or removes Gl items" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
            </div>
        </div>
    <?php endif; ?>
</form>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lupu\Desktop\laravel\fa-saas\resources\views/hr/payslips.blade.php ENDPATH**/ ?>