<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'fax' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'tax_id' => 'nullable|string|max:50',
            'supplier_type' => 'required|in:individual,company',
            'credit_limit' => 'nullable|numeric|min:0',
            'payment_terms' => 'nullable|string|max:255',
            'purchase_account' => 'nullable|string|max:50',
            'payable_account' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Supplier name is required',
            'name.max' => 'Supplier name cannot exceed 255 characters',
            'email.email' => 'Email must be a valid email address',
            'supplier_type.required' => 'Supplier type is required',
            'supplier_type.in' => 'Supplier type must be individual or company',
            'credit_limit.min' => 'Credit limit must be at least 0',
        ];
    }
}
