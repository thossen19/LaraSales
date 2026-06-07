<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'order_date',
        'delivery_date',
        'customer_id',
        'customer_branch_id',
        'sales_person_id',
        'sales_type_id',
        'quotation_id',
        'payment',
        'location',
        'ship_via',
        'status',
        'subtotal',
        'tax_amount',
        'total_amount',
        'discount_amount',
        'paid_amount',
        'delivery_address',
        'customer_notes',
        'terms_and_conditions',
        'internal_notes',
    ];

    protected $casts = [
        'order_date' => 'date',
        'delivery_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function customerBranch(): BelongsTo
    {
        return $this->belongsTo(CustomerBranch::class);
    }

    public function salesPerson(): BelongsTo
    {
        return $this->belongsTo(SalesPerson::class);
    }

    public function salesType(): BelongsTo
    {
        return $this->belongsTo(SalesType::class);
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(SalesQuotation::class);
    }

    public function lineItems(): HasMany
    {
        return $this->hasMany(OrderLineItem::class, 'order_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function getFormattedTotalAttribute(): string
    {
        return '$' . number_format($this->total_amount, 2);
    }

    public function getBalanceAttribute(): float
    {
        return $this->total_amount - $this->paid_amount;
    }

    public function getFormattedBalanceAttribute(): string
    {
        return '$' . number_format($this->balance, 2);
    }

    public function getStatusBadgeAttribute(): string
    {
        $badges = [
            'pending' => '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>',
            'confirmed' => '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Confirmed</span>',
            'in_progress' => '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">In Progress</span>',
            'delivered' => '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Delivered</span>',
            'cancelled' => '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Cancelled</span>',
        ];

        return $badges[$this->status] ?? $badges['pending'];
    }
}
