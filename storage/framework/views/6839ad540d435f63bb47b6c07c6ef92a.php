<?php $__env->startSection('title', 'Manage Salary Structure'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Manage Salary Structure</h2>
</div>

<?php if($msg): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4"><?php echo e($msg); ?></div>
<?php endif; ?>
<?php if($error): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?php echo e($error); ?></div>
<?php endif; ?>

<form method="POST" action="<?php echo e(route('hr.salary-structure')); ?>">
    <?php echo csrf_field(); ?>

    <?php if($has_positions): ?>
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-1">Job Position:</label>
        <div class="flex items-center gap-3">
            <select name="position_id" onchange="this.form.submit()"
                class="w-full max-w-md border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Select Job Position</option>
                <?php $__currentLoopData = $positions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($p->position_id); ?>" <?php echo e($position_id == $p->position_id ? 'selected' : ''); ?>>
                        <?php echo e($p->position_name); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <noscript>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Show</button>
            </noscript>
        </div>
    </div>
    <?php else: ?>
    <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded mb-4">
        Before this function can be run, Job Positions must be defined and Pay Elements must be added.
    </div>
    <?php endif; ?>

    <?php if($position_id): ?>
    <div class="bg-white shadow rounded-lg p-6">
        <?php if(!$has_elements): ?>
            <div class="text-center py-8 text-gray-500">
                <p>Selected job position has not been assigned any pay element yet</p>
            </div>
        <?php else: ?>
        <div class="mb-4">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-4">
                    <a href="<?php echo e(route('hr.salary-structure', ['position_id' => $position_id, '_tabs_sel' => 0])); ?>"
                       class="px-4 py-2 text-sm font-medium <?php echo e($active_tab == 0 ? 'border-b-2 border-indigo-500 text-indigo-600' : 'text-gray-500 hover:text-gray-700'); ?>">
                        Basic
                    </a>
                    <?php for($i = 1; $i <= $grades_count; $i++): ?>
                        <a href="<?php echo e(route('hr.salary-structure', ['position_id' => $position_id, '_tabs_sel' => $i])); ?>"
                           class="px-4 py-2 text-sm font-medium <?php echo e($active_tab == $i ? 'border-b-2 border-indigo-500 text-indigo-600' : 'text-gray-500 hover:text-gray-700'); ?>">
                            Grade <?php echo e($i); ?>

                        </a>
                    <?php endfor; ?>
                </nav>
            </div>
        </div>

        <?php if($grade_exists): ?>
            <input type="hidden" name="_tabs_sel" value="<?php echo e($active_tab); ?>">
            <input type="hidden" name="position_id" value="<?php echo e($position_id); ?>">

            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pay Element</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase"><?php echo e($pay_basis == 1 ? 'Daily' : 'Monthly'); ?> Earnings</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase"><?php echo e($pay_basis == 1 ? 'Daily' : 'Monthly'); ?> Deductions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr class="bg-gray-100">
                        <td class="px-4 py-2 text-sm font-medium text-gray-700">Basic Salary</td>
                        <td class="px-4 py-2 text-sm"><?php echo e($basic_salary ? number_format($basic_salary->pay_amount, 2) : '0.00'); ?></td>
                        <td class="px-4 py-2 text-sm">0.00</td>
                    </tr>

                    <?php $__currentLoopData = $elements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $elem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $code = $elem->account_code;
                            $existing = $existing_salary->get($code);
                            $debit_val = ($existing && $existing->type == 1) ? $existing->pay_amount : 0;
                            $credit_val = ($existing && $existing->type == 0) ? $existing->pay_amount : 0;
                        ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 text-sm text-gray-700">
                                <input type="hidden" name="Account<?php echo e($code); ?>" value="<?php echo e($code); ?>">
                                <?php echo e($elem->element_name); ?>

                            </td>
                            <td class="px-4 py-2">
                                <input type="text" name="Debit<?php echo e($code); ?>" value="<?php echo e(number_format($debit_val, 2)); ?>"
                                    class="w-32 border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </td>
                            <td class="px-4 py-2">
                                <input type="text" name="Credit<?php echo e($code); ?>" value="<?php echo e(number_format($credit_val, 2)); ?>"
                                    class="w-32 border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>

            <div class="mt-6 flex items-center gap-3">
                <?php if($has_existing_data): ?>
                    <button type="submit" name="submit" value="1"
                        class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                        Update
                    </button>
                    <button type="submit" name="delete" value="1" onclick="return confirm('Are you sure you want to delete this salary structure?')"
                        class="px-4 py-2 border border-red-300 text-red-700 font-medium rounded-md hover:bg-red-50 transition duration-150">
                        Delete
                    </button>
                <?php else: ?>
                    <button type="submit" name="submit" value="1"
                        class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                        Save salary structure
                    </button>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-8 text-gray-500">
                <p>Please define grade amount for the selected job position first.</p>
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    <?php endif; ?>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lupu\Desktop\laravel\fa-saas\resources\views/hr/salary-structure.blade.php ENDPATH**/ ?>