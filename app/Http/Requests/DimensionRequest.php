<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DimensionRequest extends FormRequest
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
            'type' => 'required|in:department,project,cost_center,location',
            'parent_id' => 'nullable|exists:dimensions,id',
            'manager' => 'nullable|string|max:255',
            'budget_code' => 'nullable|string|max:50',
            'budget_amount' => 'nullable|numeric|min:0',
            'is_active' => 'nullable|boolean',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Dimension name is required',
            'name.max' => 'Dimension name cannot exceed 255 characters',
            'type.required' => 'Dimension type is required',
            'type.in' => 'Dimension type must be department, project, cost_center, or location',
            'parent_id.exists' => 'Selected parent dimension does not exist',
            'budget_amount.min' => 'Budget amount must be at least 0',
        ];
    }
}
