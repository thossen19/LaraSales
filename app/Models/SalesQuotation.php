<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesQuotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_number',
        'quotation_date',
        'expiry_date',
        'customer_id',
        'customer_branch_id',
        'sales_person_id',
        'sales_type_id',
        'status',
        'subtotal',
        'tax_amount',
        'total_amount',
        'discount_amount',
        'freight_cost',
        'deliver_to',
        'delivery_address',
        'phone',
        'cust_ref',
        'location',
        'payment',
        'ship_via',
        'reference',
        'customer_notes',
        'terms_and_conditions',
        'internal_notes',
    ];

    protected $casts = [
        'quotation_date' => 'date',
        'expiry_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function salesPerson(): BelongsTo
    {
        return $this->belongsTo(SalesPerson::class);
    }

    public function salesType(): BelongsTo
    {
        return $this->belongsTo(SalesType::class);
    }

    public function lineItems(): HasMany
    {
        return $this->hasMany(QuotationLineItem::class, 'quotation_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(SalesOrder::class, 'quotation_id');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    public function getFormattedTotalAttribute(): string
    {
        return '$' . number_format($this->total_amount, 2);
    }

    public function getStatusBadgeAttribute(): string
    {
        $badges = [
            'draft' => '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Draft</span>',
            'sent' => '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Sent</span>',
            'accepted' => '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Accepted</span>',
            'rejected' => '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Rejected</span>',
            'expired' => '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Expired</span>',
        ];

        return $badges[$this->status] ?? $badges['draft'];
    }
}
