<?php $__env->startSection('title', 'Customer Payment Entry - Sales ERP'); ?>
<?php $__env->startSection('content'); ?>
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Customer Payment Entry</h2>
    <p class="mt-1 text-sm text-gray-500">Record a payment received from a customer and allocate it to outstanding invoices.</p>
</div>

<?php if($message): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 text-center"><?php echo e($message); ?></div>
<?php endif; ?>
<?php if($error): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?php echo e($error); ?></div>
<?php endif; ?>

<form method="POST" action="<?php echo e(route('sales.payments.index')); ?>">
<?php echo csrf_field(); ?>
<div class="bg-white shadow rounded-lg overflow-hidden mb-6">
    <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700">
        <h3 class="text-lg font-semibold text-white"><i class="fas fa-money-check-alt mr-2"></i>Payment Details</h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Customer *</label>
                <select name="customer_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-sm" required>
                    <option value="">-- Select Customer --</option>
                    <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($c->id); ?>" <?php echo e($selectedCustomerId == $c->id ? 'selected' : ''); ?>><?php echo e($c->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Payment Date *</label>
                <input type="date" name="payment_date" value="<?php echo e(date('Y-m-d')); ?>" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bank Account *</label>
                <select name="bank_account_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-sm" required>
                    <option value="">-- Select Bank Account --</option>
                    <?php $__currentLoopData = $bankAccounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ba): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($ba->id); ?>"><?php echo e($ba->bank_account_name); ?> (<?php echo e($ba->bank_curr_code); ?>)</option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Amount *</label>
                <input type="number" name="amount" id="payment_amount" value="0" step="0.01" min="0" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-sm" required>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Reference (Check # / Transaction ID)</label>
                <input type="text" name="reference" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-sm" placeholder="Optional reference number">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Memo / Notes</label>
                <input type="text" name="memo" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-sm" placeholder="Payment notes">
            </div>
        </div>
    </div>
</div>

<div class="bg-white shadow rounded-lg overflow-hidden mb-6">
    <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700">
        <h3 class="text-lg font-semibold text-white"><i class="fas fa-file-invoice mr-2"></i>Allocate to Invoices</h3>
    </div>
    <div class="p-6">
        <?php if($selectedCustomerId): ?>
            <p class="text-sm text-gray-600 mb-4">
                Outstanding invoices for selected customer.
                <span class="text-xs text-gray-400 ml-2">Enter the amount to allocate to each invoice.</span>
            </p>
            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase w-10">
                                <input type="checkbox" id="select-all-inv" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Invoice #</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Due Date</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Total Amount</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Balance Due</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Amount to Allocate</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $__empty_1 = true; $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php $balance = $inv->total_amount - $inv->paid_amount; ?>
                            <tr class="hover:bg-gray-50 transition invoice-row">
                                <td class="px-4 py-3 text-center">
                                    <input type="checkbox" class="inv-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" data-balance="<?php echo e($balance); ?>">
                                </td>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900"><?php echo e($inv->order_number); ?></td>
                                <td class="px-4 py-3 text-sm text-gray-700"><?php echo e($inv->order_date ? $inv->order_date->format('d/m/Y') : '-'); ?></td>
                                <td class="px-4 py-3 text-sm text-gray-700"><?php echo e($inv->delivery_date ? $inv->delivery_date->format('d/m/Y') : '-'); ?></td>
                                <td class="px-4 py-3 text-sm text-right text-gray-700">$<?php echo e(number_format($inv->total_amount, 2)); ?></td>
                                <td class="px-4 py-3 text-sm text-right text-gray-900 font-medium balance-cell">$<?php echo e(number_format($balance, 2)); ?></td>
                                <td class="px-4 py-3 text-right">
                                    <input type="number" name="alloc_amount[<?php echo e($inv->id); ?>]" value="0" step="0.01" min="0" max="<?php echo e($balance); ?>" class="alloc-input w-28 border border-gray-300 rounded-md px-3 py-1.5 text-right text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-sm text-gray-500 text-center">
                                    <i class="fas fa-inbox text-gray-300 text-3xl mb-2 block"></i>
                                    No outstanding invoices for this customer. Select a customer above.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-8 text-sm text-gray-500">
                <i class="fas fa-hand-pointer text-gray-300 text-3xl mb-2 block"></i>
                Please select a customer first to load outstanding invoices.
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
        <div>
            <span class="text-green-700 font-medium">Total Payment:</span>
            <span class="font-bold ml-2 text-green-800" id="display_total">$0.00</span>
        </div>
        <div>
            <span class="text-green-700 font-medium">Allocated Amount:</span>
            <span class="font-bold ml-2 text-green-800" id="display_allocated">$0.00</span>
        </div>
        <div>
            <span class="text-green-700 font-medium">Unallocated:</span>
            <span class="font-bold ml-2 text-green-800" id="display_unallocated">$0.00</span>
        </div>
    </div>
</div>

<div class="flex justify-center gap-4 mt-6">
    <button type="submit" name="CancelPayment" value="1" class="px-6 py-2.5 bg-white text-gray-700 font-medium rounded-md hover:bg-gray-100 transition border border-gray-300 shadow-sm"><i class="fas fa-times mr-2"></i>Cancel</button>
    <button type="submit" name="ProcessPayment" value="1" class="px-8 py-2.5 bg-gradient-to-r from-green-600 to-green-700 text-white font-medium rounded-md hover:from-green-700 hover:to-green-800 transition shadow-sm"><i class="fas fa-money-check-alt mr-2"></i>Record Payment</button>
</div>
</form>

<div class="bg-white shadow rounded-lg overflow-hidden mt-8">
    <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700">
        <h3 class="text-lg font-semibold text-white"><i class="fas fa-history mr-2"></i>Recent Payments</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Payment #</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Customer</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Reference</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Amount</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php $__empty_1 = true; $__currentLoopData = $recentPayments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 text-sm font-medium text-gray-900"><?php echo e($p->payment_number); ?></td>
                        <td class="px-4 py-3 text-sm text-gray-700"><?php echo e($p->customer->name ?? 'N/A'); ?></td>
                        <td class="px-4 py-3 text-sm text-gray-700"><?php echo e($p->payment_date ? $p->payment_date->format('d/m/Y') : '-'); ?></td>
                        <td class="px-4 py-3 text-sm text-gray-600"><?php echo e($p->reference ?? '-'); ?></td>
                        <td class="px-4 py-3 text-sm text-right text-gray-900 font-medium">$<?php echo e(number_format($p->amount, 2)); ?></td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800"><?php echo e(ucfirst($p->status)); ?></span>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-sm text-gray-500 text-center">No payments recorded yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
// Auto-submit on customer change
document.querySelector('select[name="customer_id"]')?.addEventListener('change', function() {
    this.closest('form').submit();
});

// Select-all checkbox
document.getElementById('select-all-inv')?.addEventListener('change', function() {
    document.querySelectorAll('.inv-checkbox').forEach(cb => cb.checked = this.checked);
});

// Auto-check when alloc amount > 0
document.querySelectorAll('.alloc-input').forEach(inp => {
    inp.addEventListener('input', function() {
        const row = this.closest('tr');
        const cb = row.querySelector('.inv-checkbox');
        if (parseFloat(this.value) > 0) {
            cb.checked = true;
        } else {
            cb.checked = false;
        }
        updateSummary();
    });
});

// Checkbox toggles alloc amount to balance or 0
document.querySelectorAll('.inv-checkbox').forEach(cb => {
    cb.addEventListener('change', function() {
        const row = this.closest('tr');
        const input = row.querySelector('.alloc-input');
        const balance = parseFloat(this.dataset.balance || 0);
        if (this.checked && parseFloat(input.value) === 0) {
            input.value = balance.toFixed(2);
        } else if (!this.checked) {
            input.value = '0.00';
        }
        updateSummary();
    });
});

function updateSummary() {
    let totalPayment = parseFloat(document.getElementById('payment_amount').value) || 0;
    let allocated = 0;
    document.querySelectorAll('.alloc-input').forEach(inp => {
        allocated += parseFloat(inp.value) || 0;
    });
    document.getElementById('display_total').textContent = '$' + totalPayment.toFixed(2);
    document.getElementById('display_allocated').textContent = '$' + allocated.toFixed(2);
    document.getElementById('display_unallocated').textContent = '$' + (totalPayment - allocated).toFixed(2);
}

document.getElementById('payment_amount')?.addEventListener('input', updateSummary);
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lupu\Desktop\laravel\fa-saas\resources\views/sales/payments/index.blade.php ENDPATH**/ ?>