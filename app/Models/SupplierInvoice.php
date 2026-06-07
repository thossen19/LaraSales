<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupplierInvoice extends Model
{
    protected $fillable = [
        'type', 'company_id', 'supplier_id', 'invoice_number', 'reference', 'supp_reference',
        'invoice_date', 'due_date', 'location', 'delivery_address', 'currency',
        'exchange_rate', 'subtotal', 'tax_total', 'total_amount', 'alloc',
        'cash_account_id', 'dimension_id', 'dimension2_id', 'comments', 'status', 'created_by',
    ];

    protected $casts = [
        'type' => 'string',
        'invoice_date' => 'date',
        'due_date' => 'date',
        'exchange_rate' => 'decimal:6',
        'subtotal' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'alloc' => 'decimal:2',
        'dimension_id' => 'integer',
        'dimension2_id' => 'integer',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SupplierInvoiceItem::class);
    }

    public function cashAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class, 'cash_account_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function paymentAllocations(): HasMany
    {
        return $this->hasMany(SupplierPaymentAllocation::class, 'supplier_invoice_id');
    }

    public function getOutstandingAmountAttribute(): float
    {
        return max(0, $this->total_amount - $this->alloc);
    }
}
