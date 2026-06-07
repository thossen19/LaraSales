<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductionOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'item_id' => 'required|exists:items,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'order_date' => 'required|date',
            'start_date' => 'nullable|date|after_or_equal:order_date',
            'finish_date' => 'nullable|date|after_or_equal:start_date',
            'quantity_planned' => 'required|integer|min:1',
            'standard_cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'item_id.required' => 'Item is required',
            'item_id.exists' => 'Selected item does not exist',
            'warehouse_id.required' => 'Warehouse is required',
            'warehouse_id.exists' => 'Selected warehouse does not exist',
            'order_date.required' => 'Order date is required',
            'order_date.date' => 'Order date must be a valid date',
            'start_date.after_or_equal' => 'Start date must be after or equal to order date',
            'finish_date.after_or_equal' => 'Finish date must be after or equal to start date',
            'quantity_planned.required' => 'Planned quantity is required',
            'quantity_planned.min' => 'Planned quantity must be at least 1',
            'standard_cost.min' => 'Standard cost must be at least 0',
        ];
    }
}
