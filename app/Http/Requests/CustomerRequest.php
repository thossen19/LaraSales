<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
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
            'customer_type' => 'required|in:individual,company',
            'credit_limit' => 'nullable|numeric|min:0',
            'payment_terms' => 'nullable|string|max:255',
            'sales_account' => 'nullable|string|max:50',
            'receivable_account' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Customer name is required',
            'name.max' => 'Customer name cannot exceed 255 characters',
            'email.email' => 'Email must be a valid email address',
            'customer_type.required' => 'Customer type is required',
            'customer_type.in' => 'Customer type must be individual or company',
            'credit_limit.min' => 'Credit limit must be at least 0',
        ];
    }
}
