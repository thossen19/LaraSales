<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // User Information
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',

            // Company Information
            'company_name' => 'required|string|max:255',
            'company_email' => 'required|email|unique:companies,email',
            'company_phone' => 'nullable|string|max:20',
            'company_address' => 'nullable|string|max:500',
            'company_city' => 'nullable|string|max:100',
            'company_country' => 'nullable|string|max:100',
            'company_tax_id' => 'nullable|string|max:50',
            'company_registration_number' => 'nullable|string|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required',
            'email.required' => 'Email is required',
            'email.email' => 'Email must be a valid email address',
            'email.unique' => 'Email is already taken',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 8 characters',
            'password.confirmed' => 'Password confirmation does not match',

            'company_name.required' => 'Company name is required',
            'company_email.required' => 'Company email is required',
            'company_email.email' => 'Company email must be a valid email address',
            'company_email.unique' => 'Company email is already taken',
        ];
    }
}
