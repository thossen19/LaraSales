<?php $__env->startSection('title', 'Manage Grades'); ?>
<?php $__env->startSection('content'); ?>
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Manage Grades</h2>
</div>

<?php if($msg): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4"><?php echo e($msg); ?></div>
<?php endif; ?>
<?php if($error): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?php echo e($error); ?></div>
<?php endif; ?>

<?php if($position_count == 0): ?>
    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">Please define Job Positions First</div>
<?php endif; ?>

<form method="POST" action="<?php echo e(route('hr.grades')); ?>" class="bg-white shadow rounded-lg mb-6">
    <?php echo csrf_field(); ?>

    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Job Position</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Basic Amount</th>
                <?php for($i = 1; $i <= $grades_no; $i++): ?>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Grade <?php echo e($i); ?></th>
                <?php endfor; ?>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Edit</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Delete</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php $__empty_1 = true; $__currentLoopData = $positions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr class="hover:bg-gray-50 <?php echo e($p->inactive ? 'text-gray-400' : ''); ?>">
                <td class="px-4 py-2 text-sm"><?php echo e($p->position_name); ?></td>
                <td class="px-4 py-2 text-sm text-right"><?php echo e(number_format($p->pay_amount ?? 0, 2)); ?></td>
                <?php for($i = 1; $i <= $grades_no; $i++): ?>
                <td class="px-4 py-2 text-sm text-right">
                    <?php echo e(isset($grade_amounts[$p->position_id][$i]) ? number_format($grade_amounts[$p->position_id][$i], 2) : ''); ?>

                </td>
                <?php endfor; ?>
                <td class="px-4 py-2 text-sm text-center">
                    <button type="submit" name="Edit<?php echo e($p->position_id); ?>" value="1"
                        class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</button>
                </td>
                <td class="px-4 py-2 text-sm text-center">
                    <button type="submit" name="Delete<?php echo e($p->position_id); ?>" value="1"
                        class="text-red-600 hover:text-red-900 text-sm"
                        onclick="return confirm('Are you sure you want to delete grade table for this job position?')">Delete</button>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="<?php echo e(2 + $grades_no + 2); ?>" class="px-4 py-8 text-center text-gray-500">No job positions defined yet.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</form>

<form method="POST" action="<?php echo e(route('hr.grades')); ?>" class="bg-white shadow rounded-lg">
    <?php echo csrf_field(); ?>
    <?php if($selected_id !== -1): ?>
        <input type="hidden" name="selected_id" value="<?php echo e($selected_id); ?>">
    <?php endif; ?>

    <div class="p-6">
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Job Position:</label>
            <select name="position_id"
                class="w-full max-w-lg border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Select Job Position</option>
                <?php $__currentLoopData = $all_positions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pos): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($pos->position_id); ?>"
                    <?php echo e(old('position_id', $selected_id !== -1 ? $selected_id : '') == $pos->position_id ? 'selected' : ''); ?>>
                    <?php echo e($pos->position_name); ?>

                </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        <?php if($selected_id !== -1 && $selected_position): ?>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Basic Amount:</label>
            <span class="text-sm text-gray-900 font-medium"><?php echo e(number_format($selected_position->pay_amount ?? 0, 2)); ?></span>
        </div>
        <?php endif; ?>

        <?php for($i = 1; $i <= $grades_no; $i++): ?>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Grade <?php echo e($i); ?>:</label>
            <input type="text" name="amt_<?php echo e($i); ?>"
                value="<?php echo e(old('amt_'.$i, isset($selected_position_grades[$i]) ? (empty($selected_position_grades[$i]) ? number_format($selected_position->pay_amount ?? 0, 2) : number_format($selected_position_grades[$i], 2)) : '')); ?>"
                class="w-40 border border-gray-300 rounded-md px-3 py-2 text-right focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <?php endfor; ?>
    </div>

    <div class="px-6 py-4 bg-gray-50 rounded-b-lg border-t border-gray-200">
        <?php if($selected_id !== -1): ?>
            <button type="submit" name="UPDATE_ITEM"
                class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                Update
            </button>
            <a href="<?php echo e(route('hr.grades')); ?>"
                class="ml-2 px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-md hover:bg-gray-50 transition duration-150">
                Cancel
            </a>
        <?php else: ?>
            <button type="submit" name="ADD_ITEM"
                class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                Add New
            </button>
        <?php endif; ?>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lupu\Desktop\laravel\fa-saas\resources\views/hr/grades.blade.php ENDPATH**/ ?>