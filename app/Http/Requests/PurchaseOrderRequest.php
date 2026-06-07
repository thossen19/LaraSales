<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'expected_date' => 'nullable|date|after_or_equal:order_date',
            'payment_terms' => 'nullable|string|max:255',
            'delivery_address' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
            'shipping_amount' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.warehouse_id' => 'required|exists:warehouses,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'nullable|numeric|min:0',
            'items.*.discount_percentage' => 'nullable|numeric|min:0|max:100',
            'items.*.tax_percentage' => 'nullable|numeric|min:0|max:100',
            'items.*.description' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'supplier_id.required' => 'Supplier is required',
            'supplier_id.exists' => 'Selected supplier does not exist',
            'order_date.required' => 'Order date is required',
            'order_date.date' => 'Order date must be a valid date',
            'expected_date.after_or_equal' => 'Expected date must be after or equal to order date',
            'items.required' => 'At least one item is required',
            'items.*.item_id.required' => 'Item is required for each line',
            'items.*.item_id.exists' => 'Selected item does not exist',
            'items.*.warehouse_id.required' => 'Warehouse is required for each line',
            'items.*.warehouse_id.exists' => 'Selected warehouse does not exist',
            'items.*.quantity.required' => 'Quantity is required for each line',
            'items.*.quantity.min' => 'Quantity must be at least 1',
            'items.*.unit_price.min' => 'Unit price must be at least 0',
            'items.*.discount_percentage.max' => 'Discount percentage cannot exceed 100',
            'items.*.tax_percentage.max' => 'Tax percentage cannot exceed 100',
        ];
    }
}
