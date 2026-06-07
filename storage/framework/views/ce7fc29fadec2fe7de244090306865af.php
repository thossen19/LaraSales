<?php $__env->startSection('title', 'Payment Advice'); ?>
<?php $__env->startSection('content'); ?>
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Payment Advice</h2>
</div>

<?php if($msg): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4"><?php echo $msg; ?></div>
<?php endif; ?>
<?php if($error): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?php echo $error; ?></div>
<?php endif; ?>

<form method="post" action="<?php echo e(url()->current()); ?>" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>

    
    <table class="table-auto border-collapse bg-white shadow rounded-lg mb-4">
        <tr>
            <td class="border px-3 py-2 w-1/3">
                <table class="w-full">
                    <tr>
                        <td class="p-1 text-right font-semibold w-1/3">Bank Account:</td>
                        <td class="p-1">
                            <select name="bank_account_id" class="border px-2 py-1 min-w-[200px]">
                                <option value="">Select Bank Account</option>
                                <?php $__currentLoopData = $bank_accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ba): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($ba->id); ?>" <?php if($cart['bank_account_id'] == $ba->id): echo 'selected'; endif; ?>><?php echo e($ba->bank_account_name); ?> (<?php echo e($ba->bank_curr_code); ?>)</option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </td>
                    </tr>
                </table>
            </td>
            <td class="border px-3 py-2 w-2/3">
                <table class="w-full">
                    <tr>
                        <td class="p-1 text-right font-semibold">Date:</td>
                        <td class="p-1"><input type="text" name="date_" value="<?php echo e($cart['pay_date']); ?>" size="12" class="border px-2 py-1"></td>
                        <td class="p-1 text-right font-semibold">Reference:</td>
                        <td class="p-1"><input type="text" name="ref" value="<?php echo e($cart['ref']); ?>" size="15" class="border px-2 py-1"></td>
                        <td class="p-1 text-right font-semibold">Memo:</td>
                        <td class="p-1"><input type="text" name="memo_" value="<?php echo e($cart['memo_']); ?>" size="20" class="border px-2 py-1"></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    
    <?php if($unpaid_payslips->isNotEmpty()): ?>
        <div class="bg-white shadow rounded-lg mb-4 p-2">
            <table class="table-auto w-full text-sm">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border px-2 py-1">Payslip No</th>
                        <th class="border px-2 py-1">Employee</th>
                        <th class="border px-2 py-1">Date</th>
                        <th class="border px-2 py-1">From</th>
                        <th class="border px-2 py-1">To</th>
                        <th class="border px-2 py-1 text-right">Amount</th>
                        <th class="border px-2 py-1 text-center"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $unpaid_payslips; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ps): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="<?php echo e($loop->even ? 'bg-gray-50' : ''); ?> <?php echo e($cart['payslip_no'] == $ps->payslip_no ? 'bg-blue-100' : ''); ?>">
                            <td class="border px-2 py-1"><?php echo e($ps->payslip_no); ?></td>
                            <td class="border px-2 py-1"><?php echo e($ps->emp_name); ?></td>
                            <td class="border px-2 py-1"><?php echo e($ps->generated_date); ?></td>
                            <td class="border px-2 py-1"><?php echo e($ps->from_date); ?></td>
                            <td class="border px-2 py-1"><?php echo e($ps->to_date); ?></td>
                            <td class="border px-2 py-1 text-right"><?php echo e(number_format($ps->payable_amount, 2)); ?></td>
                            <td class="border px-2 py-1 text-center">
                                <button type="submit" name="SelectPayslip" value="<?php echo e($ps->payslip_no); ?>" class="text-blue-600 hover:text-blue-800 text-xs <?php echo e($cart['payslip_no'] == $ps->payslip_no ? 'font-bold underline' : ''); ?>"><?php echo e($cart['payslip_no'] == $ps->payslip_no ? 'Selected' : 'Select'); ?></button>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="bg-white shadow rounded-lg mb-4 p-4 text-center text-gray-500">No unpaid payslips available.</div>
    <?php endif; ?>

    
    <?php if($cart['payslip_no'] > 0): ?>
        <table class="table-auto border-collapse bg-white shadow rounded-lg mb-4">
            <tr>
                <td class="border px-3 py-2">
                    <table class="w-full">
                        <tr>
                            <td class="p-1 text-right font-semibold w-1/4">Payslip #:</td>
                            <td class="p-1 font-bold"><?php echo e($cart['payslip_no']); ?></td>
                            <td class="p-1 text-right font-semibold w-1/4">Employee:</td>
                            <td class="p-1 font-bold"><?php echo e($cart['person_name']); ?></td>
                        </tr>
                        <tr>
                            <td class="p-1 text-right font-semibold w-1/4">Pay Amount:</td>
                            <td class="p-1 font-bold"><?php echo e(number_format($cart['pay_amount'], 2)); ?></td>
                            <td class="p-1 text-right font-semibold w-1/4">To the order of:</td>
                            <td class="p-1 font-bold"><?php echo e($cart['person_name']); ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        
        <div class="text-center mb-4">
            <button type="submit" name="GenerateGl" value="1" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">Generate Payment Items</button>
        </div>
    <?php endif; ?>

    
    <?php if(!empty($cart['gl_items'])): ?>
        <div class="bg-white shadow rounded-lg overflow-x-auto mb-4">
            <table class="table-auto w-full text-sm">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border px-2 py-1">Account Code</th>
                        <th class="border px-2 py-1">Account Name</th>
                        <th class="border px-2 py-1">Debit</th>
                        <th class="border px-2 py-1">Credit</th>
                        <th class="border px-2 py-1">Memo</th>
                        <th class="border px-2 py-1"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $t_debit = 0; $t_credit = 0; ?>
                    <?php $__currentLoopData = $cart['gl_items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $acct = $accounts->firstWhere('code', $item['code_id']);
                            $debit = $item['amount'] > 0 ? $item['amount'] : 0;
                            $credit = $item['amount'] < 0 ? -$item['amount'] : 0;
                            $t_debit += $debit;
                            $t_credit += $credit;
                        ?>
                        <tr class="<?php echo e($loop->even ? 'bg-gray-50' : ''); ?>">
                            <td class="border px-2 py-1"><?php echo e($acct->code ?? $item['code_id']); ?></td>
                            <td class="border px-2 py-1"><?php echo e($acct->name ?? ''); ?></td>
                            <td class="border px-2 py-1 text-right"><?php echo e($debit > 0 ? number_format($debit, 2) : ''); ?></td>
                            <td class="border px-2 py-1 text-right"><?php echo e($credit > 0 ? number_format($credit, 2) : ''); ?></td>
                            <td class="border px-2 py-1"><?php echo e($item['memo']); ?></td>
                            <td class="border px-2 py-1 text-center">
                                <button type="submit" name="Edit<?php echo e($idx); ?>" value="<?php echo e($idx); ?>" class="text-blue-600 hover:text-blue-800 text-xs">Edit</button>
                                <button type="submit" name="Delete" value="<?php echo e($idx); ?>" class="text-red-600 hover:text-red-800 text-xs" onclick="return confirm('Delete this item?')">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
                <tfoot>
                    <tr class="font-bold bg-gray-100">
                        <td class="border px-2 py-1" colspan="2">Total</td>
                        <td class="border px-2 py-1 text-right"><?php echo e(number_format($t_debit, 2)); ?></td>
                        <td class="border px-2 py-1 text-right"><?php echo e(number_format($t_credit, 2)); ?></td>
                        <td class="border px-2 py-1" colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        
        <div class="bg-white shadow rounded-lg p-4 mb-4">
            <table class="table-auto border-collapse">
                <tr>
                    <td class="p-1 text-right font-semibold">GL Account:</td>
                    <td class="p-1">
                        <select name="code_id" class="border px-2 py-1 min-w-[250px]">
                            <option value="">Select GL Account</option>
                            <?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $acct): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($acct->code); ?>" <?php if(isset($edit_index) && isset($cart['gl_items'][$edit_index]) && $cart['gl_items'][$edit_index]['code_id'] == $acct->code): echo 'selected'; endif; ?>><?php echo e($acct->code); ?> - <?php echo e($acct->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="p-1 text-right font-semibold">Amount Debit:</td>
                    <td class="p-1"><input type="text" name="AmountDebit" value="<?php echo e(isset($edit_index) && isset($cart['gl_items'][$edit_index]) && $cart['gl_items'][$edit_index]['amount'] > 0 ? number_format($cart['gl_items'][$edit_index]['amount'], 2, '.', '') : ''); ?>" size="15" class="border px-2 py-1"></td>
                </tr>
                <tr>
                    <td class="p-1 text-right font-semibold">Amount Credit:</td>
                    <td class="p-1"><input type="text" name="AmountCredit" value="<?php echo e(isset($edit_index) && isset($cart['gl_items'][$edit_index]) && $cart['gl_items'][$edit_index]['amount'] < 0 ? number_format(-$cart['gl_items'][$edit_index]['amount'], 2, '.', '') : ''); ?>" size="15" class="border px-2 py-1"></td>
                </tr>
                <tr>
                    <td class="p-1 text-right font-semibold">Memo:</td>
                    <td class="p-1"><input type="text" name="LineMemo" value="<?php echo e(isset($edit_index) && isset($cart['gl_items'][$edit_index]) ? $cart['gl_items'][$edit_index]['memo'] : ''); ?>" size="30" class="border px-2 py-1"></td>
                </tr>
            </table>
            <div class="text-center mt-2">
                <?php if($edit_index !== null): ?>
                    <button type="submit" name="UpdateItem" value="1" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-1 rounded">Update</button>
                    <button type="submit" name="CancelItemChanges" value="1" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-1 rounded">Cancel</button>
                <?php else: ?>
                    <button type="submit" name="AddItem" value="1" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-1 rounded">Add</button>
                <?php endif; ?>
            </div>
        </div>

        
        <?php if($advances->isNotEmpty()): ?>
            <div class="bg-white shadow rounded-lg mb-4 p-2">
                <h3 class="font-bold text-lg mb-2">Allocate Advances to this Payment</h3>
                <table class="table-auto w-full text-sm">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="border px-2 py-1">Trans #</th>
                            <th class="border px-2 py-1">Date</th>
                            <th class="border px-2 py-1">Amount</th>
                            <th class="border px-2 py-1">Remaining</th>
                            <th class="border px-2 py-1">Allocation</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $advances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $adv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="<?php echo e($loop->even ? 'bg-gray-50' : ''); ?>">
                                <td class="border px-2 py-1"><?php echo e($adv->trans_no); ?></td>
                                <td class="border px-2 py-1"><?php echo e($adv->pay_date); ?></td>
                                <td class="border px-2 py-1 text-right"><?php echo e(number_format($adv->pay_amount, 2)); ?></td>
                                <td class="border px-2 py-1 text-right"><?php echo e(number_format($adv->remain, 2)); ?></td>
                                <td class="border px-2 py-1 text-center">
                                    <input type="text" name="amount<?php echo e($adv->id); ?>" value="<?php echo e(request('amount'.$adv->id, 0)); ?>" size="10" class="border px-1 py-0.5 text-right" onchange="document.getElementById('alloc_warn').style.display=(parseFloat(this.value)>0&&parseFloat(this.value)><?php echo e($adv->remain); ?>)?'inline':'none'">
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
                <span id="alloc_warn" style="display:none;color:red;">Warning: allocation exceeds remaining amount</span>
            </div>
        <?php endif; ?>

        
        <div class="text-center mt-4">
            <button type="submit" name="Process" value="1" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded font-bold">Process</button>
            <button type="submit" name="CancelOrder" value="1" class="bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded font-bold">Cancel</button>
        </div>
    <?php endif; ?>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lupu\Desktop\laravel\fa-saas\resources\views/hr/payment-advice.blade.php ENDPATH**/ ?>