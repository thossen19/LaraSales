<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuotationLineItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_id',
        'item_code',
        'description',
        'quantity',
        'unit_price',
        'tax_rate',
        'tax_amount',
        'line_total',
        'discount_percentage',
        'discount_amount',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'line_total' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(SalesQuotation::class, 'quotation_id');
    }

    public function getFormattedUnitPriceAttribute(): string
    {
        return '$' . number_format($this->unit_price, 2);
    }

    public function getFormattedLineTotalAttribute(): string
    {
        return '$' . number_format($this->line_total, 2);
    }

    public function getFormattedTaxRateAttribute(): string
    {
        return number_format($this->tax_rate, 2) . '%';
    }

    public function getFormattedDiscountAttribute(): string
    {
        return number_format($this->discount_percentage, 2) . '%';
    }
}
