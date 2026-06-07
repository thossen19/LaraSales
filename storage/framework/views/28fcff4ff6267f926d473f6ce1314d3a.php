<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<?php $__env->stopPush(); ?>
<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
flatpickr('#from_date', { dateFormat: 'd/m/Y' });
flatpickr('#to_date', { dateFormat: 'd/m/Y' });
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('title', 'Employees Attendance'); ?>
<?php $__env->startSection('content'); ?>
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Employees Attendance</h2>
</div>

<?php if($msg): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4"><?php echo e($msg); ?></div>
<?php endif; ?>
<?php if($error): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?php echo e($error); ?></div>
<?php endif; ?>

<?php if(!$has_employee): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">There are no employees for attendance.</div>
<?php endif; ?>

<form method="post" action="<?php echo e(url()->current()); ?>">
    <?php echo csrf_field(); ?>

    <table class="table-auto border-collapse bg-white shadow rounded-lg mb-4">
        <tr>
            <td class="p-2">
                <table class="w-full">
                    <tr>
                        <td class="p-1 text-right font-semibold">From:</td>
                        <td class="p-1"><input type="text" name="from_date" id="from_date" value="<?php echo e($from_date); ?>" size="10" maxlength="10" class="border px-2 py-1"></td>
                        <td class="p-1 text-right font-semibold">To:</td>
                        <td class="p-1"><input type="text" name="to_date" id="to_date" value="<?php echo e($to_date); ?>" size="10" maxlength="10" class="border px-2 py-1"></td>
                        <td class="p-1 text-right font-semibold">Department:</td>
                        <td class="p-1">
                            <select name="DeptId" class="border px-2 py-1" onchange="this.form.submit();">
                                <option value="">All departments</option>
                                <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($dept->dept_id); ?>" <?php if($dept_id == $dept->dept_id): echo 'selected'; endif; ?>><?php echo e($dept->dept_name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </td>
                        <td class="p-1">
                            <input type="submit" name="bulk" value="Bulk" title="Record all as regular work" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded">
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <?php if($has_employee): ?>
        <table class="table-auto border-collapse bg-white shadow rounded-lg w-full text-sm">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border px-2 py-1">ID</th>
                    <th class="border px-2 py-1">Employee</th>
                    <th class="border px-2 py-1">Regular time</th>
                    <?php $__currentLoopData = $overtimes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <th class="border px-2 py-1"><?php echo e($ot->overtime_name); ?></th>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <th class="border px-2 py-1">Leave Type</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $checked = request()->method() == 'GET' || request((string)$emp->id) == 1;
                        $reg_val = old($emp->id . '-0', request($emp->id . '-0', ''));
                        $leave_val = old($emp->id . '-leave', request($emp->id . '-leave', ''));
                    ?>
                    <tr>
                        <td class="border px-2 py-1">
                            <?php echo e($emp->id); ?>

                            <input type="checkbox" name="<?php echo e($emp->id); ?>" value="1" <?php echo e($checked ? 'checked' : ''); ?>>
                        </td>
                        <td class="border px-2 py-1"><?php echo e($emp->name); ?></td>
                        <td class="border px-2 py-1">
                            <input type="text" name="<?php echo e($emp->id); ?>-0" value="<?php echo e($reg_val); ?>" size="10" maxlength="10" class="border px-2 py-1 text-right">
                        </td>
                        <?php $__currentLoopData = $overtimes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $ot_val = old($emp->id . '-' . $ot->overtime_id, request($emp->id . '-' . $ot->overtime_id, ''));
                            ?>
                            <td class="border px-2 py-1">
                                <input type="text" name="<?php echo e($emp->id); ?>-<?php echo e($ot->overtime_id); ?>" value="<?php echo e($ot_val); ?>" size="10" maxlength="10" class="border px-2 py-1 text-right">
                            </td>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <td class="border px-2 py-1">
                            <select name="<?php echo e($emp->id); ?>-leave" class="border px-2 py-1">
                                <option value="">Select Leave Type</option>
                                <?php $__currentLoopData = $leave_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($lt->leave_id); ?>" <?php if($leave_val == $lt->leave_id): echo 'selected'; endif; ?>><?php echo e($lt->leave_name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        <br>
        <div class="text-center">
            <input type="submit" name="addatt" value="Save attendance" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
        </div>
    <?php endif; ?>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lupu\Desktop\laravel\fa-saas\resources\views/hr/attendance.blade.php ENDPATH**/ ?>