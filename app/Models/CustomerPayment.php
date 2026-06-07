<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'bank_account_id',
        'payment_number',
        'payment_date',
        'reference',
        'amount',
        'discount',
        'bank_amount',
        'bank_charge',
        'currency',
        'exchange_rate',
        'memo',
        'status',
        'created_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'bank_amount' => 'decimal:2',
        'bank_charge' => 'decimal:2',
        'exchange_rate' => 'decimal:6',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(CustomerPaymentAllocation::class, 'customer_payment_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
