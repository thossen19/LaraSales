<?php $__env->startSection('title', 'Manage Payroll Rule'); ?>
<?php $__env->startSection('content'); ?>
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Manage Payroll Rule</h2>
</div>

<?php if($msg): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4"><?php echo e($msg); ?></div>
<?php endif; ?>
<?php if($error): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?php echo e($error); ?></div>
<?php endif; ?>

<?php if($position_count > 0): ?>
<form method="POST" action="<?php echo e(route('hr.pay-elements-allocation')); ?>" class="bg-white shadow rounded-lg mb-6">
    <?php echo csrf_field(); ?>

    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center gap-4">
        <label class="block text-sm font-medium text-gray-700">Job Position:</label>
        <select name="PositionId" onchange="this.form.submit()"
            class="w-full max-w-md border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">Select Job Position</option>
            <?php $__currentLoopData = $positions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($p->position_id); ?>" <?php echo e($position_id == $p->position_id ? 'selected' : ''); ?>>
                <?php echo e($p->position_name); ?>

            </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <label class="inline-flex items-center">
            <input type="checkbox" name="show_inactive" value="1" <?php echo e($show_inactive ? 'checked' : ''); ?> onchange="this.form.submit()"
                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
            <span class="ml-2 text-sm text-gray-700">Show inactive:</span>
        </label>
    </div>

    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pay Element</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Account</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Active</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php $__empty_1 = true; $__currentLoopData = $rules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-2 text-sm"><?php echo e($r->element_name); ?></td>
                <td class="px-4 py-2 text-sm"><?php echo e($r->account_code); ?> - <?php echo e($r->account_name); ?></td>
                <td class="px-4 py-2 text-sm text-center">
                    <input type="checkbox" name="Payroll<?php echo e($r->account_code); ?>" value="1"
                        <?php echo e(in_array($r->account_code, $existing_rules) ? 'checked' : ''); ?>

                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="3" class="px-4 py-8 text-center text-gray-500">No pay elements defined yet.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 text-center flex items-center justify-center gap-4">
        <?php if($has_rules): ?>
            <button type="submit" name="submit" value="1"
                class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                Update
            </button>
            <button type="submit" name="delete" value="1"
                class="px-4 py-2 border border-red-300 text-red-700 font-medium rounded-md hover:bg-red-50 transition duration-150"
                onclick="return confirm('Delete payroll rules?')">
                Delete
            </button>
        <?php else: ?>
            <button type="submit" name="submit" value="1"
                class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                Save
            </button>
        <?php endif; ?>
    </div>
</form>
<?php else: ?>
    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">Define Job Positions first.</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lupu\Desktop\laravel\fa-saas\resources\views/hr/pay-elements-allocation.blade.php ENDPATH**/ ?>