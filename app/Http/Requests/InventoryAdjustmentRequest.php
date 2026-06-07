<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InventoryAdjustmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.warehouse_id' => 'required|exists:warehouses,id',
            'items.*.new_quantity' => 'required|integer|min:0',
            'items.*.unit_cost' => 'nullable|numeric|min:0',
            'items.*.notes' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'At least one item adjustment is required',
            'items.*.item_id.required' => 'Item ID is required for each adjustment',
            'items.*.item_id.exists' => 'Selected item does not exist',
            'items.*.warehouse_id.required' => 'Warehouse ID is required for each adjustment',
            'items.*.warehouse_id.exists' => 'Selected warehouse does not exist',
            'items.*.new_quantity.required' => 'New quantity is required for each adjustment',
            'items.*.new_quantity.min' => 'New quantity must be at least 0',
            'items.*.unit_cost.min' => 'Unit cost must be at least 0',
        ];
    }
}
