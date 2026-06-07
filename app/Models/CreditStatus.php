<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CreditStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'status_name',
        'status_code',
        'description',
        'credit_limit_type',
        'credit_limit',
        'grace_period',
        'interest_rate',
        'late_fee',
        'allow_overdraft',
        'dissallow_invoices',
        'status',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'late_fee' => 'decimal:2',
        'allow_overdraft' => 'boolean',
        'dissallow_invoices' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function getFormattedCreditLimitAttribute(): string
    {
        return '$' . number_format($this->credit_limit, 2);
    }

    public function getFormattedInterestRateAttribute(): string
    {
        return number_format($this->interest_rate, 2) . '%';
    }

    public function getFormattedLateFeeAttribute(): string
    {
        return number_format($this->late_fee, 2) . '%';
    }
}
