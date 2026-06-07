<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<?php $__env->stopPush(); ?>
<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
flatpickr('#CreditDate', { dateFormat: 'Y-m-d' });
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('title', 'Credit all or part of an Invoice'); ?>
<?php $__env->startSection('content'); ?>
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Credit all or part of an Invoice</h2>
    <p class="mt-1 text-sm text-gray-500">Select items and quantities to credit against an invoice.</p>
</div>

<?php if(session('success')): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 text-center"><?php echo e(session('success')); ?></div>
<?php endif; ?>
<?php if(session('error')): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?php echo e(session('error')); ?></div>
<?php endif; ?>

<form method="POST" action="<?php echo e(route('sales.credit-invoice')); ?>">
<?php echo csrf_field(); ?>
<input type="hidden" name="cart_id" value="<?php echo e($cart['invoice_id'] ?? ''); ?>">

<table class="w-full mb-6 border-collapse bg-white shadow rounded-lg overflow-hidden">
    <tr>
        <td class="p-4 align-top w-1/2">
            <table class="w-full">
                <tr>
                    <td class="font-bold text-gray-700 pr-4 py-1 w-32">Customer:</td>
                    <td class="py-1"><?php echo e($cart['customer_name']); ?></td>
                </tr>
                <tr>
                    <td class="font-bold text-gray-700 pr-4 py-1">Branch:</td>
                    <td class="py-1"><?php echo e($cart['branch_name'] ?: 'N/A'); ?></td>
                </tr>
                <tr>
                    <td class="font-bold text-gray-700 pr-4 py-1">Currency:</td>
                    <td class="py-1"><?php echo e($cart['currency']); ?></td>
                </tr>
                <tr>
                    <td class="font-bold text-gray-700 pr-4 py-1">Reference:</td>
                    <td class="py-1">
                        <?php if($cart['modify_id']): ?>
                            <span class="text-gray-900"><?php echo e($cart['reference']); ?></span>
                        <?php else: ?>
                            <input type="text" name="ref" value="<?php echo e($cart['reference']); ?>" class="border border-gray-300 rounded px-2 py-1 text-sm w-48">
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td class="font-bold text-gray-700 pr-4 py-1">Crediting Invoice:</td>
                    <td class="py-1">
                        <a href="<?php echo e(route('sales.orders.show', $cart['invoice_id'])); ?>" class="text-blue-600 hover:text-blue-800 underline text-sm" target="_blank">#<?php echo e($cart['invoice_id']); ?></a>
                    </td>
                </tr>
                <tr>
                    <td class="font-bold text-gray-700 pr-4 py-1">Shipping Company:</td>
                    <td class="py-1">
                        <select name="ShipperID" class="border border-gray-300 rounded px-2 py-1 text-sm">
                            <option value="">--</option>
                            <?php $__currentLoopData = $shippers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($s->shipper_name); ?>" <?php echo e($cart['shipper_id'] == $s->shipper_name ? 'selected' : ''); ?>><?php echo e($s->shipper_name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </td>
                </tr>
            </table>
        </td>
        <td class="p-4 align-top w-1/2">
            <table class="w-full">
                <tr>
                    <td class="font-bold text-gray-700 pr-4 py-1 w-40">Invoice Date:</td>
                    <td class="py-1"><?php echo e($cart['invoice_date'] ? \Carbon\Carbon::parse($cart['invoice_date'])->format('d/m/Y') : ''); ?></td>
                </tr>
                <tr>
                    <td class="font-bold text-gray-700 pr-4 py-1">Credit Note Date:</td>
                    <td class="py-1">
                        <input type="text" id="CreditDate" name="CreditDate" value="<?php echo e($cart['credit_date']); ?>" class="border border-gray-300 rounded px-2 py-1 text-sm w-40">
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<div id="credit_items">
<div class="bg-white shadow rounded-lg overflow-hidden mb-6">
    <div class="px-6 py-4 bg-gradient-to-r from-orange-600 to-orange-700">
        <h3 class="text-lg font-semibold text-white"><i class="fas fa-list mr-2"></i>Credit Items</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Item Code</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Item Description</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Invoiced Quantity</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Units</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Credit Quantity</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Price</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Discount %</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Total</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php $k = 0; $subtotal = 0; ?>
                <?php $__currentLoopData = $cart['line_items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $li): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($li['invoiced_qty'] > 0 && $li['credit_qty'] <= 0 && $li['invoiced_qty'] == ($li['already_credited'] ?? 0)): ?>
                        <?php continue; ?>
                    <?php endif; ?>
                    <?php
                        $lineTotal = $li['credit_qty'] * $li['unit_price'] * (1 - $li['discount_percent'] / 100);
                        $subtotal += $lineTotal;
                        $rowClass = $k % 2 == 0 ? 'bg-white' : 'bg-gray-50';
                        $k++;
                    ?>
                    <tr class="<?php echo e($rowClass); ?> hover:bg-gray-100 transition">
                        <td class="px-4 py-3 text-sm font-medium text-gray-900"><?php echo e($li['stock_id']); ?></td>
                        <td class="px-4 py-3 text-sm">
                            <input type="text" name="Line<?php echo e($idx); ?>Desc" value="<?php echo e($li['description']); ?>" class="border-0 bg-transparent text-gray-700 w-full focus:outline-none">
                        </td>
                        <td class="px-4 py-3 text-sm text-right text-gray-700"><?php echo e(number_format($li['invoiced_qty'], 4)); ?></td>
                        <td class="px-4 py-3 text-sm text-center text-gray-700"><?php echo e($li['units']); ?></td>
                        <td class="px-4 py-3 text-sm text-right">
                            <?php if($li['invoiced_qty'] > 0 && $li['credit_qty'] <= 0 && $li['invoiced_qty'] == ($li['already_credited'] ?? 0)): ?>
                                <span class="text-gray-400 italic">Fully Credited</span>
                            <?php else: ?>
                                <input type="text" name="Line<?php echo e($idx); ?>" value="<?php echo e(number_format($li['credit_qty'], 4, '.', '')); ?>" class="border border-gray-300 rounded px-2 py-0.5 text-right w-24 text-sm">
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-sm text-right text-gray-700"><?php echo e(number_format($li['unit_price'], 4)); ?></td>
                        <td class="px-4 py-3 text-sm text-right text-gray-700"><?php echo e(number_format($li['discount_percent'], 2)); ?>%</td>
                        <td class="px-4 py-3 text-sm text-right font-medium text-gray-900"><?php echo e(number_format($lineTotal, 2)); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>

    <div class="p-4 border-t border-gray-200">
        <div class="w-80 ml-auto space-y-2">
            <div class="flex justify-between items-center text-sm">
                <span class="text-gray-700 font-medium">Credit Shipping Cost:</span>
                <input type="text" name="ChargeFreightCost" value="<?php echo e(number_format($cart['freight_cost'], 2, '.', '')); ?>" class="border border-gray-300 rounded px-2 py-1 text-right w-28 text-sm">
            </div>
            <?php
                $itemsTotal = array_sum(array_map(function($li) {
                    return $li['credit_qty'] * $li['unit_price'] * (1 - $li['discount_percent'] / 100);
                }, $cart['line_items']));
                $subtotalDisplay = $itemsTotal + $cart['freight_cost'];
            ?>
            <div class="flex justify-between text-sm">
                <span class="text-gray-700 font-medium">Sub-total:</span>
                <span class="font-medium text-gray-900">$<?php echo e(number_format($subtotalDisplay, 2)); ?></span>
            </div>
            <div class="flex justify-between text-base font-bold pt-2 border-t border-gray-200">
                <span class="text-gray-900">Credit Note Total:</span>
                <span class="text-orange-700">$<?php echo e(number_format($subtotalDisplay, 2)); ?></span>
            </div>
        </div>
    </div>
</div>
</div>

<div class="bg-white shadow rounded-lg overflow-hidden mb-6">
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <table class="w-full">
                    <tr>
                        <td class="font-bold text-gray-700 pr-4 py-2 w-48">Credit Note Type:</td>
                        <td class="py-2">
                            <select name="CreditType" class="border border-gray-300 rounded px-2 py-1 text-sm" onchange="this.closest('form').querySelector('[name=Update]').click();">
                                <option value="Return" <?php echo e(($cart['credit_type'] ?? 'Return') == 'Return' ? 'selected' : ''); ?>>Return</option>
                                <option value="Write Off" <?php echo e(($cart['credit_type'] ?? '') == 'Write Off' ? 'selected' : ''); ?>>Write Off</option>
                            </select>
                        </td>
                    </tr>
                    <?php if(($cart['credit_type'] ?? 'Return') == 'Return'): ?>
                    <tr>
                        <td class="font-bold text-gray-700 pr-4 py-2">Items Returned to Location:</td>
                        <td class="py-2">
                            <select name="Location" class="border border-gray-300 rounded px-2 py-1 text-sm w-64">
                                <option value="">-- Select Location --</option>
                                <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($loc->location_code ?? $loc->location_name); ?>" <?php echo e($cart['return_location'] == ($loc->location_code ?? $loc->location_name) ? 'selected' : ''); ?>><?php echo e($loc->location_name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </td>
                    </tr>
                    <?php else: ?>
                    <tr>
                        <td class="font-bold text-gray-700 pr-4 py-2">Write off the cost of the items to:</td>
                        <td class="py-2">
                            <select name="WriteOffGLCode" class="border border-gray-300 rounded px-2 py-1 text-sm w-64">
                                <option value="">-- Select GL Account --</option>
                                <?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $acc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($acc->code); ?>" <?php echo e($cart['write_off_gl'] == $acc->code ? 'selected' : ''); ?>><?php echo e($acc->code); ?> - <?php echo e($acc->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
            <div>
                <label class="font-bold text-gray-700 block mb-2">Memo:</label>
                <textarea name="CreditText" rows="3" class="w-full border border-gray-300 rounded px-3 py-2 text-sm"><?php echo e($cart['memo']); ?></textarea>
            </div>
        </div>
    </div>
</div>

<div class="flex justify-center gap-4 mt-6">
    <button type="submit" name="Update" value="1" class="px-6 py-2.5 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 transition shadow-sm"><i class="fas fa-sync mr-2"></i>Update</button>
    <button type="submit" name="ProcessCredit" value="1" class="px-8 py-2.5 bg-gradient-to-r from-orange-600 to-orange-700 text-white font-medium rounded-md hover:from-orange-700 hover:to-orange-800 transition shadow-sm"><i class="fas fa-receipt mr-2"></i>Process Credit Note</button>
</div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lupu\Desktop\laravel\fa-saas\resources\views/sales/credit-invoice/index.blade.php ENDPATH**/ ?>