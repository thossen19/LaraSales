<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BillOfMaterialsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'item_id' => 'required|exists:items,id',
            'version' => 'nullable|string|max:20',
            'status' => 'nullable|in:active,inactive,obsolete',
            'standard_cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
            'is_default' => 'nullable|boolean',
            'effective_date' => 'nullable|date',
            'items' => 'required|array|min:1',
            'items.*.component_item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.unit_of_measure' => 'required|string|max:20',
            'items.*.scrap_percentage' => 'nullable|numeric|min:0|max:100',
            'items.*.notes' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'item_id.required' => 'Item is required',
            'item_id.exists' => 'Selected item does not exist',
            'items.required' => 'At least one component is required',
            'items.*.component_item_id.required' => 'Component item is required',
            'items.*.component_item_id.exists' => 'Selected component does not exist',
            'items.*.quantity.required' => 'Quantity is required',
            'items.*.quantity.min' => 'Quantity must be at least 0',
            'items.*.unit_of_measure.required' => 'Unit of measure is required',
            'items.*.scrap_percentage.max' => 'Scrap percentage cannot exceed 100',
        ];
    }
}
