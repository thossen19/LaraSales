<?php $__env->startSection('title'); ?>
<?php echo e($mode === 'grn' ? 'Direct GRN Entry' : 'Purchase Order Entry'); ?> - Sales ERP
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900"><?php echo e($mode === 'grn' ? 'Direct GRN Entry' : 'Purchase Order Entry'); ?></h2>
    <p class="mt-2 text-gray-600"><?php echo e($mode === 'grn' ? 'Record a direct goods received note for a supplier.' : 'Create a new purchase order for a supplier.'); ?></p>
</div>

<?php if(session('success')): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4"><?php echo e(session('success')); ?></div>
<?php endif; ?>
<?php if(session('error')): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?php echo e(session('error')); ?></div>
<?php endif; ?>
<?php if(session('warning')): ?>
    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4"><?php echo e(session('warning')); ?></div>
<?php endif; ?>

<form method="POST" action="<?php echo e($mode === 'grn' ? route('purchases.grn.direct') : route('purchases.orders.create')); ?>" id="po-form">
<?php echo csrf_field(); ?>

<!-- Header Section -->
<div class="bg-white shadow rounded-lg overflow-hidden mb-6">
    <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700">
        <h3 class="text-lg font-semibold text-white flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            <?php echo e($mode === 'grn' ? 'GRN Details' : 'Order Details'); ?>

        </h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Left Column -->
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Supplier <span class="text-red-500">*</span></label>
                    <select name="supplier_id" onchange="this.form.submit()" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">-- Select a supplier --</option>
                        <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($s->id); ?>" <?php echo e($cart['supplier_id'] == $s->id ? 'selected' : ''); ?>><?php echo e($s->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1"><?php echo e($mode === 'grn' ? 'Delivery Date:' : 'Order Date:'); ?> <span class="text-red-500">*</span></label>
                    <input type="date" name="order_date" value="<?php echo e($cart['order_date']); ?>" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reference</label>
                    <input type="text" name="reference" value="<?php echo e($cart['reference']); ?>" maxlength="60" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>
                <?php if($supplier && $supplier->curr_code && $supplier->curr_code != 'USD'): ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Supplier Currency</label>
                        <p class="text-sm text-gray-900 py-2 bg-gray-50 rounded-md px-3 border border-gray-200"><?php echo e($supplier->curr_code); ?></p>
                    </div>
                <?php endif; ?>
            </div>
            <!-- Middle Column -->
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Supplier's Reference</label>
                    <input type="text" name="supp_ref" value="<?php echo e($cart['supp_ref']); ?>" maxlength="60" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dimension</label>
                    <select name="dimension_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="0">-- None --</option>
                        <?php $__currentLoopData = $dimensions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($d->id); ?>" <?php echo e($cart['dimension_id'] == $d->id ? 'selected' : ''); ?>><?php echo e($d->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Receive Into</label>
                    <select name="location" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">-- Select location --</option>
                        <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($loc->loc_code); ?>" <?php echo e($cart['location'] == $loc->loc_code ? 'selected' : ''); ?>><?php echo e($loc->location_name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>
            <!-- Right Column -->
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deliver to</label>
                    <textarea name="delivery_address" rows="5" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"><?php echo e($cart['delivery_address']); ?></textarea>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Items Table -->
<div class="bg-white shadow rounded-lg overflow-hidden mb-6">
    <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700">
        <h3 class="text-lg font-semibold text-white flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            Order Items
        </h3>
    </div>
    <div class="p-6">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg" id="items-table">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Item Code</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Item Description</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Quantity</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Unit</th>
                        <?php if($mode !== 'grn'): ?>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Required Delivery Date</th>
                        <?php endif; ?>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Price</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Line Total</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider" style="width:100px">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php
                        $total = 0;
                        $colspan = $mode === 'grn' ? 7 : 8;
                    ?>
                    <?php $__empty_1 = true; $__currentLoopData = $cart['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line_no => $line_item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $line_total = $line_item['quantity'] * $line_item['price'];
                            $total += $line_total;
                        ?>
                        <?php if($cart['edit_line'] === $line_no): ?>
                            <!-- Edit Row -->
                            <tr class="bg-yellow-50">
                                <td class="px-4 py-2">
                                    <input type="hidden" name="stock_id" value="<?php echo e($line_item['stock_id']); ?>">
                                    <span class="text-sm font-medium text-gray-900"><?php echo e($line_item['stock_id']); ?></span>
                                </td>
                                <td class="px-4 py-2">
                                    <input type="text" name="item_description" value="<?php echo e($line_item['item_description']); ?>" maxlength="150" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                </td>
                                <td class="px-4 py-2">
                                    <input type="number" name="qty" value="<?php echo e($line_item['quantity']); ?>" min="1" step="1" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm text-right" required>
                                </td>
                                <td class="px-4 py-2 text-center text-sm text-gray-700"><?php echo e($line_item['unit']); ?></td>
                                <?php if($mode !== 'grn'): ?>
                                <td class="px-4 py-2">
                                    <input type="date" name="req_del_date" value="<?php echo e($line_item['req_del_date']); ?>" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                </td>
                                <?php endif; ?>
                                <td class="px-4 py-2">
                                    <input type="number" name="price" value="<?php echo e($line_item['price']); ?>" min="0" step="0.01" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm text-right">
                                </td>
                                <td class="px-4 py-2 text-right text-sm text-gray-900 font-medium"><?php echo e(number_format($line_total, 2)); ?></td>
                                <td class="px-4 py-2 text-center whitespace-nowrap">
                                    <button type="submit" name="UpdateLine" value="1" class="px-3 py-1.5 text-xs bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition shadow-sm">Update</button>
                                    <button type="submit" name="CancelUpdate" value="1" class="px-3 py-1.5 text-xs bg-white text-gray-700 font-medium rounded-md hover:bg-gray-100 transition border border-gray-300 ml-1">Cancel</button>
                                </td>
                            </tr>
                        <?php else: ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900"><?php echo e($line_item['stock_id']); ?></td>
                                <td class="px-4 py-3 text-sm text-gray-700"><?php echo e($line_item['item_description']); ?></td>
                                <td class="px-4 py-3 text-sm text-gray-900 text-right"><?php echo e($line_item['quantity']); ?></td>
                                <td class="px-4 py-3 text-sm text-gray-700 text-center"><?php echo e($line_item['unit']); ?></td>
                                <?php if($mode !== 'grn'): ?>
                                <td class="px-4 py-3 text-sm text-gray-700 text-center"><?php echo e($line_item['req_del_date']); ?></td>
                                <?php endif; ?>
                                <td class="px-4 py-3 text-sm text-gray-900 text-right"><?php echo e(number_format($line_item['price'], 2)); ?></td>
                                <td class="px-4 py-3 text-sm text-gray-900 text-right font-medium"><?php echo e(number_format($line_total, 2)); ?></td>
                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    <button type="submit" name="Edit<?php echo e($line_no); ?>" value="1" class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-indigo-700 bg-indigo-50 hover:bg-indigo-100 transition" title="Edit">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        Edit
                                    </button>
                                    <button type="submit" name="Delete<?php echo e($line_no); ?>" value="1" class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100 transition ml-1" title="Delete" onclick="return confirm('Delete this line item?')">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="<?php echo e($colspan); ?>" class="px-4 py-8 text-center text-gray-500">No items added yet. Select an item below and click Add Item.</td>
                        </tr>
                    <?php endif; ?>

                    <!-- Add/Edit Item Row -->
                    <?php if($cart['edit_line'] < 0): ?>
                    <tr class="bg-gray-50/80">
                        <td class="px-4 py-2">
                            <select name="stock_id" onchange="this.form.submit()" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="">-- Select item --</option>
                                <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($item->code); ?>" <?php echo e(($cart['stock_id'] ?? '') == $item->code ? 'selected' : ''); ?>><?php echo e($item->code); ?> - <?php echo e($item->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </td>
                        <td class="px-4 py-2">
                            <?php
                                $desc = $cart['item_description'] ?? '';
                                if ((empty($cart['stock_id']) || empty($cart['item_description'])) && !empty($selected_item_info)) {
                                    $desc = $selected_item_info->name;
                                }
                            ?>
                            <input type="text" name="item_description" value="<?php echo e($desc); ?>" maxlength="150" placeholder="Item description" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </td>
                        <td class="px-4 py-2">
                            <input type="number" name="qty" value="<?php echo e($cart['qty'] ?? 1); ?>" min="1" step="1" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm text-right">
                        </td>
                        <td class="px-4 py-2 text-center text-sm text-gray-700">
                            <?php echo e($selected_item_info->unit_of_measure ?? 'each'); ?>

                        </td>
                        <?php if($mode !== 'grn'): ?>
                        <td class="px-4 py-2">
                            <input type="date" name="req_del_date" value="<?php echo e($cart['req_del_date'] ?? date('Y-m-d', strtotime('+7 days'))); ?>" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </td>
                        <?php endif; ?>
                        <td class="px-4 py-2">
                            <input type="number" name="price" value="<?php echo e($cart['price'] ?? '0.00'); ?>" min="0" step="0.01" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm text-right">
                        </td>
                        <td class="px-4 py-2 text-right text-sm text-gray-900 font-medium">
                            <?php $add_total = ($cart['qty'] ?? 1) * ($cart['price'] ?? 0); ?>
                            <?php echo e(number_format($add_total, 2)); ?>

                        </td>
                        <td class="px-4 py-2 text-center">
                            <button type="submit" name="EnterLine" value="1" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition shadow-sm">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Add Item
                            </button>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php $tax_total = 0; ?>
        <div class="mt-4 bg-gray-50 rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-end gap-6">
                <div class="text-right">
                    <span class="text-sm text-gray-500">Sub-total:</span>
                    <span class="ml-2 text-sm font-semibold text-gray-900"><?php echo e(number_format($total, 2)); ?></span>
                </div>
                <div class="text-right">
                    <span class="text-sm text-gray-500">Amount Total:</span>
                    <span class="ml-2 text-base font-bold text-indigo-700"><?php echo e(number_format($total + $tax_total, 2)); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Memo -->
<div class="bg-white shadow rounded-lg overflow-hidden mb-6">
    <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700">
        <h3 class="text-lg font-semibold text-white flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
            Memo
        </h3>
    </div>
    <div class="p-6">
        <textarea name="comments" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Optional notes or memo..."><?php echo e($cart['comments']); ?></textarea>
    </div>
</div>

<!-- Action Buttons -->
<div class="flex justify-center gap-4 mt-6">
    <button type="submit" name="CancelOrder" value="1" class="px-6 py-2.5 bg-white text-gray-700 font-medium rounded-md hover:bg-gray-100 transition border border-gray-300 shadow-sm" onclick="return confirm('Cancel this <?php echo e($mode === 'grn' ? 'GRN' : 'purchase order'); ?> entry?')">
        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        Cancel <?php echo e($mode === 'grn' ? 'GRN' : 'Order'); ?>

    </button>
    <?php if(!empty($cart['items'])): ?>
        <button type="submit" name="Commit" value="1" class="px-8 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white font-medium rounded-md hover:from-indigo-700 hover:to-indigo-800 transition shadow-sm">
            <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <?php if($mode === 'grn'): ?>
                Process GRN
            <?php else: ?>
                <?php echo e(!empty($cart['order_no']) ? 'Update Order' : 'Place Order'); ?>

            <?php endif; ?>
        </button>
    <?php endif; ?>
</div>

</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lupu\Desktop\laravel\fa-saas\resources\views/purchases/orders/create.blade.php ENDPATH**/ ?>