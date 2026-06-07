<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'employee_id',
        'payroll_number',
        'pay_period_start',
        'pay_period_end',
        'payment_date',
        'gross_salary',
        'overtime_hours',
        'overtime_pay',
        'bonus',
        'allowances',
        'deductions',
        'tax_withheld',
        'other_deductions',
        'net_salary',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'pay_period_start' => 'date',
        'pay_period_end' => 'date',
        'payment_date' => 'date',
        'gross_salary' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'overtime_pay' => 'decimal:2',
        'bonus' => 'decimal:2',
        'allowances' => 'decimal:2',
        'deductions' => 'decimal:2',
        'tax_withheld' => 'decimal:2',
        'other_deductions' => 'decimal:2',
        'net_salary' => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeProcessed($query)
    {
        return $query->where('status', 'processed');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payroll) {
            $payroll->payroll_number = self::generatePayrollNumber($payroll->company_id);
            
            // Calculate net salary
            $gross = $payroll->gross_salary + $payroll->overtime_pay + $payroll->bonus + $payroll->allowances;
            $totalDeductions = $payroll->deductions + $payroll->tax_withheld + $payroll->other_deductions;
            $payroll->net_salary = $gross - $totalDeductions;
        });
    }

    private static function generatePayrollNumber($companyId)
    {
        $prefix = 'PAY-' . $companyId . '-';
        $lastPayroll = self::where('company_id', $companyId)
            ->where('payroll_number', 'like', $prefix . '%')
            ->orderBy('payroll_number', 'desc')
            ->first();

        if ($lastPayroll) {
            $lastNumber = (int) str_replace($prefix, '', $lastPayroll->payroll_number);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }
}
