<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PayrollResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'payroll_number' => $this->payroll_number,
            'pay_period_start' => $this->pay_period_start,
            'pay_period_end' => $this->pay_period_end,
            'payment_date' => $this->payment_date,
            'gross_salary' => $this->gross_salary,
            'overtime_hours' => $this->overtime_hours,
            'overtime_pay' => $this->overtime_pay,
            'bonus' => $this->bonus,
            'allowances' => $this->allowances,
            'deductions' => $this->deductions,
            'tax_withheld' => $this->tax_withheld,
            'other_deductions' => $this->other_deductions,
            'net_salary' => $this->net_salary,
            'status' => $this->status,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'employee' => [
                'id' => $this->employee->id,
                'employee_number' => $this->employee->employee_number,
                'full_name' => $this->employee->full_name,
                'position' => $this->employee->position,
                'department' => $this->employee->department,
            ],
            'created_by' => [
                'id' => $this->createdBy->id,
                'name' => $this->createdBy->name,
                'email' => $this->createdBy->email,
            ],
        ];
    }
}
