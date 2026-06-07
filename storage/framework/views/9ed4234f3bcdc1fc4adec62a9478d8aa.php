<?php $__env->startSection('title', 'Direct Invoice Entry - Sales ERP'); ?>
<?php $__env->startSection('content'); ?>
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Direct Invoice Entry</h2>
    <p class="mt-1 text-sm text-gray-500">Create invoices directly without prior sales orders or deliveries.</p>
</div>

<?php if($message): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 text-center"><?php echo e($message); ?></div>
<?php endif; ?>
<?php if($error): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?php echo e($error); ?></div>
<?php endif; ?>

<?php if($addedID): ?>
    <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4 text-center">
        Invoice # <?php echo e($addedID); ?> has been entered.
    </div>
    <div class="text-center mb-4">
        <a href="<?php echo e(route('sales.invoice.direct', ['NewInvoice' => 'Yes'])); ?>" class="text-indigo-600 hover:text-indigo-900 underline">Enter a New Invoice</a>
    </div>
<?php else: ?>

<form method="POST" action="<?php echo e(route('sales.invoice.direct')); ?>">
<?php echo csrf_field(); ?>

<div class="bg-white shadow rounded-lg overflow-hidden mb-6">
    <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700">
        <h3 class="text-lg font-semibold text-white"><i class="fas fa-file-invoice mr-2"></i>Invoice Header</h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Customer <span class="text-red-500">*</span></label>
                    <select name="customer_id" onchange="this.form.submit()" class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                        <option value="">-- Select --</option>
                        <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($c->id); ?>" <?php echo e($cart['customer_id'] == $c->id ? 'selected' : ''); ?>><?php echo e($c->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Branch <span class="text-red-500">*</span></label>
                    <select name="branch_id" onchange="this.form.submit()" class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                        <option value="">-- Select --</option>
                        <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($b->id); ?>" <?php echo e($cart['branch_id'] == $b->id ? 'selected' : ''); ?>><?php echo e($b->branch_name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reference <span class="text-red-500">*</span></label>
                    <input type="text" name="ref" value="<?php echo e(old('ref', $cart['reference'] ?: $defaultRef)); ?>" class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>
            </div>
            <div>
                <?php if($customerInfo): ?>
                    <div class="mb-4 p-3 bg-gray-50 rounded-md border border-gray-200">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-500">Customer Currency</span>
                            <span class="text-sm font-medium text-gray-900">USD</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Customer Discount</span>
                            <span class="text-sm font-medium text-gray-900"><?php echo e($customerInfo->discount ?? '0'); ?>%</span>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Payment</label>
                    <select name="payment" onchange="this.form.submit()" class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                        <option value="">-- Select --</option>
                        <?php $__currentLoopData = $paymentTerms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($pt->terms_indicator); ?>" <?php echo e($cart['payment'] == $pt->terms_indicator ? 'selected' : ''); ?>><?php echo e($pt->terms); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Price List</label>
                    <select name="sales_type" onchange="this.form.submit()" class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                        <option value="">-- Select --</option>
                        <?php $__currentLoopData = $salesTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($st->id); ?>" <?php echo e($cart['sales_type'] == $st->id ? 'selected' : ''); ?>><?php echo e($st->type_name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>
            <div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Invoice Date <span class="text-red-500">*</span></label>
                    <input type="date" name="OrderDate" value="<?php echo e($cart['ord_date']); ?>" onchange="this.form.submit()" class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                    <input type="date" name="delivery_date" value="<?php echo e($cart['delivery_date']); ?>" class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>
            </div>
            <div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deliver from Location</label>
                    <select name="Location" onchange="this.form.submit()" class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                        <option value="">-- Select --</option>
                        <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($loc->loc_code); ?>" <?php echo e($cart['location'] == $loc->loc_code ? 'selected' : ''); ?>><?php echo e($loc->location_name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pre-Payment Required</label>
                    <input type="text" name="prep_amount" value="0" class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="bg-white shadow rounded-lg overflow-hidden mb-6">
    <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700">
        <h3 class="text-lg font-semibold text-white"><i class="fas fa-shopping-cart mr-2"></i>Invoice Items</h3>
    </div>
    <div class="p-6">
        <div id="items_table">
            <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Item Code</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Item Description</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Quantity</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Unit</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Price</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Discount %</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Total</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider" colspan="2">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $k = 0; $total = 0; ?>
                    <?php $__currentLoopData = $cart['line_items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line_no => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $lineTotal = $item['quantity'] * $item['price'] * (1 - $item['discount_percent']);
                            $total += $lineTotal;
                        ?>
                        <?php if($edit_index !== null && $edit_index == $line_no): ?>
                            <tr class="bg-indigo-50">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900"><?php echo e($item['stock_id']); ?></td>
                                <td class="px-4 py-3"><input type="text" name="item_description" value="<?php echo e($item['item_description']); ?>" class="w-full border border-gray-300 rounded-md px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></td>
                                <td class="px-4 py-3 text-right"><input type="text" name="qty" value="<?php echo e(number_format($item['quantity'], 4)); ?>" class="w-24 border border-gray-300 rounded-md px-3 py-1.5 text-right text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></td>
                                <td class="px-4 py-3 text-center text-sm text-gray-700"><?php echo e($item['units']); ?></td>
                                <td class="px-4 py-3 text-right"><input type="text" name="price" value="<?php echo e(number_format($item['price'], 4)); ?>" class="w-24 border border-gray-300 rounded-md px-3 py-1.5 text-right text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></td>
                                <td class="px-4 py-3 text-right"><input type="text" name="Disc" value="<?php echo e(number_format($item['discount_percent'] * 100, 2)); ?>" class="w-20 border border-gray-300 rounded-md px-3 py-1.5 text-right text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></td>
                                <td class="px-4 py-3 text-sm text-right font-medium text-gray-900"><?php echo e(number_format($lineTotal, 4)); ?></td>
                                <td class="px-4 py-3 text-center" colspan="2">
                                    <button type="submit" name="UpdateItem" value="1" class="px-3 py-1.5 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition shadow-sm"><i class="fas fa-check mr-1"></i>Update</button>
                                    <button type="submit" name="CancelItemChanges" value="1" class="px-3 py-1.5 bg-white text-gray-700 text-sm font-medium rounded-md hover:bg-gray-100 transition border border-gray-300 ml-1"><i class="fas fa-times mr-1"></i>Cancel</button>
                                </td>
                            </tr>
                        <?php else: ?>
                            <tr class="<?php echo e($k % 2 == 0 ? 'bg-white' : 'bg-gray-50/50'); ?> hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900"><?php echo e($item['stock_id']); ?></td>
                                <td class="px-4 py-3 text-sm text-gray-700"><?php echo e($item['item_description']); ?></td>
                                <td class="px-4 py-3 text-sm text-right text-gray-700"><?php echo e(number_format($item['quantity'], 4)); ?></td>
                                <td class="px-4 py-3 text-sm text-center text-gray-700"><?php echo e($item['units']); ?></td>
                                <td class="px-4 py-3 text-sm text-right text-gray-700"><?php echo e(number_format($item['price'], 4)); ?></td>
                                <td class="px-4 py-3 text-sm text-right text-gray-700"><?php echo e(number_format($item['discount_percent'] * 100, 2)); ?>%</td>
                                <td class="px-4 py-3 text-sm text-right font-medium text-gray-900"><?php echo e(number_format($lineTotal, 4)); ?></td>
                                <?php if($edit_index === null): ?>
                                    <td class="px-4 py-3 text-center">
                                        <button type="submit" name="Edit" value="<?php echo e($line_no); ?>" class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 text-sm font-medium rounded-md text-indigo-700 bg-indigo-50 hover:bg-indigo-100 transition"><i class="fas fa-edit mr-1"></i>Edit</button>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <button type="submit" name="Delete" value="<?php echo e($line_no); ?>" onclick="return confirm('Are you sure?')" class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 text-sm font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100 transition"><i class="fas fa-trash mr-1"></i>Delete</button>
                                    </td>
                                <?php else: ?>
                                    <td class="px-4 py-3 text-center text-sm text-gray-400" colspan="2">-</td>
                                <?php endif; ?>
                            </tr>
                        <?php endif; ?>
                        <?php $k++; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php if($edit_index === null): ?>
                        <tr class="bg-gray-50/80">
                            <td class="px-4 py-3">
                                <div class="flex">
                                    <input type="text" name="stock_id" value="<?php echo e($cart['stock_id']); ?>" placeholder="Item Code" class="flex-1 border border-gray-300 rounded-l-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <button type="button" onclick="openItemSearch()" class="inline-flex items-center px-3 py-2 border border-l-0 border-gray-300 rounded-r-md bg-gray-50 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 transition">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <?php if($cart['stock_id']): ?>
                                    <?php $selItem = \DB::table('items')->where('code', $cart['stock_id'])->first(); ?>
                                    <input type="text" name="item_description" value="<?php echo e($cart['item_description'] ?: ($selItem->name ?? '')); ?>" class="w-full border border-gray-300 rounded-md px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <input type="text" name="qty" value="<?php echo e(number_format($cart['qty'] ?: 1, 4)); ?>" class="w-24 border border-gray-300 rounded-md px-3 py-1.5 text-right text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </td>
                            <td class="px-4 py-3 text-center text-sm text-gray-700">
                                <?php if($cart['stock_id']): ?><?php echo e($selItem->unit_of_measure ?? ''); ?><?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <?php if($cart['stock_id']): ?>
                                    <input type="text" name="price" value="<?php echo e(number_format($cart['price'] ?: ($selItem->cost_price ?? 0), 4)); ?>" class="w-24 border border-gray-300 rounded-md px-3 py-1.5 text-right text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <input type="text" name="Disc" value="<?php echo e(number_format($cart['Disc'] ?? 0, 2)); ?>" class="w-20 border border-gray-300 rounded-md px-3 py-1.5 text-right text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </td>
                            <td class="px-4 py-3 text-right">&nbsp;</td>
                            <td class="px-4 py-3 text-center" colspan="2">
                                <button type="submit" name="AddItem" value="1" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition shadow-sm"><i class="fas fa-plus mr-1"></i>Add Item</button>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            </div>

            <div class="mt-4 bg-gray-50 rounded-lg border border-gray-200 p-4">
                <div class="flex items-center justify-end gap-4">
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-600">Shipping Charge:</span>
                        <input type="text" name="freight_cost" value="<?php echo e(number_format($cart['freight_cost'], 4)); ?>" class="w-24 border border-gray-300 rounded-md px-3 py-1.5 text-right text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <button type="submit" name="update" value="1" class="px-4 py-1.5 bg-white text-gray-700 text-sm font-medium rounded-md hover:bg-gray-100 transition border border-gray-300"><i class="fas fa-sync-alt mr-1"></i>Update</button>
                </div>
                <div class="flex items-center justify-end gap-6 mt-2">
                    <?php $subtotal = $total + ($cart['freight_cost'] ?? 0); ?>
                    <div class="text-right">
                        <span class="text-sm text-gray-500">Sub-total:</span>
                        <span class="ml-2 text-sm font-semibold text-gray-900"><?php echo e(number_format($subtotal, 4)); ?></span>
                    </div>
                    <div class="text-right">
                        <span class="text-sm text-gray-500">Amount Total:</span>
                        <span class="ml-2 text-base font-bold text-indigo-700"><?php echo e(number_format($subtotal, 4)); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="bg-white shadow rounded-lg overflow-hidden mb-6">
    <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700">
        <h3 class="text-lg font-semibold text-white"><i class="fas fa-truck mr-2"></i>Delivery Details</h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deliver To <span class="text-red-500">*</span></label>
                    <input type="text" name="deliver_to" value="<?php echo e($cart['deliver_to']); ?>" class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address <span class="text-red-500">*</span></label>
                    <textarea name="delivery_address" rows="3" class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"><?php echo e($cart['delivery_address']); ?></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contact Phone Number</label>
                    <input type="text" name="phone" value="<?php echo e($cart['phone']); ?>" class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>
            </div>
            <div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Customer Reference</label>
                    <input type="text" name="cust_ref" value="<?php echo e($cart['cust_ref']); ?>" class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Comments</label>
                    <textarea name="Comments" rows="3" class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"><?php echo e($cart['comments']); ?></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Shipping Company</label>
                    <select name="ship_via" class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                        <option value="">-- Select --</option>
                        <?php $__currentLoopData = $shippers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sh): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($sh->shipper_id); ?>" <?php echo e($cart['ship_via'] == $sh->shipper_id ? 'selected' : ''); ?>><?php echo e($sh->shipper_name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="flex justify-center gap-4 mt-6">
    <button type="submit" name="CancelOrder" value="1" class="px-6 py-2.5 bg-white text-gray-700 font-medium rounded-md hover:bg-gray-100 transition border border-gray-300 shadow-sm"><i class="fas fa-times mr-2"></i>Cancel Invoice</button>
    <button type="submit" name="ProcessOrder" value="1" class="px-8 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white font-medium rounded-md hover:from-indigo-700 hover:to-indigo-800 transition shadow-sm"><i class="fas fa-check mr-2"></i>Place Invoice</button>
</div>
</form>
<?php endif; ?>
<?php echo $__env->make('components.item-search-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lupu\Desktop\laravel\fa-saas\resources\views/sales/invoice/direct.blade.php ENDPATH**/ ?>