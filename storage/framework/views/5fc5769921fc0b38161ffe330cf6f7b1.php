<?php $__env->startSection('title', 'Customer Transaction Inquiry - Sales ERP'); ?>
<?php $__env->startSection('content'); ?>
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Customer Transaction Inquiry</h2>
    <p class="mt-1 text-sm text-gray-500">View comprehensive transaction history for customers including orders, invoices, payments, and credits.</p>
</div>

<form method="GET" action="<?php echo e(route('sales.inquiries.transactions')); ?>">
<div class="bg-white shadow rounded-lg overflow-hidden mb-6">
    <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700">
        <h3 class="text-lg font-semibold text-white"><i class="fas fa-filter mr-2"></i>Select Customer &amp; Filters</h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Customer</label>
                <select name="customer_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-sm">
                    <option value="all">All Customers</option>
                    <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($c->id); ?>" <?php echo e($selectedCustomer == $c->id ? 'selected' : ''); ?>><?php echo e($c->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Transaction Type</label>
                <select name="type_filter" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-sm">
                    <option value="all" <?php echo e($typeFilter == 'all' || !$typeFilter ? 'selected' : ''); ?>>All Types</option>
                    <option value="invoices" <?php echo e($typeFilter == 'invoices' ? 'selected' : ''); ?>>Sales Invoices</option>
                    <option value="unsettled" <?php echo e($typeFilter == 'unsettled' ? 'selected' : ''); ?>>Unsettled transactions</option>
                    <option value="payments" <?php echo e($typeFilter == 'payments' ? 'selected' : ''); ?>>Payments</option>
                    <option value="credits" <?php echo e($typeFilter == 'credits' ? 'selected' : ''); ?>>Credit Notes</option>
                    <option value="deliveries" <?php echo e($typeFilter == 'deliveries' ? 'selected' : ''); ?>>Delivery Notes</option>
                    <option value="journal" <?php echo e($typeFilter == 'journal' ? 'selected' : ''); ?>>Journal Entries</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                <input type="date" name="date_from" value="<?php echo e($dateFrom); ?>" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                <input type="date" name="date_to" value="<?php echo e($dateTo); ?>" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-sm">
            </div>
        </div>
        <div class="flex justify-end gap-2">
            <a href="<?php echo e(route('sales.inquiries.transactions')); ?>" class="px-4 py-2 bg-white text-gray-700 text-sm font-medium rounded-md hover:bg-gray-100 transition border border-gray-300"><i class="fas fa-undo mr-1"></i>Reset</a>
            <button type="submit" class="px-5 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition shadow-sm"><i class="fas fa-search mr-1"></i>View Transactions</button>
        </div>
    </div>
</div>
</form>

<?php if(request()->anyFilled(['customer_id', 'type_filter', 'date_from', 'date_to'])): ?>
<div class="bg-white shadow rounded-lg overflow-hidden mb-6">
    <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700">
        <h3 class="text-lg font-semibold text-white"><i class="fas fa-chart-pie mr-2"></i>Transaction Summary</h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
            <div class="text-center">
                <p class="text-sm text-gray-500">Total Debit</p>
                <p class="text-2xl font-bold text-red-600">$<?php echo e(number_format($totalDebit, 2)); ?></p>
            </div>
            <div class="text-center">
                <p class="text-sm text-gray-500">Total Credit</p>
                <p class="text-2xl font-bold text-green-600">$<?php echo e(number_format($totalCredit, 2)); ?></p>
            </div>
            <div class="text-center">
                <p class="text-sm text-gray-500">Balance</p>
                <p class="text-2xl font-bold <?php echo e($balance >= 0 ? 'text-red-600' : 'text-green-600'); ?>">$<?php echo e(number_format(abs($balance), 2)); ?></p>
                <p class="text-xs <?php echo e($balance >= 0 ? 'text-red-500' : 'text-green-500'); ?>"><?php echo e($balance >= 0 ? 'Amount Due' : 'In Credit'); ?></p>
            </div>
            <div class="text-center">
                <p class="text-sm text-gray-500">Transactions</p>
                <p class="text-2xl font-bold text-gray-900"><?php echo e(number_format($paginated->total())); ?></p>
            </div>
            <div class="text-center">
                <p class="text-sm text-gray-500">Filters Applied</p>
                <p class="text-sm font-medium text-gray-700 mt-2">
                    <?php echo e($selectedCustomer && $selectedCustomer != 'all' ? '1 Customer' : 'All Customers'); ?>

                    <?php echo e($typeFilter && $typeFilter != 'all' ? '/ ' . ucfirst($typeFilter) : ''); ?>

                </p>
            </div>
        </div>
    </div>
</div>

<div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700">
        <h3 class="text-lg font-semibold text-white"><i class="fas fa-list mr-2"></i>Transaction History</h3>
    </div>
    <div class="p-6">
        <div class="mb-4 text-right text-sm text-gray-600">
            <?php if($paginated->total() > 0): ?>
                Showing <?php echo e($paginated->firstItem()); ?>-<?php echo e($paginated->lastItem()); ?> of <?php echo e($paginated->total()); ?> transactions
            <?php endif; ?>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Reference #</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Description</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Debit</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Credit</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Balance</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $__empty_1 = true; $__currentLoopData = $paginated; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900"><?php echo e($t['date'] ? \Carbon\Carbon::parse($t['date'])->format('d/m/Y') : '-'); ?></td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo e($t['type_class']); ?>"><?php echo e($t['type_label']); ?></span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo e($t['reference']); ?></td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 max-w-[200px] truncate"><?php echo e($t['description']); ?></td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-right <?php echo e($t['debit'] > 0 ? 'text-red-600 font-medium' : 'text-gray-500'); ?>"><?php echo e($t['debit'] > 0 ? '$' . number_format($t['debit'], 2) : '-'); ?></td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-right <?php echo e($t['credit'] > 0 ? 'text-green-600 font-medium' : 'text-gray-500'); ?>"><?php echo e($t['credit'] > 0 ? '$' . number_format($t['credit'], 2) : '-'); ?></td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-right font-medium <?php echo e($t['running_balance'] >= 0 ? 'text-red-700' : 'text-green-700'); ?>">$<?php echo e(number_format(abs($t['running_balance']), 2)); ?></td>
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center gap-0.5">
                                    <?php if($t['url_gl']): ?>
                                        <a href="<?php echo e($t['url_gl']); ?>" class="p-1.5 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded transition" title="GL" target="_blank"><i class="fas fa-book text-xs"></i></a>
                                    <?php endif; ?>
                                    <?php if($t['url_edit']): ?>
                                        <a href="<?php echo e($t['url_edit']); ?>" class="p-1.5 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded transition" title="Edit"><i class="fas fa-pencil-alt text-xs"></i></a>
                                    <?php endif; ?>
                                    <?php if($t['url_copy']): ?>
                                        <a href="<?php echo e($t['url_copy']); ?>" class="p-1.5 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded transition" title="<?php echo e($t['copy_label'] ?? 'Copy'); ?>"><i class="fas fa-copy text-xs"></i></a>
                                    <?php endif; ?>
                                    <?php if($t['url_credit']): ?>
                                        <a href="<?php echo e($t['url_credit']); ?>" class="p-1.5 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded transition" title="<?php echo e($t['credit_label'] ?? 'Credit'); ?>"><i class="fas <?php echo e($t['credit_icon'] ?? 'fa-credit-card'); ?> text-xs"></i></a>
                                    <?php endif; ?>
                                    <?php if($t['url_view']): ?>
                                        <a href="<?php echo e($t['url_view']); ?>" class="p-1.5 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded transition" title="View" target="_blank"><i class="fas fa-eye text-xs"></i></a>
                                    <?php endif; ?>
                                    <?php if($t['url_print']): ?>
                                        <a href="<?php echo e($t['url_print']); ?>" class="p-1.5 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded transition" title="Print" target="_blank"><i class="fas fa-print text-xs"></i></a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center text-gray-500">
                                <i class="fas fa-exchange-alt text-5xl mb-3 text-gray-300"></i>
                                <p class="text-base font-medium text-gray-400">No transactions found</p>
                                <p class="text-sm mt-1">Select filters and click "View Transactions".</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($paginated->hasPages()): ?>
            <div class="mt-6">
                <?php echo e($paginated->appends(request()->query())->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-blue-500 rounded-md p-3"><i class="fas fa-file-invoice-dollar text-white"></i></div>
            <div class="ml-5"><p class="text-sm font-medium text-gray-500 truncate">Sales Invoices</p><p class="text-lg font-medium text-gray-900"><?php echo e($countInvoices); ?></p></div>
        </div>
    </div>
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-red-500 rounded-md p-3"><i class="fas fa-exclamation-triangle text-white"></i></div>
            <div class="ml-5"><p class="text-sm font-medium text-gray-500 truncate">Unsettled</p><p class="text-lg font-medium text-gray-900"><?php echo e($countUnsettled); ?></p></div>
        </div>
    </div>
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-green-500 rounded-md p-3"><i class="fas fa-money-check-alt text-white"></i></div>
            <div class="ml-5"><p class="text-sm font-medium text-gray-500 truncate">Payments</p><p class="text-lg font-medium text-gray-900"><?php echo e($countPayments); ?></p></div>
        </div>
    </div>
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-orange-500 rounded-md p-3"><i class="fas fa-receipt text-white"></i></div>
            <div class="ml-5"><p class="text-sm font-medium text-gray-500 truncate">Credit Notes</p><p class="text-lg font-medium text-gray-900"><?php echo e($countCredits); ?></p></div>
        </div>
    </div>
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-cyan-500 rounded-md p-3"><i class="fas fa-truck text-white"></i></div>
            <div class="ml-5"><p class="text-sm font-medium text-gray-500 truncate">Delivery Notes</p><p class="text-lg font-medium text-gray-900"><?php echo e($countDeliveries); ?></p></div>
        </div>
    </div>
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-gray-500 rounded-md p-3"><i class="fas fa-journal-whills text-white"></i></div>
            <div class="ml-5"><p class="text-sm font-medium text-gray-500 truncate">Journal Entries</p><p class="text-lg font-medium text-gray-900"><?php echo e($countJournal); ?></p></div>
        </div>
    </div>
</div>
<?php else: ?>
<div class="bg-white shadow rounded-lg p-12 text-center">
    <i class="fas fa-exchange-alt text-6xl mb-4 text-gray-300"></i>
    <h3 class="text-lg font-medium text-gray-500 mb-2">Select Filters to View Transactions</h3>
    <p class="text-gray-400">Choose a customer, transaction type, or date range above and click "View Transactions".</p>
</div>
<?php endif; ?>

<?php $__env->startPush('styles'); ?>
<style>
    .pagination { display: flex; justify-content: center; gap: 4px; flex-wrap: wrap; }
    .pagination .page-item { list-style: none; }
    .pagination .page-link { display: block; padding: 6px 12px; border: 1px solid #d1d5db; border-radius: 6px; color: #374151; font-size: 14px; text-decoration: none; transition: all 0.15s; }
    .pagination .page-link:hover { background-color: #f3f4f6; }
    .pagination .active .page-link { background-color: #4f46e5; border-color: #4f46e5; color: white; }
    .pagination .disabled .page-link { color: #9ca3af; pointer-events: none; background-color: #f9fafb; }
    .pagination svg { width: 16px; height: 16px; }
</style>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lupu\Desktop\laravel\fa-saas\resources\views/sales/inquiries/transactions.blade.php ENDPATH**/ ?>