<div id="itemSearchModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeItemSearch()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
            <div class="bg-white px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Item Search</h3>
                <button type="button" onclick="closeItemSearch()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="px-6 py-4">
                <div class="relative mb-4">
                    <input type="text" id="itemSearchInput" placeholder="Search item code or description..." class="w-full border border-gray-300 rounded-md pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
                <div class="max-h-80 overflow-y-auto border border-gray-200 rounded-md">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 sticky top-0">
                            <tr>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Code</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Description</th>
                                <th class="px-4 py-2.5 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Unit</th>
                                <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Cost Price</th>
                            </tr>
                        </thead>
                        <tbody id="itemSearchResults" class="bg-white divide-y divide-gray-200">
                            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="item-row hover:bg-indigo-50 cursor-pointer transition" data-code="<?php echo e($item->code); ?>" data-name="<?php echo e($item->name ?? ''); ?>" data-unit="<?php echo e($item->unit_of_measure ?? ''); ?>" data-price="<?php echo e($item->cost_price ?? 0); ?>" onclick="selectItem(this)">
                                <td class="px-4 py-2.5 text-sm font-medium text-gray-900"><?php echo e($item->code); ?></td>
                                <td class="px-4 py-2.5 text-sm text-gray-600"><?php echo e($item->name ?? ''); ?></td>
                                <td class="px-4 py-2.5 text-sm text-gray-600 text-center"><?php echo e($item->unit_of_measure ?? ''); ?></td>
                                <td class="px-4 py-2.5 text-sm text-gray-600 text-right"><?php echo e(number_format($item->cost_price ?? 0, 4)); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <div class="mt-2 text-xs text-gray-500 text-right">Showing <span id="itemCount"><?php echo e(count($items)); ?></span> items</div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
const items = <?php echo json_encode($items, 15, 512) ?>;

function openItemSearch() {
    document.getElementById('itemSearchModal').classList.remove('hidden');
    document.getElementById('itemSearchInput').value = '';
    document.getElementById('itemSearchInput').focus();
    filterItems();
}

function closeItemSearch() {
    document.getElementById('itemSearchModal').classList.add('hidden');
}

function filterItems() {
    const q = document.getElementById('itemSearchInput').value.toLowerCase();
    const rows = document.querySelectorAll('#itemSearchResults .item-row');
    let count = 0;
    rows.forEach(row => {
        const code = row.getAttribute('data-code').toLowerCase();
        const name = row.getAttribute('data-name').toLowerCase();
        if (code.includes(q) || name.includes(q)) {
            row.classList.remove('hidden');
            count++;
        } else {
            row.classList.add('hidden');
        }
    });
    document.getElementById('itemCount').textContent = count;
}

function selectItem(row) {
    const code = row.getAttribute('data-code');
    const name = row.getAttribute('data-name');
    const unit = row.getAttribute('data-unit');
    const price = row.getAttribute('data-price');
    document.querySelector('input[name="stock_id"]').value = code;
    const descField = document.querySelector('input[name="item_description"]');
    if (descField) {
        descField.value = name;
    }
    const priceField = document.querySelector('input[name="price"]');
    if (priceField) {
        priceField.value = parseFloat(price).toFixed(4);
    }
    closeItemSearch();
    document.querySelector('input[name="stock_id"]').form.submit();
}

document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('itemSearchInput');
    if (input) {
        input.addEventListener('input', filterItems);
    }
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeItemSearch();
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\Users\Lupu\Desktop\laravel\fa-saas\resources\views/components/item-search-modal.blade.php ENDPATH**/ ?>