<!-- Line Items Section -->
<div class="mb-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-medium text-gray-900">Line Items</h3>
        <button type="button" onclick="addLineItem()" class="px-3 py-1 bg-green-600 text-white text-sm rounded-md hover:bg-green-700">
            <i class="fas fa-plus mr-1"></i>Add Item
        </button>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200" id="lineItemsTable">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tax Rate</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Discount</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Line Total</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200" id="lineItemsBody">
                @forelse($quotation->lineItems ?? [] as $index => $lineItem)
                    <tr id="lineItem_{{ $index }}">
                        <td class="px-4 py-3">
                            <select name="line_items[{{ $index }}][item_id]" class="w-full border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" onchange="updateLineItem({{ $index }})">
                                <option value="">Select Item</option>
                                @foreach($items as $item)
                                    <option value="{{ $item->id }}" {{ $lineItem->item_code == $item->code ? 'selected' : '' }} data-price="{{ $item->sale_price }}" data-code="{{ $item->code }}">{{ $item->code }} - {{ $item->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="px-4 py-3">
                            <input type="text" name="line_items[{{ $index }}][description]" value="{{ $lineItem->description }}" class="w-full border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </td>
                        <td class="px-4 py-3">
                            <input type="number" name="line_items[{{ $index }}][quantity]" value="{{ $lineItem->quantity }}" step="0.01" min="0" class="w-20 border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" onchange="calculateLineTotal({{ $index }})">
                        </td>
                        <td class="px-4 py-3">
                            <input type="number" name="line_items[{{ $index }}][unit_price]" value="{{ $lineItem->unit_price }}" step="0.01" min="0" class="w-24 border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" onchange="calculateLineTotal({{ $index }})">
                        </td>
                        <td class="px-4 py-3">
                            <input type="number" name="line_items[{{ $index }}][tax_rate]" value="{{ $lineItem->tax_rate }}" step="0.01" min="0" class="w-20 border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" onchange="calculateLineTotal({{ $index }})">
                        </td>
                        <td class="px-4 py-3">
                            <input type="number" name="line_items[{{ $index }}][discount_percentage]" value="{{ $lineItem->discount_percentage }}" step="0.01" min="0" class="w-20 border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" onchange="calculateLineTotal({{ $index }})">
                        </td>
                        <td class="px-4 py-3">
                            <input type="number" name="line_items[{{ $index }}][line_total]" value="{{ $lineItem->line_total }}" step="0.01" readonly class="w-24 border border-gray-300 rounded-md px-2 py-1 text-sm bg-gray-50">
                        </td>
                        <td class="px-4 py-3">
                            <button type="button" onclick="removeLineItem({{ $index }})" class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr id="lineItem_0">
                        <td class="px-4 py-3">
                            <select name="line_items[0][item_id]" class="w-full border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" onchange="updateLineItem(0)">
                                <option value="">Select Item</option>
                                @foreach($items as $item)
                                    <option value="{{ $item->id }}" data-price="{{ $item->sale_price }}" data-code="{{ $item->code }}">{{ $item->code }} - {{ $item->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="px-4 py-3">
                            <input type="text" name="line_items[0][description]" class="w-full border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </td>
                        <td class="px-4 py-3">
                            <input type="number" name="line_items[0][quantity]" value="1" step="0.01" min="0" class="w-20 border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" onchange="calculateLineTotal(0)">
                        </td>
                        <td class="px-4 py-3">
                            <input type="number" name="line_items[0][unit_price]" value="0" step="0.01" min="0" class="w-24 border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" onchange="calculateLineTotal(0)">
                        </td>
                        <td class="px-4 py-3">
                            <input type="number" name="line_items[0][tax_rate]" value="0" step="0.01" min="0" class="w-20 border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" onchange="calculateLineTotal(0)">
                        </td>
                        <td class="px-4 py-3">
                            <input type="number" name="line_items[0][discount_percentage]" value="0" step="0.01" min="0" class="w-20 border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" onchange="calculateLineTotal(0)">
                        </td>
                        <td class="px-4 py-3">
                            <input type="number" name="line_items[0][line_total]" value="0" step="0.01" readonly class="w-24 border border-gray-300 rounded-md px-2 py-1 text-sm bg-gray-50">
                        </td>
                        <td class="px-4 py-3">
                            <button type="button" onclick="removeLineItem(0)" class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Line Items Summary -->
    <div class="mt-4 p-4 bg-gray-50 rounded-lg">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Subtotal</label>
                <div class="text-lg font-semibold" id="subtotal">$0.00</div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Total Tax</label>
                <div class="text-lg font-semibold" id="totalTax">$0.00</div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Total Amount</label>
                <div class="text-lg font-bold text-green-600" id="totalAmount">$0.00</div>
            </div>
        </div>
    </div>
</div>

<input type="hidden" name="line_items_count" id="lineItemsCount" value="{{ max(1, ($quotation->lineItems ?? collect())->count()) }}">

<script>
let lineItemCount = {{ max(1, ($quotation->lineItems ?? collect())->count()) }};

function addLineItem() {
    const tbody = document.getElementById('lineItemsBody');
    const rowCount = tbody.children.length;
    const newRow = document.createElement('tr');
    newRow.id = 'lineItem_' + rowCount;
    
    newRow.innerHTML = `
        <td class="px-4 py-3">
            <select name="line_items[${rowCount}][item_id]" class="w-full border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" onchange="updateLineItem(${rowCount})">
                <option value="">Select Item</option>
                @foreach($items as $item)
                    <option value="{{ $item->id }}" data-price="{{ $item->sale_price }}" data-code="{{ $item->code }}">{{ $item->code }} - {{ $item->name }}</option>
                @endforeach
            </select>
        </td>
        <td class="px-4 py-3">
            <input type="text" name="line_items[${rowCount}][description]" class="w-full border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </td>
        <td class="px-4 py-3">
            <input type="number" name="line_items[${rowCount}][quantity]" value="1" step="0.01" min="0" class="w-20 border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" onchange="calculateLineTotal(${rowCount})">
        </td>
        <td class="px-4 py-3">
            <input type="number" name="line_items[${rowCount}][unit_price]" value="0" step="0.01" min="0" class="w-24 border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" onchange="calculateLineTotal(${rowCount})">
        </td>
        <td class="px-4 py-3">
            <input type="number" name="line_items[${rowCount}][tax_rate]" value="0" step="0.01" min="0" class="w-20 border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" onchange="calculateLineTotal(${rowCount})">
        </td>
        <td class="px-4 py-3">
            <input type="number" name="line_items[${rowCount}][discount_percentage]" value="0" step="0.01" min="0" class="w-20 border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" onchange="calculateLineTotal(${rowCount})">
        </td>
        <td class="px-4 py-3">
            <input type="number" name="line_items[${rowCount}][line_total]" value="0" step="0.01" readonly class="w-24 border border-gray-300 rounded-md px-2 py-1 text-sm bg-gray-50">
        </td>
        <td class="px-4 py-3">
            <button type="button" onclick="removeLineItem(${rowCount})" class="text-red-600 hover:text-red-900">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;
    
    tbody.appendChild(newRow);
    lineItemCount++;
    document.getElementById('lineItemsCount').value = lineItemCount;
}

function removeLineItem(index) {
    const row = document.getElementById('lineItem_' + index);
    if (row) {
        row.remove();
        calculateTotals();
    }
}

function updateLineItem(index) {
    const select = document.querySelector(`select[name="line_items[${index}][item_id]"]`);
    const selectedOption = select.options[select.selectedIndex];
    const price = selectedOption.getAttribute('data-price');
    const code = selectedOption.getAttribute('data-code');
    
    if (price) {
        document.querySelector(`input[name="line_items[${index}][unit_price]"]`).value = price;
    }
    
    if (code) {
        const descriptionInput = document.querySelector(`input[name="line_items[${index}][description]"]`);
        if (!descriptionInput.value) {
            descriptionInput.value = selectedOption.text.split(' - ').slice(1).join(' - ');
        }
    }
    
    calculateLineTotal(index);
}

function calculateLineTotal(index) {
    const quantity = parseFloat(document.querySelector(`input[name="line_items[${index}][quantity]"]`).value) || 0;
    const unitPrice = parseFloat(document.querySelector(`input[name="line_items[${index}][unit_price]"]`).value) || 0;
    const taxRate = parseFloat(document.querySelector(`input[name="line_items[${index}][tax_rate]"]`).value) || 0;
    const discountPercentage = parseFloat(document.querySelector(`input[name="line_items[${index}][discount_percentage]"]`).value) || 0;
    
    const subtotal = quantity * unitPrice;
    const discountAmount = subtotal * (discountPercentage / 100);
    const afterDiscount = subtotal - discountAmount;
    const taxAmount = afterDiscount * (taxRate / 100);
    const lineTotal = afterDiscount + taxAmount;
    
    document.querySelector(`input[name="line_items[${index}][line_total]"]`).value = lineTotal.toFixed(2);
    
    calculateTotals();
}

function calculateTotals() {
    let subtotal = 0;
    let totalTax = 0;
    
    const rows = document.querySelectorAll('#lineItemsBody tr');
    rows.forEach(row => {
        const lineTotalInput = row.querySelector('input[name*="[line_total]"]');
        if (lineTotalInput && lineTotalInput.value) {
            const quantity = parseFloat(row.querySelector('input[name*="[quantity]"]').value) || 0;
            const unitPrice = parseFloat(row.querySelector('input[name*="[unit_price]"]').value) || 0;
            const taxRate = parseFloat(row.querySelector('input[name*="[tax_rate]"]').value) || 0;
            const discountPercentage = parseFloat(row.querySelector('input[name*="[discount_percentage]"]').value) || 0;
            
            const lineSubtotal = quantity * unitPrice;
            const discountAmount = lineSubtotal * (discountPercentage / 100);
            const afterDiscount = lineSubtotal - discountAmount;
            const taxAmount = afterDiscount * (taxRate / 100);
            
            subtotal += afterDiscount;
            totalTax += taxAmount;
        }
    });
    
    const totalAmount = subtotal + totalTax;
    
    document.getElementById('subtotal').textContent = '$' + subtotal.toFixed(2);
    document.getElementById('totalTax').textContent = '$' + totalTax.toFixed(2);
    document.getElementById('totalAmount').textContent = '$' + totalAmount.toFixed(2);
    
    // Update the hidden total amount field
    const totalAmountField = document.querySelector('input[name="total_amount"]');
    if (totalAmountField) {
        totalAmountField.value = totalAmount.toFixed(2);
    }
}

// Initialize calculations on page load
document.addEventListener('DOMContentLoaded', function() {
    calculateTotals();
});
</script>
