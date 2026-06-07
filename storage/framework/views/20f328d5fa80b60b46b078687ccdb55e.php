<?php $__env->startSection('title', 'Bank Accounts - Sales ERP'); ?>
<?php $__env->startSection('content'); ?>
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Bank Accounts</h2>
    <p class="mt-2 text-gray-600">Manage bank accounts.</p>
</div>

<?php if($message): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4"><?php echo e($message); ?></div>
<?php endif; ?>
<?php if($error): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?php echo e($error); ?></div>
<?php endif; ?>

<form method="GET" action="<?php echo e(route('banking.accounts')); ?>" class="mb-4">
    <label class="flex items-center text-sm text-gray-700 cursor-pointer">
        <input type="checkbox" name="show_inactive" value="1" <?php echo e($show_inactive ? 'checked' : ''); ?> onchange="this.form.submit()" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
        <span class="ml-2">Show also inactive</span>
    </label>
</form>

<div class="bg-white shadow rounded-lg overflow-hidden mb-6">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Account Name</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Currency</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">GL Account</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bank</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Number</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bank Address</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Dflt</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Inactive</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase" colspan="2">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php $__empty_1 = true; $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap font-medium"><?php echo e($a->bank_account_name); ?></td>
                    <td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap"><?php echo e($account_types[$a->account_type] ?? $a->account_type); ?></td>
                    <td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap"><?php echo e($a->bank_curr_code); ?></td>
                    <td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap"><?php echo e($a->account_code); ?> <?php echo e($a->glAccount->name ?? ''); ?></td>
                    <td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap"><?php echo e($a->bank_name); ?></td>
                    <td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap"><?php echo e($a->bank_account_number); ?></td>
                    <td class="px-4 py-3 text-sm text-gray-600 max-w-[200px] truncate"><?php echo e($a->bank_address); ?></td>
                    <td class="px-4 py-3 text-center text-sm <?php echo e($a->dflt_curr_act ? 'text-green-600 font-medium' : 'text-gray-400'); ?>"><?php echo e($a->dflt_curr_act ? 'Yes' : 'No'); ?></td>
                    <td class="px-4 py-3 text-center">
                        <a href="<?php echo e(route('banking.accounts', ['toggle_inactive' => $a->id, 'show_inactive' => $show_inactive ? '1' : null])); ?>" class="text-sm <?php echo e($a->inactive ? 'text-red-600' : 'text-green-600'); ?> hover:underline"><?php echo e($a->inactive ? 'Yes' : 'No'); ?></a>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <a href="<?php echo e(route('banking.accounts', ['bank_id' => $a->id, 'selected_id' => $a->id, 'Mode' => 'Edit', 'show_inactive' => $show_inactive ? '1' : null])); ?>" class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</a>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <a href="<?php echo e(route('banking.accounts', ['Mode' => 'Delete', 'selected_id' => $a->id, 'show_inactive' => $show_inactive ? '1' : null])); ?>" class="text-red-600 hover:text-red-900 text-sm" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="11" class="px-4 py-8 text-center text-gray-500">No bank accounts defined.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if($selected_id && $edit_acct): ?>
    <div class="text-lg font-medium text-gray-800 mb-4">
        <?php echo e($edit_acct->bank_account_name); ?> - <?php echo e($edit_acct->bank_curr_code); ?>

    </div>

    <div x-data="{ tab: '<?php echo e($selected_tab); ?>' }">
        <div class="border-b border-gray-200 mb-6">
            <nav class="-mb-px flex space-x-6">
                <button @click="tab = 'settings'" :class="{ 'border-indigo-500 text-indigo-600': tab === 'settings', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'settings' }" class="whitespace-nowrap pb-3 px-1 border-b-2 font-medium text-sm">General settings</button>
                <button @click="tab = 'transactions'" :class="{ 'border-indigo-500 text-indigo-600': tab === 'transactions', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'transactions' }" class="whitespace-nowrap pb-3 px-1 border-b-2 font-medium text-sm">Transactions</button>
                <button @click="tab = 'attachments'" :class="{ 'border-indigo-500 text-indigo-600': tab === 'attachments', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'attachments' }" class="whitespace-nowrap pb-3 px-1 border-b-2 font-medium text-sm">Attachments</button>
            </nav>
        </div>

        <div x-show="tab === 'settings'">
            <form method="POST" action="<?php echo e(route('banking.accounts', ['bank_id' => $edit_acct->id, 'selected_id' => $edit_acct->id, 'Mode' => 'UPDATE_ITEM'])); ?>">
                <?php echo csrf_field(); ?>
                <?php $is_used = \DB::table('bank_trans')->where('bank_act', $edit_acct->id)->exists(); ?>
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="space-y-4 max-w-lg">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Bank Account Name:</label>
                            <input type="text" name="bank_account_name" value="<?php echo e(old('bank_account_name', $edit_acct->bank_account_name)); ?>" maxlength="100" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Account Type:</label>
                            <?php if($is_used): ?>
                                <p class="py-2 text-sm text-gray-800"><?php echo e($account_types[$edit_acct->account_type] ?? $edit_acct->account_type); ?></p>
                                <input type="hidden" name="account_type" value="<?php echo e($edit_acct->account_type); ?>">
                            <?php else: ?>
                                <select name="account_type" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                                    <?php $__currentLoopData = $account_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($val); ?>" <?php echo e($edit_acct->account_type == $val ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            <?php endif; ?>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Bank Account Currency:</label>
                            <?php if($is_used): ?>
                                <p class="py-2 text-sm text-gray-800"><?php echo e($edit_acct->bank_curr_code); ?></p>
                                <input type="hidden" name="BankAccountCurrency" value="<?php echo e($edit_acct->bank_curr_code); ?>">
                            <?php else: ?>
                                <select name="BankAccountCurrency" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                                    <?php $__currentLoopData = $currencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($code); ?>" <?php echo e($edit_acct->bank_curr_code == $code ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            <?php endif; ?>
                        </div>
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="dflt_curr_act" value="1" <?php echo e($edit_acct->dflt_curr_act ? 'checked' : ''); ?> class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700">Default currency account</span>
                            </label>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Bank Account GL Code:</label>
                            <?php if($is_used): ?>
                                <p class="py-2 text-sm text-gray-800"><?php echo e($edit_acct->account_code); ?></p>
                                <input type="hidden" name="account_code" value="<?php echo e($edit_acct->account_code); ?>">
                            <?php else: ?>
                                <select name="account_code" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                                    <?php $__currentLoopData = $glAccounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $acc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($acc->code); ?>" <?php echo e($edit_acct->account_code == $acc->code ? 'selected' : ''); ?>><?php echo e($acc->code); ?> <?php echo e($acc->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            <?php endif; ?>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Bank Charges Account:</label>
                            <select name="bank_charge_act" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                                <option value="">-- None --</option>
                                <?php $__currentLoopData = $allAccounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $acc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($acc->code); ?>" <?php echo e(($edit_acct->bank_charge_act ?: '') == $acc->code ? 'selected' : ''); ?>><?php echo e($acc->code); ?> <?php echo e($acc->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Bank Name:</label>
                            <input type="text" name="bank_name" value="<?php echo e(old('bank_name', $edit_acct->bank_name)); ?>" maxlength="60" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Bank Account Number:</label>
                            <input type="text" name="bank_account_number" value="<?php echo e(old('bank_account_number', $edit_acct->bank_account_number)); ?>" maxlength="60" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Bank Address:</label>
                            <textarea name="bank_address" rows="5" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"><?php echo e(old('bank_address', $edit_acct->bank_address)); ?></textarea>
                        </div>
                    </div>
                    <div class="pt-4 mt-4 border-t border-gray-200">
                        <button type="submit" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition">Update</button>
                    </div>
                </div>
            </form>
        </div>

        <div x-show="tab === 'transactions'">
            <div class="bg-white shadow rounded-lg p-6">
                <div class="text-center py-12 text-gray-500">
                    <i class="fas fa-exchange-alt text-6xl mb-4"></i>
                    <p class="text-lg">Bank transactions for this account will appear here.</p>
                </div>
            </div>
        </div>

        <div x-show="tab === 'attachments'">
            <div class="bg-white shadow rounded-lg p-6">
                <div class="text-center py-12 text-gray-500">
                    <i class="fas fa-paperclip text-6xl mb-4"></i>
                    <p class="text-lg">Attachments will appear here.</p>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Add New Bank Account</h3>

        <form method="POST" action="<?php echo e(route('banking.accounts', ['Mode' => 'ADD_ITEM'])); ?>">
            <?php echo csrf_field(); ?>

            <div class="space-y-4 max-w-lg">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bank Account Name:</label>
                    <input type="text" name="bank_account_name" maxlength="100" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Account Type:</label>
                    <select name="account_type" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                        <?php $__currentLoopData = $account_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($val); ?>" <?php echo e($val == 0 ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bank Account Currency:</label>
                    <select name="BankAccountCurrency" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                        <?php $__currentLoopData = $currencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($code); ?>" <?php echo e($code == 'USD' ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="dflt_curr_act" value="1" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700">Default currency account</span>
                    </label>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bank Account GL Code:</label>
                    <select name="account_code" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                        <option value="">-- Select --</option>
                        <?php $__currentLoopData = $glAccounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $acc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($acc->code); ?>"><?php echo e($acc->code); ?> <?php echo e($acc->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bank Charges Account:</label>
                    <select name="bank_charge_act" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                        <option value="">-- None --</option>
                        <?php $__currentLoopData = $allAccounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $acc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($acc->code); ?>"><?php echo e($acc->code); ?> <?php echo e($acc->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bank Name:</label>
                    <input type="text" name="bank_name" maxlength="60" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bank Account Number:</label>
                    <input type="text" name="bank_account_number" maxlength="60" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bank Address:</label>
                    <textarea name="bank_address" rows="5" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>
            </div>

            <div class="pt-4 mt-4 border-t border-gray-200">
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition">Add New</button>
            </div>
        </form>
    </div>
<?php endif; ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lupu\Desktop\laravel\fa-saas\resources\views/banking/accounts.blade.php ENDPATH**/ ?>