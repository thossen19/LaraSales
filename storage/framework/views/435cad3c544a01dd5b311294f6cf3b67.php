<?php $__env->startSection('title', 'Bank Account Deposit Entry - Sales ERP'); ?>
<?php $__env->startSection('content'); ?>
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Bank Account Deposit Entry</h2>
</div>

<?php if($message): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4"><?php echo e($message); ?></div>
<?php endif; ?>
<?php if($error): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?php echo e($error); ?></div>
<?php endif; ?>

<form method="POST" action="<?php echo e(route('banking.deposits')); ?>">
<?php echo csrf_field(); ?>
<div id="dep_header">
<table class="w-full bg-white shadow rounded-lg mb-6">
<tr>
<td class="p-4 align-top w-1/3">
    <table class="w-full">
        <tr>
            <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap">Date:</td>
            <td class="py-2">
                <input type="date" name="date_" value="<?php echo e(request('date_', $cart['tran_date'] ?? date('Y-m-d'))); ?>" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </td>
        </tr>
        <tr>
            <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap">Reference:</td>
            <td class="py-2">
                <input type="text" name="ref" value="<?php echo e(request('ref', $cart['reference'] ?? '')); ?>" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </td>
        </tr>
    </table>
</td>
<td class="p-4 align-top w-1/3">
    <table class="w-full">
        <tr>
            <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap">From:</td>
            <td class="py-2">
                <select name="PayType" onchange="this.form.submit()" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Select --</option>
                    <option value="customer" <?php echo e(request('PayType') == 'customer' ? 'selected' : ''); ?>>Customer</option>
                    <option value="supplier" <?php echo e(request('PayType') == 'supplier' ? 'selected' : ''); ?>>Supplier</option>
                    <option value="misc" <?php echo e(request('PayType') == 'misc' ? 'selected' : ''); ?>>Miscellaneous</option>
                    <option value="quick" <?php echo e(request('PayType') == 'quick' ? 'selected' : ''); ?>>Quick Entry</option>
                </select>
            </td>
        </tr>
        <?php $payType = request('PayType', ''); ?>
        <?php if($payType == 'customer'): ?>
        <tr>
            <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap">Customer:</td>
            <td class="py-2">
                <select name="person_id" onchange="this.form.submit()" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Select --</option>
                    <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($c->id); ?>" <?php echo e(request('person_id') == $c->id ? 'selected' : ''); ?>><?php echo e($c->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </td>
        </tr>
        <tr>
            <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap">Branch:</td>
            <td class="py-2">
                <select name="PersonDetailID" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Select --</option>
                    <?php
                        $custBranches = request('person_id') ? \DB::table('customer_branches')->where('customer_id', request('person_id'))->get() : collect();
                    ?>
                    <?php $__currentLoopData = $custBranches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($b->id); ?>" <?php echo e(request('PersonDetailID') == $b->id ? 'selected' : ''); ?>><?php echo e($b->branch_name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </td>
        </tr>
        <?php elseif($payType == 'supplier'): ?>
        <tr>
            <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap">Supplier:</td>
            <td class="py-2">
                <select name="person_id" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Select --</option>
                    <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($s->id); ?>" <?php echo e(request('person_id') == $s->id ? 'selected' : ''); ?>><?php echo e($s->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </td>
        </tr>
        <?php elseif($payType == 'misc'): ?>
        <tr>
            <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap">Name:</td>
            <td class="py-2">
                <input type="text" name="person_id" value="<?php echo e(request('person_id')); ?>" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 w-full">
            </td>
        </tr>
        <?php elseif($payType == 'quick'): ?>
        <tr>
            <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap">Type:</td>
            <td class="py-2">
                <select name="person_id" onchange="this.form.submit()" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Select --</option>
                    <?php $__currentLoopData = $quick_entries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $qe): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($qe->id); ?>" <?php echo e(request('person_id') == $qe->id ? 'selected' : ''); ?>><?php echo e($qe->description); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </td>
        </tr>
        <?php if(request('person_id')): ?>
        <?php
            $selQe = \DB::table('quick_entries')->where('id', request('person_id'))->first();
        ?>
        <tr>
            <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap"><?php echo e($selQe->base_desc ?? 'Amount'); ?>:</td>
            <td class="py-2">
                <input type="text" name="totamount" value="<?php echo e(request('totamount', $selQe->base_amount ?? 0)); ?>" class="border border-gray-300 rounded-md px-3 py-2 text-sm w-32 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <button type="submit" name="go" value="1" class="ml-2 px-3 py-2 bg-gray-200 text-gray-800 text-sm rounded-md hover:bg-gray-300">Go</button>
            </td>
        </tr>
        <?php endif; ?>
        <?php endif; ?>
    </table>
</td>
<td class="p-4 align-top w-1/3">
    <table class="w-full">
        <tr>
            <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap">Into:</td>
            <td class="py-2">
                <select name="bank_account" onchange="this.form.submit()" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Select --</option>
                    <?php $__currentLoopData = $bank_accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ba): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($ba->id); ?>" <?php echo e(request('bank_account') == $ba->id ? 'selected' : ''); ?>><?php echo e($ba->bank_account_name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </td>
        </tr>
        <?php
            $selBank = request('bank_account') ? \DB::table('bank_accounts')->where('id', request('bank_account'))->first() : null;
            $bankCurrency = $home_currency;
            if ($selBank) {
                $bankCurrency = $selBank->bank_curr_code ?: $home_currency;
            }
        ?>
        <?php if($selBank): ?>
        <tr>
            <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap">Bank Balance:</td>
            <td class="py-2 text-sm text-gray-700">0.00</td>
        </tr>
        <?php endif; ?>
        <tr>
            <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap">Exchange Rate:</td>
            <td class="py-2 text-sm text-gray-700">
                <?php if($bankCurrency != $home_currency): ?>
                    <?php echo e($home_currency); ?>/<?php echo e($bankCurrency); ?> 1.000000
                <?php else: ?>
                    1.000000
                <?php endif; ?>
            </td>
        </tr>
    </table>
</td>
</tr>
</table>
</div>

<div class="bg-white shadow rounded-lg p-4 mb-6">
    <h3 class="text-lg font-medium text-gray-800 mb-3">Deposit Items</h3>
    <table class="w-full border-collapse" id="items_table">
        <thead>
            <tr class="bg-gray-50">
                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Account Code</th>
                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Account Description</th>
                <?php if($use_dimension >= 1): ?>
                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Dimension 1</th>
                <?php endif; ?>
                <?php if($use_dimension >= 2): ?>
                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Dimension 2</th>
                <?php endif; ?>
                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Memo</th>
                <?php if(count($cart['gl_items'] ?? []) > 0): ?>
                <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase" colspan="2">Actions</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            <?php $totalAmt = 0; ?>
            <?php $__currentLoopData = $cart['gl_items'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $acc = \DB::table('accounts')->where('code', $item['code_id'])->first();
                    $accName = $acc->name ?? $item['code_id'];
                    $totalAmt += $item['amount'];
                    $displayAmt = -$item['amount']; // negate for display (deposit stores negative)
                ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-3 py-2 text-sm"><?php echo e($item['code_id']); ?></td>
                    <td class="px-3 py-2 text-sm"><?php echo e($accName); ?></td>
                    <?php if($use_dimension >= 1): ?>
                    <td class="px-3 py-2 text-sm"><?php echo e($item['dimension_id'] ? \DB::table('dimensions')->where('id', $item['dimension_id'])->value('name') : ''); ?></td>
                    <?php endif; ?>
                    <?php if($use_dimension >= 2): ?>
                    <td class="px-3 py-2 text-sm"><?php echo e($item['dimension2_id'] ? \DB::table('dimensions')->where('id', $item['dimension2_id'])->value('name') : ''); ?></td>
                    <?php endif; ?>
                    <td class="px-3 py-2 text-sm text-right"><?php echo e(number_format($displayAmt, 2)); ?></td>
                    <td class="px-3 py-2 text-sm"><?php echo e($item['memo']); ?></td>
                    <?php if(count($cart['gl_items'] ?? []) > 0): ?>
                    <td class="px-3 py-2 text-center">
                        <button type="submit" name="Edit" value="<?php echo e($idx); ?>" class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</button>
                    </td>
                    <td class="px-3 py-2 text-center">
                        <button type="submit" name="Delete" value="<?php echo e($idx); ?>" class="text-red-600 hover:text-red-900 text-sm">Delete</button>
                    </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php
                $isEditing = $edit_index !== null && isset($cart['gl_items'][$edit_index]);
                $editItem = $isEditing ? $cart['gl_items'][$edit_index] : null;
                $editCodeId = request('code_id', $editItem['code_id'] ?? '');
                $editDimId = request('dimension_id', $editItem['dimension_id'] ?? '');
                $editDim2Id = request('dimension2_id', $editItem['dimension2_id'] ?? '');
                $editAmt = request('amount', $editItem ? number_format(abs($editItem['amount']), 2) : '');
                $editMemo = request('LineMemo', $editItem['memo'] ?? '');
                $colspan = 2;
                if ($use_dimension >= 1) $colspan += 1;
                if ($use_dimension >= 2) $colspan += 1;
                $colspan += 1; // Amount
                $colspan += 1; // Memo
            ?>
            <?php if($isEditing): ?>
                <tr class="bg-yellow-50">
                    <td colspan="<?php echo e($colspan); ?>" class="px-3 py-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <select name="code_id" class="border border-gray-300 rounded-md px-2 py-1 text-sm max-w-xs">
                                <option value="">-- Select --</option>
                                <?php $__currentLoopData = $gl_accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ga): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($ga->code); ?>" <?php echo e($editCodeId == $ga->code ? 'selected' : ''); ?>><?php echo e($ga->code); ?> <?php echo e($ga->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php if($use_dimension >= 1): ?>
                            <select name="dimension_id" class="border border-gray-300 rounded-md px-2 py-1 text-sm max-w-[140px]">
                                <option value="">-- None --</option>
                                <?php $__currentLoopData = $dimensions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($d->id); ?>" <?php echo e($editDimId == $d->id ? 'selected' : ''); ?>><?php echo e($d->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php endif; ?>
                            <?php if($use_dimension >= 2): ?>
                            <select name="dimension2_id" class="border border-gray-300 rounded-md px-2 py-1 text-sm max-w-[140px]">
                                <option value="">-- None --</option>
                                <?php $__currentLoopData = $dimensions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($d->id); ?>" <?php echo e($editDim2Id == $d->id ? 'selected' : ''); ?>><?php echo e($d->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php endif; ?>
                            <input type="text" name="amount" value="<?php echo e($editAmt); ?>" placeholder="Amount" class="border border-gray-300 rounded-md px-2 py-1 text-sm w-24">
                            <input type="text" name="LineMemo" value="<?php echo e($editMemo); ?>" placeholder="Memo" class="border border-gray-300 rounded-md px-2 py-1 text-sm w-40">
                            <button type="submit" name="UpdateItem" value="1" class="px-3 py-1 bg-indigo-600 text-white text-sm rounded-md">Update</button>
                            <button type="submit" name="CancelItemChanges" value="1" class="px-3 py-1 bg-gray-200 text-gray-800 text-sm rounded-md">Cancel</button>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <tr class="bg-gray-50">
                    <td colspan="<?php echo e($colspan); ?>" class="px-3 py-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <select name="code_id" class="border border-gray-300 rounded-md px-2 py-1 text-sm max-w-xs">
                                <option value="">-- Select --</option>
                                <?php $__currentLoopData = $gl_accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ga): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($ga->code); ?>" <?php echo e(request('code_id') == $ga->code ? 'selected' : ''); ?>><?php echo e($ga->code); ?> <?php echo e($ga->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php if($use_dimension >= 1): ?>
                            <select name="dimension_id" class="border border-gray-300 rounded-md px-2 py-1 text-sm max-w-[140px]">
                                <option value="">-- None --</option>
                                <?php $__currentLoopData = $dimensions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($d->id); ?>" <?php echo e(request('dimension_id') == $d->id ? 'selected' : ''); ?>><?php echo e($d->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php endif; ?>
                            <?php if($use_dimension >= 2): ?>
                            <select name="dimension2_id" class="border border-gray-300 rounded-md px-2 py-1 text-sm max-w-[140px]">
                                <option value="">-- None --</option>
                                <?php $__currentLoopData = $dimensions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($d->id); ?>" <?php echo e(request('dimension2_id') == $d->id ? 'selected' : ''); ?>><?php echo e($d->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php endif; ?>
                            <input type="text" name="amount" value="<?php echo e(request('amount')); ?>" placeholder="Amount" class="border border-gray-300 rounded-md px-2 py-1 text-sm w-24">
                            <input type="text" name="LineMemo" value="<?php echo e(request('LineMemo')); ?>" placeholder="Memo" class="border border-gray-300 rounded-md px-2 py-1 text-sm w-40">
                            <button type="submit" name="AddItem" value="1" class="px-3 py-1 bg-indigo-600 text-white text-sm rounded-md">Add Item</button>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
            <?php if(count($cart['gl_items'] ?? []) > 0): ?>
                <?php
                    $totalColspan = $colspan - 2;
                    if ($totalColspan < 1) $totalColspan = 1;
                ?>
                <tr class="font-semibold bg-gray-50">
                    <td colspan="<?php echo e($totalColspan); ?>" class="px-3 py-2 text-right text-sm">Total</td>
                    <td class="px-3 py-2 text-right text-sm"><?php echo e(number_format(abs($totalAmt), 2)); ?></td>
                    <?php if(count($cart['gl_items'] ?? []) > 0): ?>
                    <td class="px-3 py-2"></td>
                    <td class="px-3 py-2"></td>
                    <?php endif; ?>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div id="footer" class="bg-white shadow rounded-lg p-4 mb-6">
    <table class="w-full">
        <?php
            $showSettled = in_array(request('PayType'), ['customer', 'supplier']) && request('person_id');
        ?>
        <?php if($showSettled): ?>
        <tr>
            <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap w-40">Settled <?php echo e(request('PayType') == 'customer' ? 'AR' : 'AP'); ?> Amount:</td>
            <td class="py-2">
                <input type="text" name="settled_amount" value="<?php echo e(request('settled_amount', number_format(abs($totalAmt), 2))); ?>" class="border border-gray-300 rounded-md px-3 py-2 text-sm w-40 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <span class="ml-2 text-sm text-gray-500"><?php echo e($bankCurrency); ?></span>
            </td>
        </tr>
        <?php endif; ?>
        <tr>
            <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap align-top">Memo:</td>
            <td class="py-2">
                <textarea name="memo_" rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"><?php echo e(request('memo_', $cart['memo_'] ?? '')); ?></textarea>
            </td>
        </tr>
    </table>
</div>

<div class="text-center space-x-4">
    <button type="submit" name="Update" value="1" class="px-6 py-2 bg-gray-200 text-gray-800 font-medium rounded-md hover:bg-gray-300 transition">Update</button>
    <button type="submit" name="Process" value="1" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition">Process Deposit</button>
</div>

</form>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lupu\Desktop\laravel\fa-saas\resources\views/banking/deposits.blade.php ENDPATH**/ ?>