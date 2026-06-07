<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee_number' => $this->employee_number,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'hire_date' => $this->hire_date,
            'birth_date' => $this->birth_date,
            'age' => $this->age,
            'gender' => $this->gender,
            'address' => $this->address,
            'city' => $this->city,
            'country' => $this->country,
            'postal_code' => $this->postal_code,
            'department' => $this->department,
            'position' => $this->position,
            'employment_type' => $this->employment_type,
            'salary' => $this->salary,
            'monthly_salary' => $this->monthly_salary,
            'hourly_rate' => $this->hourly_rate,
            'bank_account' => $this->bank_account,
            'tax_id' => $this->tax_id,
            'social_security' => $this->social_security,
            'termination_date' => $this->termination_date,
            'termination_reason' => $this->termination_reason,
            'years_of_service' => $this->years_of_service,
            'is_active' => $this->is_active,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => [
                'id' => $this->createdBy->id,
                'name' => $this->createdBy->name,
                'email' => $this->createdBy->email,
            ],
        ];
    }
}
