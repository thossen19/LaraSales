<?php $__env->startSection('title', 'Employee Transaction Inquiry'); ?>
<?php $__env->startSection('content'); ?>
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Employee Transaction Inquiry</h2>
</div>

<?php if($msg): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4"><?php echo $msg; ?></div>
<?php endif; ?>
<?php if($error): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?php echo $error; ?></div>
<?php endif; ?>

<form method="post" action="<?php echo e(route('hr.inquiries.transactions')); ?>">
    <?php echo csrf_field(); ?>
    <table class="table-auto border-collapse bg-white shadow rounded-lg w-full mb-4">
        <tr>
            <td class="border px-3 py-2">
                <table class="w-full">
                    <tr>
                        <td class="p-1 text-right font-semibold">Reference:</td>
                        <td class="p-1"><input type="text" name="Ref" value="<?php echo e($ref); ?>" size="15" class="border px-2 py-1" placeholder="Enter reference fragment or leave empty"></td>
                        <td class="p-1 text-right font-semibold">Memo:</td>
                        <td class="p-1"><input type="text" name="Memo" value="<?php echo e($memo); ?>" size="15" class="border px-2 py-1" placeholder="Enter memo fragment or leave empty"></td>
                        <td class="p-1 text-right font-semibold">From:</td>
                        <td class="p-1"><input type="text" name="FromDate" value="<?php echo e($from_date); ?>" size="12" class="border px-2 py-1"></td>
                        <td class="p-1 text-right font-semibold">To:</td>
                        <td class="p-1"><input type="text" name="ToDate" value="<?php echo e($to_date); ?>" size="12" class="border px-2 py-1"></td>
                    </tr>
                </table>
            </td>
            <td></td>
        </tr>
        <tr>
            <td class="border px-3 py-2">
                <table class="w-full">
                    <tr>
                        <td class="p-1">
                            <select name="DeptId" class="border px-2 py-1" onchange="this.form.submit();">
                                <option value="">All departments</option>
                                <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($dept->dept_id); ?>" <?php if($dept_id == $dept->dept_id): echo 'selected'; endif; ?>><?php echo e($dept->dept_name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </td>
                        <td class="p-1">
                            <select name="EmpId" class="border px-2 py-1 min-w-[180px]">
                                <option value="">All employees</option>
                                <?php $__currentLoopData = $employees_filter; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($emp->id); ?>" <?php if($emp_id == $emp->id): echo 'selected'; endif; ?>><?php echo e($emp->first_name); ?> <?php echo e($emp->last_name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </td>
                        <td class="p-1">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="OnlyUnpaid" value="1" <?php echo e($only_unpaid ? 'checked' : ''); ?> class="mr-1">
                                <span class="font-semibold">Only unpaid:</span>
                            </label>
                        </td>
                        <td class="p-1">
                            <input type="submit" name="Search" value="Search" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded">
                        </td>
                    </tr>
                </table>
            </td>
            <td></td>
        </tr>
    </table>
</form>

<?php if($has_searched): ?>
    <div class="bg-white shadow rounded-lg overflow-x-auto">
        <table class="table-auto border-collapse w-full text-sm">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border px-2 py-1">Date</th>
                    <th class="border px-2 py-1">Trans #</th>
                    <th class="border px-2 py-1">Type</th>
                    <th class="border px-2 py-1">Employee ID</th>
                    <th class="border px-2 py-1">Employee Name</th>
                    <th class="border px-2 py-1">Payslip No</th>
                    <th class="border px-2 py-1">Pay from</th>
                    <th class="border px-2 py-1">Pay to</th>
                    <th class="border px-2 py-1 text-right">Amount</th>
                    <th class="border px-2 py-1 text-center"></th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $is_payslip = $row->type == 0;
                        $is_advance = $row->type == 1 && ($row->payslip_no == 0 || $row->payslip_no === null);
                        $is_advice = $row->type == 1 && $row->payslip_no != 0;

                        if ($is_payslip) $type_label = 'Payslip';
                        elseif ($is_advance) $type_label = 'Employee advance';
                        else $type_label = 'Payment advice';
                    ?>
                    <tr class="<?php echo e($loop->even ? 'bg-gray-50' : 'bg-white'); ?>">
                        <td class="border px-2 py-1"><?php echo e($row->trans_date); ?></td>
                        <td class="border px-2 py-1">
                            <?php if($row->trans_no != 0): ?>
                                <a href="<?php echo e(route('hr.payslips', ['AddedID' => $row->trans_no])); ?>" class="text-blue-600 hover:text-blue-800"><?php echo e($row->trans_no); ?></a>
                            <?php endif; ?>
                        </td>
                        <td class="border px-2 py-1"><?php echo e($type_label); ?></td>
                        <td class="border px-2 py-1"><?php echo e($row->emp_id); ?></td>
                        <td class="border px-2 py-1"><?php echo e($row->emp_name); ?></td>
                        <td class="border px-2 py-1"><?php echo e($row->payslip_no ?: ''); ?></td>
                        <td class="border px-2 py-1"><?php echo e($row->from_date ?? ''); ?></td>
                        <td class="border px-2 py-1"><?php echo e($row->to_date ?? ''); ?></td>
                        <td class="border px-2 py-1 text-right"><?php echo e(number_format($row->amount, 2)); ?></td>
                        <td class="border px-2 py-1 text-center">
                            <?php if($is_advice): ?>
                                <a href="<?php echo e(route('hr.payslips', ['AddedID' => $row->trans_no])); ?>" class="text-blue-600 hover:text-blue-800 text-xs" title="Print this Payslip">Print</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td class="border px-2 py-1 text-center" colspan="10">No records</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lupu\Desktop\laravel\fa-saas\resources\views/hr/inquiries/transactions.blade.php ENDPATH**/ ?>