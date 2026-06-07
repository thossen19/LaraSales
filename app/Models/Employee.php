<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'employee_number',
        'first_name',
        'last_name',
        'email',
        'phone',
        'hire_date',
        'birth_date',
        'gender',
        'address',
        'city',
        'country',
        'postal_code',
        'department',
        'position',
        'employment_type',
        'salary',
        'hourly_rate',
        'bank_account',
        'tax_id',
        'social_security',
        'termination_date',
        'termination_reason',
        'is_active',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'birth_date' => 'date',
        'termination_date' => 'date',
        'salary' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function scopeByDepartment($query, $department)
    {
        return $query->where('department', $department);
    }

    public function scopeByEmploymentType($query, $type)
    {
        return $query->where('employment_type', $type);
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getAgeAttribute()
    {
        return $this->birth_date ? $this->birth_date->age : null;
    }

    public function getYearsOfServiceAttribute()
    {
        return $this->hire_date ? $this->hire_date->diffInYears(now()) : 0;
    }

    public function getMonthlySalaryAttribute()
    {
        return $this->salary / 12;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($employee) {
            $employee->employee_number = self::generateEmployeeNumber($employee->company_id);
        });
    }

    private static function generateEmployeeNumber($companyId)
    {
        $prefix = 'EMP-' . $companyId . '-';
        $lastEmployee = self::where('company_id', $companyId)
            ->where('employee_number', 'like', $prefix . '%')
            ->orderBy('employee_number', 'desc')
            ->first();

        if ($lastEmployee) {
            $lastNumber = (int) str_replace($prefix, '', $lastEmployee->employee_number);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }
}
