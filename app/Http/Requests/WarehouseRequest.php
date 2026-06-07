<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WarehouseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'contact_person' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'is_default' => 'nullable|boolean',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Warehouse name is required',
            'name.max' => 'Warehouse name cannot exceed 255 characters',
            'address.max' => 'Address cannot exceed 500 characters',
            'city.max' => 'City cannot exceed 100 characters',
            'country.max' => 'Country cannot exceed 100 characters',
            'postal_code.max' => 'Postal code cannot exceed 20 characters',
            'phone.max' => 'Phone cannot exceed 20 characters',
            'contact_person.max' => 'Contact person cannot exceed 255 characters',
            'notes.max' => 'Notes cannot exceed 1000 characters',
        ];
    }
}
