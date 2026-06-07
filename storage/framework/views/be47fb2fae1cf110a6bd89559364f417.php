<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<?php $__env->stopPush(); ?>
<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
flatpickr('#FromDate', { dateFormat: 'Y-m-d' });
flatpickr('#ToDate', { dateFormat: 'Y-m-d' });
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('title', 'Timesheet Inquiry'); ?>
<?php $__env->startSection('content'); ?>
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Timesheet Inquiry</h2>
</div>

<?php if($msg): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4"><?php echo $msg; ?></div>
<?php endif; ?>
<?php if($error): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?php echo $error; ?></div>
<?php endif; ?>

<form method="post" action="<?php echo e(route('hr.timesheet')); ?>">
    <?php echo csrf_field(); ?>
    <table class="table-auto border-collapse bg-white shadow rounded-lg w-full mb-4">
        <tr>
            <td class="border px-3 py-2">
                <table class="w-full">
                    <tr>
                        <td class="p-1">
                            <select name="DeptId" class="border px-2 py-1" onchange="document.forms[0].submit();">
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
                        <td class="p-1 text-right font-semibold">From:</td>
                        <td class="p-1"><input type="text" id="FromDate" name="FromDate" value="<?php echo e($from_date); ?>" size="12" class="border px-2 py-1"></td>
                        <td class="p-1 text-right font-semibold">To:</td>
                        <td class="p-1"><input type="text" id="ToDate" name="ToDate" value="<?php echo e($to_date); ?>" size="12" class="border px-2 py-1"></td>
                        <td class="p-1">
                            <select name="OvertimeId" class="border px-2 py-1">
                                <option value="">Regular time</option>
                                <?php $__currentLoopData = $overtimes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($ot->overtime_id); ?>" <?php if($ot_id !== '' && $ot_id == $ot->overtime_id): echo 'selected'; endif; ?>><?php echo e($ot->overtime_name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </td>
                        <td class="p-1">
                            <input type="submit" name="Search" value="Search" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded">
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</form>

<?php if($search && count($day_columns) > 0): ?>
    <div class="bg-white shadow rounded-lg overflow-x-auto">
        <table class="table-auto border-collapse w-full text-sm whitespace-nowrap">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border px-2 py-1 text-center">Id</th>
                    <th class="border px-2 py-1">Employee Name</th>
                    <?php $__currentLoopData = $day_columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($col['is_weekend']): ?>
                            <th class="border px-2 py-1 text-center" style="background:#FFCCCC;"><?php echo e($col['day']); ?><p hidden><?php echo e($col['month']); ?></p></th>
                        <?php else: ?>
                            <th class="border px-2 py-1 text-center"><?php echo e($col['day']); ?><p hidden><?php echo e($col['month']); ?></p></th>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="<?php echo e($loop->even ? 'bg-gray-50' : 'bg-white'); ?>">
                        <td class="border px-2 py-1 text-center"><?php echo e($emp->id); ?></td>
                        <td class="border px-2 py-1"><?php echo e($emp->first_name); ?> <?php echo e($emp->last_name); ?></td>
                        <?php $__currentLoopData = $day_columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $display = '';
                                $bg = '';
                                $att = $att_data[$emp->id][$col['date']] ?? null;
                                $lev = $lev_data[$emp->id][$col['date']] ?? null;

                                if ($att) {
                                    $display = $att->hours;
                                } elseif ($lev) {
                                    $code = $lev->leave_code;
                                    if ($lev->leave_pay_rate >= 100)
                                        $display = '<b style="color:green">' . $code . '</b>';
                                    elseif ($lev->leave_pay_rate > 0)
                                        $display = '<b style="color:orange">' . $code . '</b>';
                                    else
                                        $display = '<b style="color:red">' . $code . '</b>';
                                }
                            ?>
                            <?php if($col['is_weekend']): ?>
                                <td class="border px-2 py-1 text-center" style="background:#FFCCCC;"><?php echo $display; ?></td>
                            <?php else: ?>
                                <td class="border px-2 py-1 text-center"><?php echo $display; ?></td>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td class="border px-2 py-1 text-center" colspan="<?php echo e(count($day_columns) + 2); ?>">No records</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if(method_exists($employees, 'links')): ?>
        <div class="mt-4">
            <?php echo e($employees->appends(request()->except('_token'))->links()); ?>

        </div>
    <?php endif; ?>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lupu\Desktop\laravel\fa-saas\resources\views/hr/timesheet.blade.php ENDPATH**/ ?>