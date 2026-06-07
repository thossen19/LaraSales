<?php $__env->startSection('title', 'Manage Department'); ?>
<?php $__env->startSection('content'); ?>
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Manage Department</h2>
</div>

<?php if($msg): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4"><?php echo e($msg); ?></div>
<?php endif; ?>
<?php if($error): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?php echo e($error); ?></div>
<?php endif; ?>

<form method="POST" action="<?php echo e(route('hr.departments')); ?>" class="bg-white shadow rounded-lg mb-6">
    <?php echo csrf_field(); ?>

    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Id</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Department Name</th>
                <?php if($USE_DEPT_ACC): ?>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Salary Basic Account</th>
                <?php endif; ?>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Inactive</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Edit</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Delete</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr class="hover:bg-gray-50 <?php echo e($d->inactive ? 'text-gray-400' : ''); ?>">
                <td class="px-4 py-2 text-sm"><?php echo e($d->dept_id); ?></td>
                <td class="px-4 py-2 text-sm"><?php echo e($d->dept_name); ?></td>
                <?php if($USE_DEPT_ACC): ?>
                <td class="px-4 py-2 text-sm"><?php echo e($d->basic_account); ?></td>
                <?php endif; ?>
                <td class="px-4 py-2 text-sm text-center">
                    <button type="submit" name="toggle_inactive" value="<?php echo e($d->dept_id); ?>"
                        class="inline-flex items-center px-2 py-1 rounded text-xs font-medium <?php echo e($d->inactive ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'); ?>">
                        <?php echo e($d->inactive ? 'Yes' : 'No'); ?>

                    </button>
                </td>
                <td class="px-4 py-2 text-sm text-center">
                    <button type="submit" name="Edit<?php echo e($d->dept_id); ?>" value="1"
                        class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</button>
                </td>
                <td class="px-4 py-2 text-sm text-center">
                    <button type="submit" name="Delete<?php echo e($d->dept_id); ?>" value="1"
                        class="text-red-600 hover:text-red-900 text-sm"
                        onclick="return confirm('Are you sure you want to delete this department?')">Delete</button>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
        <?php if(!$show_inactive): ?>
        <tfoot class="bg-gray-50">
            <tr>
                <td colspan="<?php echo e($USE_DEPT_ACC ? 6 : 5); ?>" class="px-4 py-2 text-sm">
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

<form method="POST" action="<?php echo e(route('hr.departments')); ?>" class="bg-white shadow rounded-lg">
    <?php echo csrf_field(); ?>
    <?php if($selected_id): ?>
        <input type="hidden" name="selected_id" value="<?php echo e($selected_id); ?>">
    <?php endif; ?>

    <div class="p-6">
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Department Name:</label>
            <input type="text" name="name" value="<?php echo e(old('name', $selected_department->dept_name ?? '')); ?>" maxlength="60"
                class="w-full max-w-lg border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <?php if($USE_DEPT_ACC): ?>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Salary Basic Account:</label>
            <select name="basic_acc"
                class="w-full max-w-lg border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Select basic account</option>
                <?php $__currentLoopData = $all_accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $acc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($acc->code); ?>" <?php echo e(old('basic_acc', $selected_department->basic_account ?? '') == $acc->code ? 'selected' : ''); ?>>
                    <?php echo e($acc->code); ?> - <?php echo e($acc->name); ?> (<?php echo e($acc->account_type); ?>)
                </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <?php else: ?>
            <input type="hidden" name="basic_acc" value="">
        <?php endif; ?>
    </div>

    <div class="px-6 py-4 bg-gray-50 rounded-b-lg border-t border-gray-200">
        <?php if($selected_id): ?>
            <button type="submit" name="UPDATE_ITEM"
                class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                Update
            </button>
            <a href="<?php echo e(route('hr.departments')); ?>"
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lupu\Desktop\laravel\fa-saas\resources\views/hr/departments.blade.php ENDPATH**/ ?>