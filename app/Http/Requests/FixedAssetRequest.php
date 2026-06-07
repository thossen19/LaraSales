<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FixedAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'category' => 'required|string|max:100',
            'location' => 'nullable|string|max:255',
            'purchase_date' => 'required|date',
            'purchase_cost' => 'required|numeric|min:0',
            'depreciation_method' => 'required|in:straight_line,declining_balance,units_of_production',
            'useful_life_years' => 'required|integer|min:1|max:50',
            'salvage_value' => 'nullable|numeric|min:0',
            'depreciation_start_date' => 'nullable|date|after_or_equal:purchase_date',
            'status' => 'nullable|in:active,disposed,fully_depreciated',
            'responsible_person' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Asset name is required',
            'name.max' => 'Asset name cannot exceed 255 characters',
            'category.required' => 'Asset category is required',
            'purchase_date.required' => 'Purchase date is required',
            'purchase_date.date' => 'Purchase date must be a valid date',
            'purchase_cost.required' => 'Purchase cost is required',
            'purchase_cost.min' => 'Purchase cost must be at least 0',
            'depreciation_method.required' => 'Depreciation method is required',
            'depreciation_method.in' => 'Invalid depreciation method',
            'useful_life_years.required' => 'Useful life is required',
            'useful_life_years.min' => 'Useful life must be at least 1 year',
            'useful_life_years.max' => 'Useful life cannot exceed 50 years',
            'salvage_value.min' => 'Salvage value must be at least 0',
            'depreciation_start_date.after_or_equal' => 'Depreciation start date must be after or equal to purchase date',
        ];
    }
}
