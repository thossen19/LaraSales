<?php $__env->startSection('title', 'Sales Persons - Sales ERP'); ?>
<?php $__env->startSection('content'); ?>
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Sales Persons</h2>
    <p class="mt-2 text-gray-600">Manage your sales team.</p>
</div>

<?php if(session('success')): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4"><?php echo e(session('success')); ?></div>
<?php endif; ?>
<?php if(session('error')): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?php echo e(session('error')); ?></div>
<?php endif; ?>

<form method="POST" action="<?php echo e(route('sales.setup.persons')); ?>">
<?php echo csrf_field(); ?>
<input type="hidden" name="selected_id" id="selected_id" value="<?php echo e($selected_id); ?>">

<div class="bg-white shadow rounded-lg overflow-hidden mb-6">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fax</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Provision</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Break Pt.</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Provision 2</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Inactive</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase" colspan="2">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php $__empty_1 = true; $__currentLoopData = $persons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $person): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-900"><?php echo e($person->name); ?></td>
                    <td class="px-4 py-3 text-sm text-gray-700"><?php echo e($person->phone); ?></td>
                    <td class="px-4 py-3 text-sm text-gray-700"><?php echo e($person->fax); ?></td>
                    <td class="px-4 py-3 text-sm text-indigo-600"><?php echo e($person->email); ?></td>
                    <td class="px-4 py-3 text-sm text-gray-700 text-right"><?php echo e(number_format($person->commission_rate, 2)); ?>%</td>
                    <td class="px-4 py-3 text-sm text-gray-700 text-right"><?php echo e(number_format($person->monthly_target, 2)); ?></td>
                    <td class="px-4 py-3 text-sm text-gray-700 text-right"><?php echo e(number_format($person->provision2, 2)); ?>%</td>
                    <td class="px-4 py-3 text-center">
                        <a href="<?php echo e(route('sales.setup.persons', ['toggle_inactive' => $person->id, 'show_inactive' => $show_inactive ? '1' : null])); ?>" class="text-sm <?php echo e($person->status === 'inactive' ? 'text-red-600' : 'text-green-600'); ?> hover:underline"><?php echo e($person->status === 'inactive' ? 'Yes' : 'No'); ?></a>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <button type="submit" name="Mode" value="Edit" onclick="this.form.selected_id.value='<?php echo e($person->id); ?>'" class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</button>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <button type="submit" name="Mode" value="Delete" onclick="this.form.selected_id.value='<?php echo e($person->id); ?>';return confirm('Are you sure you want to delete this sales person?')" class="text-red-600 hover:text-red-900 text-sm">Delete</button>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="10" class="px-4 py-8 text-center text-gray-500">No sales persons defined.</td>
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
        <?php echo e($selected_id > 0 ? 'Edit Sales Person' : 'Add New Sales Person'); ?>

    </h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 max-w-2xl">
        <?php if($selected_id > 0): ?>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">ID:</label>
                <input type="text" value="<?php echo e($edit_person->id ?? ''); ?>" class="w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-50 text-gray-500" readonly>
            </div>
        <?php endif; ?>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Sales person name:</label>
            <input type="text" name="salesman_name" value="<?php echo e(old('salesman_name', $edit_name)); ?>" maxlength="255" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Telephone number:</label>
            <input type="text" name="salesman_phone" value="<?php echo e(old('salesman_phone', $edit_phone)); ?>" maxlength="50" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Fax number:</label>
            <input type="text" name="salesman_fax" value="<?php echo e(old('salesman_fax', $edit_fax)); ?>" maxlength="50" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">E-mail:</label>
            <input type="email" name="salesman_email" value="<?php echo e(old('salesman_email', $edit_email)); ?>" maxlength="255" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Provision:</label>
            <input type="text" name="provision" value="<?php echo e(old('provision', $edit_provision)); ?>" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Turnover Break Pt Level:</label>
            <input type="text" name="break_pt" value="<?php echo e(old('break_pt', $edit_break_pt)); ?>" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Provision 2:</label>
            <input type="text" name="provision2" value="<?php echo e(old('provision2', $edit_provision2)); ?>" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lupu\Desktop\laravel\fa-saas\resources\views/sales/setup/persons.blade.php ENDPATH**/ ?>