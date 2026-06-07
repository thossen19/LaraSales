<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => 'required|string|max:50|unique:items,code,' . $this->item,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'category' => 'nullable|string|max:100',
            'unit_of_measure' => 'required|string|max:20',
            'purchase_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'weight' => 'nullable|numeric|min:0',
            'volume' => 'nullable|numeric|min:0',
            'barcode' => 'nullable|string|max:50|unique:items,barcode,' . $this->item,
            'reorder_level' => 'nullable|integer|min:0',
            'reorder_quantity' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'is_stock_item' => 'nullable|boolean',
            'is_service' => 'nullable|boolean',
            'sales_account' => 'nullable|string|max:50',
            'purchase_account' => 'nullable|string|max:50',
            'inventory_account' => 'nullable|string|max:50',
            'cogs_account' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Item code is required',
            'code.unique' => 'Item code is already taken',
            'name.required' => 'Item name is required',
            'unit_of_measure.required' => 'Unit of measure is required',
            'purchase_price.required' => 'Purchase price is required',
            'purchase_price.min' => 'Purchase price must be at least 0',
            'sale_price.required' => 'Sale price is required',
            'sale_price.min' => 'Sale price must be at least 0',
            'barcode.unique' => 'Barcode is already taken',
            'image.image' => 'File must be an image',
            'image.mimes' => 'Image must be jpeg, png, jpg, or gif',
            'image.max' => 'Image size cannot exceed 2MB',
        ];
    }
}
