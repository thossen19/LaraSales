<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupplierPayment extends Model
{
    protected $fillable = [
        'company_id', 'supplier_id', 'bank_account_id', 'payment_number', 'payment_date',
        'reference', 'amount', 'discount', 'bank_amount', 'bank_charge',
        'currency', 'exchange_rate', 'dimension_id', 'dimension2_id',
        'memo', 'status', 'created_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'bank_amount' => 'decimal:2',
        'bank_charge' => 'decimal:2',
        'exchange_rate' => 'decimal:6',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class, 'bank_account_id');
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(SupplierPaymentAllocation::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getUnallocatedAmountAttribute(): float
    {
        $allocated = $this->allocations()->sum('amount');
        return max(0, $this->amount - $allocated);
    }
}
