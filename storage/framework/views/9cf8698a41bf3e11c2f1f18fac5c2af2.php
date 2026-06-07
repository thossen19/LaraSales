<?php $__env->startSection('title', 'Shipping Company - Sales ERP'); ?>
<?php $__env->startSection('content'); ?>
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Shipping Company</h2>
    <p class="mt-2 text-gray-600">Manage shipping companies.</p>
</div>

<?php if(session('success')): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4"><?php echo e(session('success')); ?></div>
<?php endif; ?>
<?php if(session('error')): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?php echo e(session('error')); ?></div>
<?php endif; ?>

<form method="POST" action="<?php echo e(route('setup.shipping-company')); ?>" class="mb-4">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="action" value="toggle_show_inactive">
    <label class="flex items-center text-sm text-gray-700 cursor-pointer">
        <input type="checkbox" name="show_inactive" value="1" <?php echo e($show_inactive ? 'checked' : ''); ?> onchange="this.form.submit()" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
        <span class="ml-2">Show also inactive</span>
    </label>
</form>

<div class="bg-white shadow rounded-lg overflow-hidden mb-6">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact Person</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone Number</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Secondary Phone</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Address</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Inactive</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase" colspan="2">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php $__empty_1 = true; $__currentLoopData = $shippers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-900"><?php echo e($s->shipper_name); ?></td>
                    <td class="px-4 py-3 text-sm text-gray-600"><?php echo e($s->contact); ?></td>
                    <td class="px-4 py-3 text-sm text-gray-600"><?php echo e($s->phone); ?></td>
                    <td class="px-4 py-3 text-sm text-gray-600"><?php echo e($s->phone2); ?></td>
                    <td class="px-4 py-3 text-sm text-gray-600 max-w-xs truncate"><?php echo e($s->address); ?></td>
                    <td class="px-4 py-3 text-center">
                        <form method="POST" action="<?php echo e(route('setup.shipping-company')); ?>" class="inline">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="action" value="toggle_inactive">
                            <input type="hidden" name="selected_id" value="<?php echo e($s->shipper_id); ?>">
                            <button type="submit" class="text-sm <?php echo e($s->inactive ? 'text-red-600' : 'text-green-600'); ?> hover:underline">
                                <?php echo e($s->inactive ? 'Yes' : 'No'); ?>

                            </button>
                        </form>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <form method="POST" action="<?php echo e(route('setup.shipping-company')); ?>" class="inline">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="selected_id" value="<?php echo e($s->shipper_id); ?>">
                            <button type="submit" class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</button>
                        </form>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <form method="POST" action="<?php echo e(route('setup.shipping-company')); ?>" class="inline" onsubmit="return confirm('Are you sure you want to delete this shipping company?');">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="selected_id" value="<?php echo e($s->shipper_id); ?>">
                            <button type="submit" class="text-red-600 hover:text-red-900 text-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="8" class="px-4 py-8 text-center text-gray-500">No shipping companies defined yet.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="bg-white shadow rounded-lg p-6">
    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">
        <?php echo e($edit_shipper ? 'Edit Shipping Company' : 'Add New Shipping Company'); ?>

    </h3>
    <form method="POST" action="<?php echo e(route('setup.shipping-company')); ?>">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="action" value="<?php echo e($edit_shipper ? 'update' : 'add'); ?>">
        <?php if($edit_shipper): ?>
            <input type="hidden" name="selected_id" value="<?php echo e($edit_shipper->shipper_id); ?>">
        <?php endif; ?>

        <div class="space-y-4 max-w-lg">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Name:</label>
                <input type="text" name="shipper_name" value="<?php echo e(old('shipper_name', $edit_shipper->shipper_name ?? '')); ?>" maxlength="60" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 <?php $__errorArgs = ['shipper_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <?php $__errorArgs = ['shipper_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contact Person:</label>
                <input type="text" name="contact" value="<?php echo e(old('contact', $edit_shipper->contact ?? '')); ?>" maxlength="60" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number:</label>
                <input type="text" name="phone" value="<?php echo e(old('phone', $edit_shipper->phone ?? '')); ?>" maxlength="30" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Secondary Phone Number:</label>
                <input type="text" name="phone2" value="<?php echo e(old('phone2', $edit_shipper->phone2 ?? '')); ?>" maxlength="30" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Address:</label>
                <input type="text" name="address" value="<?php echo e(old('address', $edit_shipper->address ?? '')); ?>" maxlength="60" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>

        <div class="pt-4 mt-4 border-t border-gray-200 flex items-center gap-3">
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                <?php echo e($edit_shipper ? 'Update' : 'Add New'); ?>

            </button>
            <?php if($edit_shipper): ?>
                <a href="<?php echo e(route('setup.shipping-company')); ?>" class="px-4 py-2 bg-gray-200 text-gray-700 font-medium rounded-md hover:bg-gray-300 transition duration-150">Cancel</a>
            <?php endif; ?>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lupu\Desktop\laravel\fa-saas\resources\views/setup/shipping-company.blade.php ENDPATH**/ ?>