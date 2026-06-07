<?php $__env->startSection('title', 'Edit Customer - Sales ERP'); ?>

<?php $__env->startSection('content'); ?>
    <div>
        <div class="mb-6 flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Customer: <?php echo e($customer->name); ?></h2>
                    <p class="mt-1 text-gray-600">
                        Code: <?php echo e($customer->customer_code); ?> | 
                        Status: 
                        <?php if($customer->status == 'active'): ?>
                            <span class="text-green-600 font-medium">Active</span>
                        <?php elseif($customer->status == 'inactive'): ?>
                            <span class="text-gray-600 font-medium">Inactive</span>
                        <?php else: ?>
                            <span class="text-yellow-600 font-medium">On Hold</span>
                        <?php endif; ?>
                    </p>
                </div>
                <div class="flex gap-2">
                    <a href="<?php echo e(route('sales.customers.index')); ?>" class="px-4 py-2 bg-white text-gray-700 text-sm font-medium rounded-md hover:bg-gray-100 transition border border-gray-300">
                        <i class="fas fa-arrow-left mr-1"></i>Back
                    </a>
                </div>
            </div>

            <!-- Tab Navigation -->
            <div class="border-b border-gray-200 mb-6">
                <nav class="flex gap-6">
                    <button type="button" class="tab-link active px-1 py-3 text-sm font-medium text-indigo-600 border-b-2 border-indigo-600" data-tab="general">
                        <i class="fas fa-cog mr-1"></i>General settings
                    </button>
                    <button type="button" class="tab-link px-1 py-3 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent" data-tab="branches">
                        <i class="fas fa-code-branch mr-1"></i>Branches
                    </button>
                    <button type="button" class="tab-link px-1 py-3 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent" data-tab="transactions">
                        <i class="fas fa-exchange-alt mr-1"></i>Transactions
                    </button>
                    <button type="button" class="tab-link px-1 py-3 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent" data-tab="orders">
                        <i class="fas fa-file-invoice mr-1"></i>Sales Orders
                    </button>
                </nav>
            </div>

            <!-- Tab: General Settings -->
            <div id="tab-general" class="tab-content">
                <form method="POST" action="<?php echo e(route('sales.customers.update', $customer)); ?>">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700">
                        <h3 class="text-lg font-semibold text-white"><i class="fas fa-cog mr-2"></i>General Settings</h3>
                    </div>
                    <div class="p-6">
                        <table class="w-full" style="border-collapse: separate; border-spacing: 0 8px;">
                            <tr class="align-top">
                                <td class="w-1/2 pr-6" style="vertical-align: top;">
                                    <h4 class="text-md font-semibold text-gray-800 mb-4 border-b pb-2">Name and Address</h4>
                                    <table class="w-full" style="border-collapse: separate; border-spacing: 0 6px;">
                                        <tr>
                                            <td class="text-sm font-medium text-gray-700 w-1/3 pb-0.5">Customer Name:</td>
                                            <td class="pb-0.5"><input type="text" name="name" value="<?php echo e(old('name', $customer->name)); ?>" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" maxlength="80" required></td>
                                        </tr>
                                        <tr>
                                            <td class="text-sm font-medium text-gray-700 pb-0.5">Customer Short Name:</td>
                                            <td class="pb-0.5"><input type="text" name="cust_ref" value="<?php echo e(old('cust_ref', $customer->cust_ref)); ?>" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" maxlength="50"></td>
                                        </tr>
                                        <tr>
                                            <td class="text-sm font-medium text-gray-700 pb-0.5">Address:</td>
                                            <td class="pb-0.5"><textarea name="address" rows="3" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" required><?php echo e(old('address', $customer->address)); ?></textarea></td>
                                        </tr>
                                        <tr>
                                            <td class="text-sm font-medium text-gray-700 pb-0.5">City:</td>
                                            <td class="pb-0.5"><input type="text" name="city" value="<?php echo e(old('city', $customer->city)); ?>" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" maxlength="100" required></td>
                                        </tr>
                                        <tr>
                                            <td class="text-sm font-medium text-gray-700 pb-0.5">State:</td>
                                            <td class="pb-0.5"><input type="text" name="state" value="<?php echo e(old('state', $customer->state)); ?>" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" maxlength="100" required></td>
                                        </tr>
                                        <tr>
                                            <td class="text-sm font-medium text-gray-700 pb-0.5">Postal Code:</td>
                                            <td class="pb-0.5"><input type="text" name="postal_code" value="<?php echo e(old('postal_code', $customer->postal_code)); ?>" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" maxlength="20" required></td>
                                        </tr>
                                        <tr>
                                            <td class="text-sm font-medium text-gray-700 pb-0.5">Country:</td>
                                            <td class="pb-0.5"><input type="text" name="country" value="<?php echo e(old('country', $customer->country)); ?>" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" maxlength="100" required></td>
                                        </tr>
                                        <tr>
                                            <td class="text-sm font-medium text-gray-700 pb-0.5">GSTNo:</td>
                                            <td class="pb-0.5"><input type="text" name="tax_id" value="<?php echo e(old('tax_id', $customer->tax_id)); ?>" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" maxlength="50"></td>
                                        </tr>
                                        <tr>
                                            <td class="text-sm font-medium text-gray-700 pb-0.5">Phone:</td>
                                            <td class="pb-0.5"><input type="text" name="phone" value="<?php echo e(old('phone', $customer->phone)); ?>" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" maxlength="50" required></td>
                                        </tr>
                                        <tr>
                                            <td class="text-sm font-medium text-gray-700 pb-0.5">Fax:</td>
                                            <td class="pb-0.5"><input type="text" name="fax" value="<?php echo e(old('fax', $customer->fax)); ?>" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" maxlength="50"></td>
                                        </tr>
                                        <tr>
                                            <td class="text-sm font-medium text-gray-700 pb-0.5">Email:</td>
                                            <td class="pb-0.5"><input type="email" name="email" value="<?php echo e(old('email', $customer->email)); ?>" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" maxlength="55"></td>
                                        </tr>
                                        <tr>
                                            <td class="text-sm font-medium text-gray-700 pb-0.5">Customer's Currency:</td>
                                            <td class="pb-0.5">
                                                <select name="curr_code" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                                    <?php $__currentLoopData = $currencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($code); ?>" <?php echo e(old('curr_code', $customer->curr_code ?? 'USD') == $code ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-sm font-medium text-gray-700 pb-0.5">Sales Type/Price List:</td>
                                            <td class="pb-0.5">
                                                <select name="sales_type_id" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                                    <option value="">Default</option>
                                                    <?php $__currentLoopData = $salesTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($st->id); ?>" <?php echo e(old('sales_type_id', $customer->sales_type_id) == $st->id ? 'selected' : ''); ?>><?php echo e($st->type_name); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td class="w-1/2 pl-6" style="vertical-align: top;">
                                    <h4 class="text-md font-semibold text-gray-800 mb-4 border-b pb-2">Sales</h4>
                                    <table class="w-full" style="border-collapse: separate; border-spacing: 0 6px;">
                                        <tr>
                                            <td class="text-sm font-medium text-gray-700 w-2/5 pb-0.5">Discount Percent:</td>
                                            <td class="pb-0.5"><input type="number" name="discount" value="<?php echo e(old('discount', $customer->discount ?? 0)); ?>" step="0.01" min="0" max="100" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"></td>
                                        </tr>
                                        <tr>
                                            <td class="text-sm font-medium text-gray-700 pb-0.5">Prompt Payment Discount %:</td>
                                            <td class="pb-0.5"><input type="number" name="pymt_discount" value="<?php echo e(old('pymt_discount', $customer->pymt_discount ?? 0)); ?>" step="0.01" min="0" max="100" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"></td>
                                        </tr>
                                        <tr>
                                            <td class="text-sm font-medium text-gray-700 pb-0.5">Credit Limit:</td>
                                            <td class="pb-0.5"><input type="number" name="credit_limit" value="<?php echo e(old('credit_limit', $customer->credit_limit)); ?>" step="0.01" min="0" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"></td>
                                        </tr>
                                        <tr>
                                            <td class="text-sm font-medium text-gray-700 pb-0.5">Payment Terms:</td>
                                            <td class="pb-0.5">
                                                <select name="payment_terms" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                                    <option value="0" <?php echo e(old('payment_terms', $customer->payment_terms) == 0 ? 'selected' : ''); ?>>Cash Sale</option>
                                                    <option value="7" <?php echo e(old('payment_terms', $customer->payment_terms) == 7 ? 'selected' : ''); ?>>7 Days</option>
                                                    <option value="10" <?php echo e(old('payment_terms', $customer->payment_terms) == 10 ? 'selected' : ''); ?>>10 Days</option>
                                                    <option value="14" <?php echo e(old('payment_terms', $customer->payment_terms) == 14 ? 'selected' : ''); ?>>14 Days</option>
                                                    <option value="15" <?php echo e(old('payment_terms', $customer->payment_terms) == 15 ? 'selected' : ''); ?>>15 Days</option>
                                                    <option value="21" <?php echo e(old('payment_terms', $customer->payment_terms) == 21 ? 'selected' : ''); ?>>21 Days</option>
                                                    <option value="30" <?php echo e(old('payment_terms', $customer->payment_terms) == 30 ? 'selected' : ''); ?>>30 Days</option>
                                                    <option value="45" <?php echo e(old('payment_terms', $customer->payment_terms) == 45 ? 'selected' : ''); ?>>45 Days</option>
                                                    <option value="60" <?php echo e(old('payment_terms', $customer->payment_terms) == 60 ? 'selected' : ''); ?>>60 Days</option>
                                                    <option value="90" <?php echo e(old('payment_terms', $customer->payment_terms) == 90 ? 'selected' : ''); ?>>90 Days</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-sm font-medium text-gray-700 pb-0.5">Credit Status:</td>
                                            <td class="pb-0.5">
                                                <select name="credit_status_id" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                                    <option value="">None</option>
                                                    <?php $__currentLoopData = $creditStatuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($cs->id); ?>" <?php echo e(old('credit_status_id', $customer->credit_status_id) == $cs->id ? 'selected' : ''); ?>><?php echo e($cs->status_name); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-sm font-medium text-gray-700 pb-0.5">Sales Group:</td>
                                            <td class="pb-0.5">
                                                <select name="sales_group_id" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                                    <option value="">None</option>
                                                    <?php $__currentLoopData = $salesGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($sg->id); ?>" <?php echo e(old('sales_group_id', $customer->sales_group_id) == $sg->id ? 'selected' : ''); ?>><?php echo e($sg->group_name); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-sm font-medium text-gray-700 pb-0.5">Sales Person:</td>
                                            <td class="pb-0.5">
                                                <select name="sales_person_id" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                                    <option value="">None</option>
                                                    <?php $__currentLoopData = $salesPersons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($sp->id); ?>" <?php echo e(old('sales_person_id', $customer->sales_person_id) == $sp->id ? 'selected' : ''); ?>><?php echo e($sp->name); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-sm font-medium text-gray-700 pb-0.5">Contact Person:</td>
                                            <td class="pb-0.5"><input type="text" name="contact_person" value="<?php echo e(old('contact_person', $customer->contact_person)); ?>" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" maxlength="255" required></td>
                                        </tr>
                                        <tr>
                                            <td class="text-sm font-medium text-gray-700 pb-0.5">Inactive:</td>
                                            <td class="pb-0.5">
                                                <label class="inline-flex items-center">
                                                    <input type="checkbox" name="inactive" value="1" <?php echo e(old('inactive', $customer->status == 'inactive' ? 'checked' : '') ? 'checked' : ''); ?> class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                                    <span class="ml-2 text-sm text-gray-600">Record is inactive</span>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-sm font-medium text-gray-700 pb-0.5">General Notes:</td>
                                            <td class="pb-0.5"><textarea name="notes" rows="3" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"><?php echo e(old('notes', $customer->notes)); ?></textarea></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>

                        <?php if($errors->any()): ?>
                            <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded">
                                <ul class="list-disc list-inside text-sm text-red-700">
                                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li><?php echo e($error); ?></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <div class="mt-6 flex justify-center gap-3">
                            <button type="submit" class="px-5 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition">
                                <i class="fas fa-save mr-1"></i>Update Customer
                            </button>
                            <button type="button" onclick="if(confirm('Are you sure you want to delete this customer?')) { document.getElementById('delete-form').submit(); }" class="px-5 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 transition">
                                <i class="fas fa-trash mr-1"></i>Delete Customer
                            </button>
                        </div>
                    </div>
                </div>
                </form>
                <form id="delete-form" action="<?php echo e(route('sales.customers.destroy', $customer)); ?>" method="POST" class="hidden">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                </form>
            </div>

            <!-- Tab: Branches -->
            <div id="tab-branches" class="tab-content hidden">
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700">
                        <h3 class="text-lg font-semibold text-white"><i class="fas fa-code-branch mr-2"></i>Customer Branches</h3>
                    </div>
                    <div class="p-6">
                        <?php if($customer->branches->count() > 0): ?>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase">Branch Name</th>
                                            <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase">Address</th>
                                            <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase">Phone</th>
                                            <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <?php $__currentLoopData = $customer->branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $br): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-2.5 text-sm font-medium text-gray-900"><?php echo e($br->branch_name); ?></td>
                                                <td class="px-4 py-2.5 text-sm text-gray-700"><?php echo e($br->address); ?></td>
                                                <td class="px-4 py-2.5 text-sm text-gray-700"><?php echo e($br->phone); ?></td>
                                                <td class="px-4 py-2.5 text-sm">
                                                    <a href="<?php echo e(route('sales.customers.branches.edit', $br)); ?>" class="text-indigo-600 hover:text-indigo-900 mr-2"><i class="fas fa-edit"></i></a>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-8 text-gray-500">
                                <i class="fas fa-code-branch text-4xl mb-3 text-gray-300"></i>
                                <p class="text-base">No branches defined for this customer.</p>
                            </div>
                        <?php endif; ?>
                        <div class="mt-4">
                            <a href="<?php echo e(route('sales.customers.branches')); ?>?customer_id=<?php echo e($customer->id); ?>" class="text-sm text-indigo-600 hover:text-indigo-900">
                                <i class="fas fa-plus mr-1"></i>Add or Edit Branches
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab: Transactions -->
            <div id="tab-transactions" class="tab-content hidden">
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700">
                        <h3 class="text-lg font-semibold text-white"><i class="fas fa-exchange-alt mr-2"></i>Customer Transactions</h3>
                    </div>
                    <div class="p-6">
                        <div class="text-center py-8">
                            <i class="fas fa-exchange-alt text-4xl mb-3 text-gray-300"></i>
                            <p class="text-base text-gray-500">View customer transactions in the <a href="<?php echo e(route('sales.inquiries.transactions')); ?>?customer_id=<?php echo e($customer->id); ?>" class="text-indigo-600 hover:text-indigo-900">Transaction Inquiry</a>.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab: Orders -->
            <div id="tab-orders" class="tab-content hidden">
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700">
                        <h3 class="text-lg font-semibold text-white"><i class="fas fa-file-invoice mr-2"></i>Sales Orders</h3>
                    </div>
                    <div class="p-6">
                        <div class="text-center py-8">
                            <i class="fas fa-file-invoice text-4xl mb-3 text-gray-300"></i>
                            <p class="text-base text-gray-500">View sales orders in the <a href="<?php echo e(route('sales.inquiries.orders')); ?>?customer_id=<?php echo e($customer->id); ?>" class="text-indigo-600 hover:text-indigo-900">Orders Inquiry</a>.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
    <script>
        document.querySelectorAll('.tab-link').forEach(function(tab) {
            tab.addEventListener('click', function() {
                document.querySelectorAll('.tab-link').forEach(function(t) {
                    t.classList.remove('active', 'text-indigo-600', 'border-indigo-600');
                    t.classList.add('text-gray-500', 'border-transparent');
                });
                this.classList.add('active', 'text-indigo-600', 'border-indigo-600');
                this.classList.remove('text-gray-500', 'border-transparent');

                document.querySelectorAll('.tab-content').forEach(function(c) {
                    c.classList.add('hidden');
                });
                document.getElementById('tab-' + this.dataset.tab).classList.remove('hidden');
            });
        });
    </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lupu\Desktop\laravel\fa-saas\resources\views/sales/customers/edit.blade.php ENDPATH**/ ?>