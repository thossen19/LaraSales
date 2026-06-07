<?php $__env->startSection('title', 'Manage Job Positions'); ?>
<?php $__env->startSection('content'); ?>
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Manage Job Positions</h2>
</div>

<?php if($msg): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4"><?php echo e($msg); ?></div>
<?php endif; ?>
<?php if($error): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?php echo e($error); ?></div>
<?php endif; ?>

<form method="POST" action="<?php echo e(route('hr.job-positions')); ?>" class="bg-white shadow rounded-lg mb-6">
    <?php echo csrf_field(); ?>

    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Id</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Salary amount</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pay basis</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Inactive</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Edit</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Delete</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php $__currentLoopData = $positions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr class="hover:bg-gray-50 <?php echo e($p->inactive ? 'text-gray-400' : ''); ?>">
                <td class="px-4 py-2 text-sm"><?php echo e($p->position_id); ?></td>
                <td class="px-4 py-2 text-sm"><?php echo e($p->position_name); ?></td>
                <td class="px-4 py-2 text-sm"><?php echo e(number_format($p->pay_amount ?? 0, 2)); ?></td>
                <td class="px-4 py-2 text-sm"><?php echo e($p->pay_basis == 0 ? 'Monthly' : 'Daily'); ?></td>
                <td class="px-4 py-2 text-sm text-center">
                    <button type="submit" name="toggle_inactive" value="<?php echo e($p->position_id); ?>"
                        class="inline-flex items-center px-2 py-1 rounded text-xs font-medium <?php echo e($p->inactive ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'); ?>">
                        <?php echo e($p->inactive ? 'Yes' : 'No'); ?>

                    </button>
                </td>
                <td class="px-4 py-2 text-sm text-center">
                    <button type="submit" name="Edit<?php echo e($p->position_id); ?>" value="1"
                        class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</button>
                </td>
                <td class="px-4 py-2 text-sm text-center">
                    <button type="submit" name="Delete<?php echo e($p->position_id); ?>" value="1"
                        class="text-red-600 hover:text-red-900 text-sm"
                        onclick="return confirm('Are you sure you want to delete this job position?')">Delete</button>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
        <?php if(!$show_inactive): ?>
        <tfoot class="bg-gray-50">
            <tr>
                <td colspan="7" class="px-4 py-2 text-sm">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="show_inactive" value="1" onchange="this.form.submit()" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-gray-700">Show also inactive</span>
                    </label>
                </td>
            </tr>
        </tfoot>
        <?php endif; ?>
    </table>
</form>

<form method="POST" action="<?php echo e(route('hr.job-positions')); ?>" class="bg-white shadow rounded-lg">
    <?php echo csrf_field(); ?>
    <?php if($selected_id !== -1): ?>
        <input type="hidden" name="selected_id" value="<?php echo e($selected_id); ?>">
    <?php endif; ?>

    <div class="p-6">
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Position Name:</label>
            <input type="text" name="name" value="<?php echo e(old('name', $selected_position->position_name ?? '')); ?>" maxlength="50" size="37"
                class="w-full max-w-lg border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <?php if(!$USE_DEPT_ACC): ?>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Salary Basic Account:</label>
            <select name="AccountId"
                class="w-full max-w-lg border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">&nbsp;</option>
                <?php $__currentLoopData = $all_accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $acc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($acc->code); ?>" <?php echo e(old('AccountId', $selected_position->pay_rule_id ?? '') == $acc->code ? 'selected' : ''); ?>>
                    <?php echo e($acc->code); ?> - <?php echo e($acc->name); ?> (<?php echo e($acc->account_type); ?>)
                </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <?php else: ?>
            <input type="hidden" name="AccountId" value="">
        <?php endif; ?>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Salary Basic Amount:</label>
            <input type="text" name="amount" value="<?php echo e(old('amount', $selected_position->pay_amount ?? '')); ?>"
                class="w-40 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Pay Basis:</label>
            <div class="mt-1">
                <label class="inline-flex items-center mr-4">
                    <input type="radio" name="payBasis" value="0" <?php echo e(old('payBasis', $selected_position->pay_basis ?? '0') == '0' ? 'checked' : ''); ?>

                        class="border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="ml-2 text-sm text-gray-700">Monthly salary</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="radio" name="payBasis" value="1" <?php echo e(old('payBasis', $selected_position->pay_basis ?? '0') == '1' ? 'checked' : ''); ?>

                        class="border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="ml-2 text-sm text-gray-700">Daily wage</span>
                </label>
            </div>
        </div>
    </div>

    <div class="px-6 py-4 bg-gray-50 rounded-b-lg border-t border-gray-200">
        <?php if($selected_id !== -1): ?>
            <button type="submit" name="UPDATE_ITEM"
                class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                Update
            </button>
            <a href="<?php echo e(route('hr.job-positions')); ?>"
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lupu\Desktop\laravel\fa-saas\resources\views/hr/job-positions.blade.php ENDPATH**/ ?>