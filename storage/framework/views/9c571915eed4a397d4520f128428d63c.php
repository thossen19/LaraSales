<?php $__env->startSection('title', 'Employee Attach Documents'); ?>
<?php $__env->startSection('content'); ?>
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Employee Attach Documents</h2>
</div>

<?php if($msg): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4"><?php echo e($msg); ?></div>
<?php endif; ?>
<?php if($error): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?php echo $error; ?></div>
<?php endif; ?>

<?php if(!$has_doc_types): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">There are no <b>Document Types</b> defined in the system</div>
<?php endif; ?>

<form method="post" action="<?php echo e(url()->current()); ?>" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>

    
    <table class="table-auto border-collapse bg-white shadow rounded-lg mb-4 w-full">
        <?php if(empty($view_mode)): ?>
        <tr>
            <td class="p-2">
                <select name="emp_id" class="border px-2 py-1 min-w-[200px]">
                    <option value="">Select employee</option>
                    <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($emp->id); ?>" <?php if($emp_id == $emp->id): echo 'selected'; endif; ?>><?php echo e($emp->first_name); ?> <?php echo e($emp->last_name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </td>
        </tr>
        <?php else: ?>
        <tr>
            <td class="p-2">
                <table class="w-full">
                    <tr>
                        <td class="p-1"><input type="text" name="string" value="<?php echo e(request('string')); ?>" size="30" placeholder="Enter search string" class="border px-2 py-1"></td>
                        <td class="p-1">
                            <select name="emp_id" class="border px-2 py-1 min-w-[150px]">
                                <option value="">All employees</option>
                                <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($emp->id); ?>" <?php if(request('emp_id') == $emp->id): echo 'selected'; endif; ?>><?php echo e($emp->first_name); ?> <?php echo e($emp->last_name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </td>
                        <td class="p-1">
                            <select name="type_id" class="border px-2 py-1 min-w-[150px]">
                                <option value="">All document type</option>
                                <?php $__currentLoopData = $doc_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($dt->type_id); ?>" <?php if(request('type_id') == $dt->type_id): echo 'selected'; endif; ?>><?php echo e($dt->type_name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </td>
                        <td class="p-1"><label><input type="checkbox" name="alert" value="1" <?php echo e(request('alert') ? 'checked' : ''); ?>> Alert</label></td>
                        <td class="p-1"><label><input type="checkbox" name="no_alert" value="1" <?php echo e(request('no_alert') ? 'checked' : ''); ?>> Not Alert</label></td>
                    </tr>
                    <tr>
                        <td class="p-1">Expired: <input type="text" name="expired_from" value="<?php echo e(request('expired_from')); ?>" size="10" class="border px-2 py-1"></td>
                        <td class="p-1">To: <input type="text" name="expired_to" value="<?php echo e(request('expired_to')); ?>" size="10" class="border px-2 py-1"></td>
                        <td class="p-1">Issued: <input type="text" name="issue_from" value="<?php echo e(request('issue_from')); ?>" size="10" class="border px-2 py-1"></td>
                        <td class="p-1">To: <input type="text" name="issue_to" value="<?php echo e(request('issue_to')); ?>" size="10" class="border px-2 py-1"></td>
                        <td class="p-1"><input type="submit" name="Search" value="Search" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded"></td>
                    </tr>
                </table>
            </td>
        </tr>
        <?php endif; ?>
    </table>

    
    <div class="bg-white shadow rounded-lg overflow-x-auto mb-4">
        <table class="table-auto w-full text-sm">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border px-2 py-1">Doc No</th>
                    <th class="border px-2 py-1">Document Type</th>
                    <th class="border px-2 py-1">Document Title</th>
                    <th class="border px-2 py-1">Issue Date</th>
                    <th class="border px-2 py-1">Expiry Date</th>
                    <th class="border px-2 py-1">Alert</th>
                    <th class="border px-2 py-1">Filename</th>
                    <th class="border px-2 py-1">Size</th>
                    <th class="border px-2 py-1">Filetype</th>
                    <th class="border px-2 py-1"></th>
                    <th class="border px-2 py-1"></th>
                    <th class="border px-2 py-1"></th>
                    <?php if(empty($view_mode)): ?>
                        <th class="border px-2 py-1"></th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $today = strtotime('today');
                        $expiry = strtotime($doc->expiry_date);
                        $is_expired = $expiry < $today && $doc->alert;
                        $alert_from = $doc->notify_before ? date('Y-m-d', strtotime($doc->expiry_date . ' - ' . $doc->notify_before . ' days')) : $doc->expiry_date;
                        $is_warning = $doc->alert && strtotime($alert_from) <= $today && $today <= $expiry;
                    ?>
                    <tr class="<?php echo e($is_expired ? 'bg-red-100' : ($is_warning ? 'bg-yellow-100' : '')); ?>">
                        <td class="border px-2 py-1 text-center"><?php echo e($doc->id); ?></td>
                        <td class="border px-2 py-1"><?php echo e($doc->type_name); ?></td>
                        <td class="border px-2 py-1"><?php echo e($doc->description); ?></td>
                        <td class="border px-2 py-1"><?php echo e(date('d/m/Y', strtotime($doc->issue_date))); ?></td>
                        <td class="border px-2 py-1"><?php echo e(date('d/m/Y', strtotime($doc->expiry_date))); ?></td>
                        <td class="border px-2 py-1 text-center"><?php echo e($doc->alert ? 'Alert' : ''); ?></td>
                        <td class="border px-2 py-1"><?php echo e($doc->filename); ?></td>
                        <td class="border px-2 py-1"><?php echo e($doc->filesize); ?></td>
                        <td class="border px-2 py-1"><?php echo e($doc->filetype); ?></td>
                        <td class="border px-2 py-1 text-center">
                            <?php if(!empty($view_mode)): ?>
                                <a href="<?php echo e(route('hr.document-expiration')); ?>?EmpId=<?php echo e($doc->emp_id); ?>&DocId=<?php echo e($doc->id); ?>" class="text-blue-600 hover:text-blue-800 text-xs">Edit</a>
                            <?php else: ?>
                                <button type="submit" name="Edit<?php echo e($doc->id); ?>" value="<?php echo e($doc->id); ?>" class="text-blue-600 hover:text-blue-800 text-xs">Edit</button>
                            <?php endif; ?>
                        </td>
                        <td class="border px-2 py-1 text-center">
                            <button type="submit" name="view<?php echo e($doc->id); ?>" value="<?php echo e($doc->id); ?>" class="text-green-600 hover:text-green-800 text-xs">View</button>
                        </td>
                        <td class="border px-2 py-1 text-center">
                            <button type="submit" name="download<?php echo e($doc->id); ?>" value="<?php echo e($doc->id); ?>" class="text-purple-600 hover:text-purple-800 text-xs">Download</button>
                        </td>
                        <?php if(empty($view_mode)): ?>
                            <td class="border px-2 py-1 text-center">
                                <button type="submit" name="Delete<?php echo e($doc->id); ?>" value="<?php echo e($doc->id); ?>" class="text-red-600 hover:text-red-800 text-xs" onclick="return confirm('Delete?')">Delete</button>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td class="border px-2 py-1 text-center text-gray-500" colspan="<?php echo e(empty($view_mode) ? 13 : 12); ?>">No documents found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if(empty($view_mode)): ?>
        
        <div class="bg-white shadow rounded-lg p-4">
            <table class="table-auto border-collapse">
                <?php if($selected_id && $selected_id != -1): ?>
                    <tr>
                        <td class="p-1 text-right font-semibold">Document Number:</td>
                        <td class="p-1">&nbsp;&nbsp;<?php echo e($selected_id); ?></td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <td class="p-1 text-right font-semibold">Document type:</td>
                    <td class="p-1">
                        <select name="type_id" class="border px-2 py-1 min-w-[200px]">
                            <option value="">Select document type</option>
                            <?php $__currentLoopData = $doc_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($dt->type_id); ?>" <?php if(old('type_id', $edit_doc->type_id ?? '') == $dt->type_id): echo 'selected'; endif; ?>><?php echo e($dt->type_name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="p-1 text-right font-semibold">Document title:</td>
                    <td class="p-1"><input type="text" name="doc_title" value="<?php echo e(old('doc_title', $edit_doc->description ?? '')); ?>" size="40" class="border px-2 py-1"></td>
                </tr>
                <tr>
                    <td class="p-1 text-right font-semibold">Issue date:</td>
                    <td class="p-1"><input type="text" name="issue_date" value="<?php echo e(old('issue_date', isset($edit_doc) ? date('d/m/Y', strtotime($edit_doc->issue_date)) : date('d/m/Y'))); ?>" size="12" class="border px-2 py-1"></td>
                </tr>
                <tr>
                    <td class="p-1 text-right font-semibold">Expiry date:</td>
                    <td class="p-1"><input type="text" name="expiry_date" value="<?php echo e(old('expiry_date', isset($edit_doc) ? date('d/m/Y', strtotime($edit_doc->expiry_date)) : '')); ?>" size="12" class="border px-2 py-1"></td>
                </tr>
                <tr>
                    <td class="p-1 text-right font-semibold">Attached File:</td>
                    <td class="p-1"><input type="file" name="filename" class="border px-2 py-1"></td>
                </tr>
                <tr>
                    <td class="p-1 text-right font-semibold">Alert:</td>
                    <td class="p-1"><input type="checkbox" name="alert" value="1" <?php echo e(old('alert', $edit_doc->alert ?? false) ? 'checked' : ''); ?>></td>
                </tr>
            </table>
            <input type="hidden" name="selected_id" value="<?php echo e($selected_id ?: ''); ?>">
            <div class="text-center mt-4">
                <input type="submit" name="process" value="<?php echo e($selected_id && $selected_id != -1 ? 'Update' : 'Add'); ?>" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
            </div>
        </div>
    <?php endif; ?>

    <input type="hidden" name="View" value="<?php echo e($view_mode); ?>">
</form>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lupu\Desktop\laravel\fa-saas\resources\views/hr/document-expiration.blade.php ENDPATH**/ ?>