<?php $__env->startSection('title', 'Sales Groups - Sales ERP'); ?>
<?php $__env->startSection('content'); ?>
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Sales Groups</h2>
    <p class="mt-2 text-gray-600">Manage customer groups for sales analysis and reporting.</p>
</div>

<?php if(session('success')): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4"><?php echo e(session('success')); ?></div>
<?php endif; ?>
<?php if(session('error')): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?php echo e(session('error')); ?></div>
<?php endif; ?>

<form method="POST" action="<?php echo e(route('sales.setup.groups')); ?>">
<?php echo csrf_field(); ?>
<input type="hidden" name="selected_id" id="selected_id" value="<?php echo e($selected_id); ?>">

<div class="bg-white shadow rounded-lg overflow-hidden mb-6">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Group Name</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Inactive</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase" colspan="2">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php $__empty_1 = true; $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-900"><?php echo e($group->id); ?></td>
                    <td class="px-4 py-3 text-sm text-gray-900"><?php echo e($group->group_name); ?></td>
                    <td class="px-4 py-3 text-center">
                        <a href="<?php echo e(route('sales.setup.groups', ['toggle_inactive' => $group->id, 'show_inactive' => $show_inactive ? '1' : null])); ?>" class="text-sm <?php echo e($group->status === 'inactive' ? 'text-red-600' : 'text-green-600'); ?> hover:underline"><?php echo e($group->status === 'inactive' ? 'Yes' : 'No'); ?></a>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <button type="submit" name="Mode" value="Edit" onclick="this.form.selected_id.value='<?php echo e($group->id); ?>'" class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</button>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <button type="submit" name="Mode" value="Delete" onclick="this.form.selected_id.value='<?php echo e($group->id); ?>';return confirm('Are you sure you want to delete this group?')" class="text-red-600 hover:text-red-900 text-sm">Delete</button>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">No sales groups defined.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="mb-6">
    <label class="flex items-center text-sm text-gray-700 cursor-pointer">
        <input type="checkbox" name="show_inactive" value="1" <?php echo e($show_inactive ? 'checked' : ''); ?> onchange="this.form.submit()" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
        <span class="ml-2">Show also inactive</span>
    </label>
</div>

<div class="bg-white shadow rounded-lg p-6">
    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">
        <?php echo e($selected_id > 0 ? 'Edit Sales Group' : 'Add New Sales Group'); ?>

    </h3>

    <div class="space-y-4 max-w-lg">
        <?php if($selected_id > 0): ?>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ID:</label>
                <input type="text" value="<?php echo e($edit_group->id ?? ''); ?>" class="w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-50 text-gray-500" readonly>
            </div>
        <?php endif; ?>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Group Name:</label>
            <input type="text" name="description" value="<?php echo e(old('description', $edit_description)); ?>" maxlength="100" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
        </div>
    </div>

    <div class="pt-4 mt-4 border-t border-gray-200">
        <?php if($selected_id > 0): ?>
            <button type="submit" name="Mode" value="UPDATE_ITEM" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition">Update</button>
            <button type="submit" name="Mode" value="RESET" class="px-6 py-2 ml-2 bg-gray-200 text-gray-800 font-medium rounded-md hover:bg-gray-300 transition">Cancel</button>
        <?php else: ?>
            <button type="submit" name="Mode" value="ADD_ITEM" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition">Add New</button>
        <?php endif; ?>
    </div>
</div>

</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lupu\Desktop\laravel\fa-saas\resources\views/sales/setup/groups.blade.php ENDPATH**/ ?>