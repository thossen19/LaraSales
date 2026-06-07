<?php $__env->startSection('title', 'Manage Employees'); ?>
<?php $__env->startSection('content'); ?>
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Manage Employees</h2>
</div>

<?php if($msg): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4"><?php echo e($msg); ?></div>
<?php endif; ?>
<?php if($error): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?php echo e($error); ?></div>
<?php endif; ?>
<?php if($upload_error): ?>
    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4"><?php echo e($upload_error); ?></div>
<?php endif; ?>

<form method="POST" action="<?php echo e(route('hr.employees.index')); ?>" enctype="multipart/form-data">
<?php echo csrf_field(); ?>

<div class="bg-white shadow rounded-lg mb-6">
    <div class="border-b border-gray-200">
        <nav class="flex">
            <button type="submit" name="_tabs_sel" value="list"
                class="px-6 py-3 text-sm font-medium <?php echo e($tab == 'list' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-500 hover:text-gray-700'); ?>">
                Employees List
            </button>
            <button type="submit" name="_tabs_sel" value="add"
                class="px-6 py-3 text-sm font-medium <?php echo e($tab == 'add' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-500 hover:text-gray-700'); ?>">
                Add/Edit Employee
            </button>
        </nav>
    </div>

    <div class="p-6">
        <?php if($tab == 'list'): ?>
        <div class="mb-4 flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Enter Search String:</label>
                <input type="text" name="string" value="<?php echo e($search_string); ?>" placeholder="Enter fragment or leave empty"
                    class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">&nbsp;</label>
                <select name="DeptId"
                    class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">All departments</option>
                    <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($d->dept_id); ?>" <?php echo e($dept_filter == $d->dept_id ? 'selected' : ''); ?>><?php echo e($d->dept_name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">&nbsp;</label>
                <select name="position"
                    class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Positions</option>
                    <?php $__currentLoopData = $positions_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($p->position_id); ?>" <?php echo e($position_filter == $p->position_id ? 'selected' : ''); ?>><?php echo e($p->position_name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <?php if($grades_no > 0): ?>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">&nbsp;</label>
                <select name="grade"
                    class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Grades</option>
                    <?php for($i = 1; $i <= $grades_no; $i++): ?>
                    <option value="<?php echo e($i); ?>" <?php echo e($grade_filter == $i ? 'selected' : ''); ?>><?php echo e($i); ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <?php endif; ?>
            <div class="flex items-center">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="show_inactive" value="1" <?php echo e($show_inactive ? 'checked' : ''); ?>

                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="ml-2 text-sm text-gray-700">Show resigned:</span>
                </label>
            </div>
            <div>
                <button type="submit" name="Search" value="1"
                    class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
                    Search
                </button>
            </div>
        </div>

        <?php if($employees->count() > 0): ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Gender</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mobile</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Birth</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hired Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-gray-50 <?php echo e(!$e->is_active ? 'text-gray-400' : ''); ?>">
                        <td class="px-4 py-2 text-sm">
                            <button type="submit" name="<?php echo e($e->id); ?>" value="1" class="text-indigo-600 hover:text-indigo-900"><?php echo e($e->id); ?></button>
                        </td>
                        <td class="px-4 py-2 text-sm">
                            <button type="submit" name="<?php echo e($e->id); ?>" value="1" class="text-indigo-600 hover:text-indigo-900 font-medium">
                                <?php echo e($e->first_name); ?> <?php echo e($e->last_name); ?>

                            </button>
                        </td>
                        <td class="px-4 py-2 text-sm">
                            <?php if($e->gender == 0): ?> Female <?php elseif($e->gender == 1): ?> Male <?php else: ?> Other <?php endif; ?>
                        </td>
                        <td class="px-4 py-2 text-sm"><?php echo e($e->phone); ?></td>
                        <td class="px-4 py-2 text-sm"><?php echo e($e->email); ?></td>
                        <td class="px-4 py-2 text-sm"><?php echo e($e->birth_date ? date('d/m/Y', strtotime($e->birth_date)) : ''); ?></td>
                        <td class="px-4 py-2 text-sm"><?php echo e($e->hire_date && $e->hire_date != '0000-00-00' ? date('d/m/Y', strtotime($e->hire_date)) : 'Not hired'); ?></td>
                        <td class="px-4 py-2 text-sm"><?php echo e($e->dept_name ?? ($e->department ?? 'Not selected')); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            <?php echo e($employees->appends(request()->all())->links()); ?>

        </div>
        <?php else: ?>
        <div class="text-center py-8 text-gray-500">No employee defined.</div>
        <?php endif; ?>

        <?php else: ?> 

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Image File:</label>
                    <input type="file" name="pic" accept="image/*"
                        class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                </div>
                <div class="mb-4">
                    <?php
                        $img_path = $cur_id ? 'storage/employee_photos/' . $cur_id . '.jpg' : '';
                    ?>
                    <?php if($cur_id && file_exists(public_path($img_path))): ?>
                    <img src="<?php echo e(asset($img_path)); ?>" alt="<?php echo e($cur_id); ?>.jpg" height="100" class="mb-2">
                    <label class="inline-flex items-center text-sm">
                        <input type="checkbox" name="del_image" value="1" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-gray-700">Delete Image:</span>
                    </label>
                    <?php else: ?>
                    <img src="https://ui-avatars.com/api/?name=<?php echo e(urlencode(($employee->first_name ?? 'N') . '+' . ($employee->last_name ?? 'A'))); ?>&size=100&background=6366f1&color=fff" alt="avatar" height="100">
                    <?php endif; ?>
                </div>

                <h3 class="text-base font-semibold text-gray-800 mb-3 pb-2 border-b border-gray-200">Personal Information</h3>

                <?php if($cur_id): ?>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700">Employee Id:</label>
                    <span class="text-sm text-gray-900"><?php echo e($cur_id); ?></span>
                </div>
                <?php endif; ?>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">First Name:</label>
                    <input type="text" name="emp_first_name" value="<?php echo e(old('emp_first_name', $employee->first_name ?? '')); ?>" maxlength="50" size="35"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Last Name:</label>
                    <input type="text" name="emp_last_name" value="<?php echo e(old('emp_last_name', $employee->last_name ?? '')); ?>" maxlength="50" size="35"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gender:</label>
                    <div class="flex gap-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="gender" value="0" <?php echo e(old('gender', $employee->gender ?? '1') == '0' ? 'checked' : ''); ?>

                                class="border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-700">Female</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="gender" value="1" <?php echo e(old('gender', $employee->gender ?? '1') == '1' ? 'checked' : ''); ?>

                                class="border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-700">Male</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="gender" value="2" <?php echo e(old('gender', $employee->gender ?? '1') == '2' ? 'checked' : ''); ?>

                                class="border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-700">Other</span>
                        </label>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address:</label>
                    <textarea name="emp_address" rows="5" cols="31"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"><?php echo e(old('emp_address', $employee->address ?? '')); ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mobile:</label>
                    <input type="text" name="emp_mobile" value="<?php echo e(old('emp_mobile', $employee->phone ?? '')); ?>" maxlength="30" size="35"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">e-Mail:</label>
                    <input type="email" name="emp_email" value="<?php echo e(old('emp_email', $employee->email ?? '')); ?>" maxlength="100" size="35"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Birth Date:</label>
                    <input type="text" name="emp_birthdate" value="<?php echo e(old('emp_birthdate', $employee && $employee->birth_date ? date('d/m/Y', strtotime($employee->birth_date)) : '')); ?>" placeholder="dd/mm/yyyy"
                        class="w-40 border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            
            <div>
                <h3 class="text-base font-semibold text-gray-800 mb-3 pb-2 border-b border-gray-200">Personal Information</h3>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">National ID:</label>
                    <input type="text" name="national_id" value="<?php echo e(old('national_id', $employee->national_id ?? '')); ?>" maxlength="50" size="35"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Passport:</label>
                    <input type="text" name="passport" value="<?php echo e(old('passport', $employee->passport ?? '')); ?>" maxlength="50" size="35"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bank Name/Account:</label>
                    <input type="text" name="bank_account" value="<?php echo e(old('bank_account', $employee->bank_account ?? '')); ?>" maxlength="50" size="35"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tax ID Number:</label>
                    <input type="text" name="tax_number" value="<?php echo e(old('tax_number', $employee->tax_id ?? '')); ?>" maxlength="50" size="35"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <h3 class="text-base font-semibold text-gray-800 mb-3 pb-2 border-b border-gray-200 mt-6">Job Information</h3>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes:</label>
                    <textarea name="emp_notes" rows="5" cols="31"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"><?php echo e(old('emp_notes', $employee->notes ?? '')); ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hire Date:</label>
                    <input type="text" name="emp_hiredate" value="<?php echo e(old('emp_hiredate', $employee && $employee->hire_date && $employee->hire_date != '0000-00-00' ? date('d/m/Y', strtotime($employee->hire_date)) : '')); ?>" placeholder="dd/mm/yyyy"
                        class="w-40 border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Department:</label>
                    <?php if($cur_id && $employee && $employee->hire_date && $employee->hire_date != '0000-00-00'): ?>
                    <select name="department_id"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Not selected</option>
                        <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($d->dept_id); ?>" <?php echo e(old('department_id', $employee->department_id ?? '') == $d->dept_id ? 'selected' : ''); ?>><?php echo e($d->dept_name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php elseif($cur_id): ?>
                    <span class="text-sm text-gray-500">Set hire date first</span>
                    <input type="hidden" name="department_id" value="">
                    <?php else: ?>
                    <select name="department_id"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Not selected</option>
                        <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($d->dept_id); ?>" <?php echo e(old('department_id', $employee->department_id ?? '') == $d->dept_id ? 'selected' : ''); ?>><?php echo e($d->dept_name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Job Position:</label>
                    <select name="position_id"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Not selected</option>
                        <?php $__currentLoopData = $positions_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($p->position_id); ?>" <?php echo e(old('position_id', $employee->position_id ?? '') == $p->position_id ? 'selected' : ''); ?>><?php echo e($p->position_name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Salary Grade:</label>
                    <select name="grade_id"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Basic</option>
                        <?php for($i = 1; $i <= $grades_no; $i++): ?>
                        <option value="<?php echo e($i); ?>" <?php echo e(old('grade_id', $employee->grade_id ?? '') == $i ? 'selected' : ''); ?>><?php echo e($i); ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <?php if($cur_id): ?>
                <div class="mb-3">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="inactive" value="1" <?php echo e(old('inactive', $employee && !$employee->is_active ? true : false) ? 'checked' : ''); ?>

                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700">Resigned:</span>
                    </label>
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Release Date:</label>
                    <input type="text" name="emp_releasedate" value="<?php echo e(old('emp_releasedate', $employee && $employee->termination_date && $employee->termination_date != '0000-00-00' ? date('d/m/Y', strtotime($employee->termination_date)) : '')); ?>" placeholder="dd/mm/yyyy"
                        class="w-40 border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <?php else: ?>
                <input type="hidden" name="inactive" value="0">
                <input type="hidden" name="emp_releasedate" value="">
                <?php endif; ?>
            </div>

            
            <div>
                <h3 class="text-base font-semibold text-gray-800 mb-3 pb-2 border-b border-gray-200">Pay Elements</h3>

                <div class="text-xs text-gray-500 mb-3 italic" title="Enter negative amount for deduction, positive for earning">(?)</div>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Use Personal Salary Structure:</label>
                    <select name="personal_salary"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="0" <?php echo e(old('personal_salary', $employee->personal_salary ?? false) ? '' : 'selected'); ?>>No</option>
                        <option value="1" <?php echo e(old('personal_salary', $employee->personal_salary ?? false) ? 'selected' : ''); ?>>Yes</option>
                    </select>
                </div>

                <?php
                    $basic_sal = $personal_salaries->first(function($v) { return $v->is_basic; });
                    $basic_amt_val = $basic_sal ? $basic_sal->pay_amount : 0;
                ?>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Basic Salary Amount:</label>
                    <input type="text" name="basic_amt" value="<?php echo e(old('basic_amt', number_format($basic_amt_val, 2))); ?>"
                        class="w-40 border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <?php $__currentLoopData = $pay_elements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $el): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $amt_val = old('amt_' . $el->account_code, isset($personal_salaries[$el->account_code]) ? $personal_salaries[$el->account_code]->pay_amount : 0);
                ?>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1"><?php echo e($el->element_name); ?>:</label>
                    <input type="text" name="amt_<?php echo e($el->account_code); ?>" value="<?php echo e(old('amt_' . $el->account_code, number_format($amt_val, 2))); ?>"
                        class="w-40 border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        <div class="mt-6 pt-4 border-t border-gray-200 flex items-center gap-4">
            <?php if($cur_id): ?>
            <button type="submit" name="addupdate" value="1"
                class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                Update Employee
            </button>
            <a href="<?php echo e(route('hr.employees.index')); ?>?tab=list"
                class="px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-md hover:bg-gray-50 transition duration-150">
                Cancel
            </a>
            <button type="submit" name="delete" value="1"
                class="px-4 py-2 border border-red-300 text-red-700 font-medium rounded-md hover:bg-red-50 transition duration-150"
                onclick="return confirm('Delete this employee?')">
                Delete Employee
            </button>
            <?php else: ?>
            <button type="submit" name="addupdate" value="1"
                class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                Add New Employee Details
            </button>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lupu\Desktop\laravel\fa-saas\resources\views/hr/employees/index.blade.php ENDPATH**/ ?>