<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:employees,email,' . $this->employee,
            'phone' => 'nullable|string|max:20',
            'hire_date' => 'required|date',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'department' => 'nullable|string|max:100',
            'position' => 'required|string|max:255',
            'employment_type' => 'required|in:full_time,part_time,contract,intern',
            'salary' => 'nullable|numeric|min:0',
            'hourly_rate' => 'nullable|numeric|min:0',
            'bank_account' => 'nullable|string|max:50',
            'tax_id' => 'nullable|string|max:50',
            'social_security' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'First name is required',
            'last_name.required' => 'Last name is required',
            'email.required' => 'Email is required',
            'email.email' => 'Email must be a valid email address',
            'email.unique' => 'Email is already taken',
            'hire_date.required' => 'Hire date is required',
            'hire_date.date' => 'Hire date must be a valid date',
            'position.required' => 'Position is required',
            'employment_type.required' => 'Employment type is required',
            'employment_type.in' => 'Employment type must be full_time, part_time, contract, or intern',
            'salary.min' => 'Salary must be at least 0',
            'hourly_rate.min' => 'Hourly rate must be at least 0',
        ];
    }
}
